<?php
declare(strict_types=1);

namespace App\Core;

/**
 * AutoRouter class for handling HTTP routes with middleware support.
 * Inspired by Express.js, supports dynamic parameters and middleware chains.
 */
class AutoRouter
{
    /** @var array */
    protected $routes = [];

    /** @var array */
    protected $middleware = [];

    /** @var string */
    protected $prefix = '';

    /** @var array */
    protected $groupMiddleware = [];

    /**
     * Add a route with optional middleware.
     *
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $path Route path with optional {param} placeholders
     * @param callable|string $handler Handler function or Controller@method string
     * @param array<callable|string> $middleware Array of middleware callables or class names
     */
    public function add(string $method, string $path, $handler, array $middleware = []): void
    {
        $method = strtoupper($method);
        $fullPath = $this->prefix . $path;
        $route = $this->compileRoute($fullPath);
        $allMiddleware = array_merge($this->groupMiddleware, $middleware);
        $this->routes[$method][] = [
            'path' => $fullPath,
            'pattern' => $route['pattern'],
            'params' => $route['params'],
            'handler' => $handler,
            'middleware' => $allMiddleware,
        ];
    }

    /**
     * Compile route path into regex pattern and extract param names.
     *
     * @param string $path
     * @return array
     */
    protected function compileRoute(string $path): array
    {
        $params = [];
        $regex = preg_replace_callback('#\{([^}]+)\}#', function ($matches) use (&$params) {
            $params[] = $matches[1];
            // Support wildcard parameter with * suffix (e.g., {path*})
            if (substr($matches[1], -1) === '*') {
                return '(.+)';  // Match any character including slashes
            }
            return '([^/]+)';  // Match any character except slash
        }, $path);
        $pattern = '#^' . $regex . '$#';
        return ['pattern' => $pattern, 'params' => $params];
    }

    /**
     * Add global or path-specific middleware.
     *
     * @param string|callable $pathOrMiddleware Path or middleware
     * @param callable|string|null $middleware
     */
    public function use($pathOrMiddleware, $middleware = null): void
    {
        if (is_callable($pathOrMiddleware)) {
            // Global middleware
            $this->middleware[] = ['path' => null, 'middleware' => $pathOrMiddleware];
        } else {
            // Path-specific
            $this->middleware[] = ['path' => $pathOrMiddleware, 'middleware' => $middleware];
        }
    }

    /**
     * Create a route group with prefix and middleware.
     *
     * @param string $prefix
     * @param callable $callback
     * @param array $middleware
     */
    public function group(string $prefix, callable $callback, array $middleware = []): void
    {
        $previousPrefix = $this->prefix;
        $previousGroupMiddleware = $this->groupMiddleware;

        $this->prefix = $previousPrefix . $prefix;
        $this->groupMiddleware = array_merge($previousGroupMiddleware, $middleware);

        $callback($this);

        $this->prefix = $previousPrefix;
        $this->groupMiddleware = $previousGroupMiddleware;
    }

    /**
     * Dispatch the request to the appropriate handler.
     *
     * @param string|null $method Override HTTP method
     * @param string|null $uri Override URI
     * @throws \Exception If handler is invalid
     */
    public function dispatch(?string $method = null, ?string $uri = null): void
    {
        $req = new Request();
        $res = new Response();

        if ($method === null) {
            $method = $req->method;
        }
        if ($uri === null) {
            $uri = $req->path;
        }

        $method = strtoupper($method);
        $candidates = $this->routes[$method] ?? [];

        foreach ($candidates as $route) {
            if (preg_match($route['pattern'], $uri, $matches)) {
                array_shift($matches);
                $req->params = [];
                foreach ($route['params'] as $i => $name) {
                    $req->params[$name] = isset($matches[$i]) ? urldecode($matches[$i]) : null;
                }

                $allMiddleware = $this->getApplicableMiddleware($uri);
                $allMiddleware = array_merge($allMiddleware, $route['middleware']);
                $handler = $route['handler'];

                $this->runMiddleware($allMiddleware, $req, $res, function() use ($handler, $req, $res) {
                    $this->callHandler($handler, $req, $res);
                });

                return;
            }
        }

        $res->status(404)->send('404 Not Found');
    }

    /**
     * Get middleware applicable to the given path.
     *
     * @param string $path
     * @return array
     */
    protected function getApplicableMiddleware(string $path): array
    {
        $applicable = [];
        foreach ($this->middleware as $mw) {
            if ($mw['path'] === null || substr($path, 0, strlen($mw['path'])) === $mw['path']) {
                $applicable[] = $mw['middleware'];
            }
        }
        return $applicable;
    }

    /**
     * Run middleware chain.
     *
     * @param array $middleware
     * @param Request $req
     * @param Response $res
     * @param callable $final
     */
    protected function runMiddleware(array $middleware, Request $req, Response $res, callable $final): void
    {
        $index = 0;
        $next = function() use (&$middleware, &$index, $req, $res, &$next, $final) {
            if ($index < count($middleware)) {
                $mw = $middleware[$index++];
                if (is_string($mw)) {
                    $instance = new $mw();
                    $instance->handle($req, $res, $next);
                } elseif (is_callable($mw)) {
                    $mw($req, $res, $next);
                }
            } else {
                $final();
            }
        };
        $next();
    }

    /**
     * Call route handler.
     *
     * @param callable|string $handler
     * @param Request $req
     * @param Response $res
     * @throws \Exception
     */
    protected function callHandler($handler, Request $req, Response $res): void
    {
        if (is_callable($handler)) {
            $handler($req, $res);
            return;
        }

        if (is_string($handler) && strpos($handler, '@') !== false) {
            [$class, $methodName] = explode('@', $handler, 2);
            if (!class_exists($class)) {
                throw new \Exception("Controller class {$class} not found");
            }
            $obj = new $class();
            if (!method_exists($obj, $methodName)) {
                throw new \Exception("Method {$methodName} not found in {$class}");
            }
            $obj->{$methodName}($req, $res);
            return;
        }

        throw new \Exception('Route handler is not callable');
    }

    /**
     * Add GET route.
     *
     * @param string $path
     * @param callable|string $handler
     * @param array<callable> $middleware
     */
    public function get(string $path, $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    /**
     * Add POST route.
     *
     * @param string $path
     * @param callable|string $handler
     * @param array<callable> $middleware
     */
    public function post(string $path, $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }
}