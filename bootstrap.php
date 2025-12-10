<?php

declare(strict_types=1);

/**
 * Application Bootstrap
 *
 * Initializes the auto-router, loads routes, and dispatches requests.
 */

require __DIR__ . '/vendor/autoload.php';

// Load environment variables
use App\Helpers\EnvHelper;

EnvHelper::load(__DIR__ . '/.env');

use App\Core\AutoRouter;
use App\Core\Request;
use App\Core\Response;

$router = new AutoRouter();

// Manually load specified route files
$routeFiles = [
    __DIR__ . '/src/Routes/api.php',
    __DIR__ . '/src/Routes/web.php',
];

foreach ($routeFiles as $file) {
    if (file_exists($file)) {
        $returned = require $file;
        if (is_callable($returned)) {
            $returned($router);
        } elseif (is_array($returned)) {
            foreach ($returned as $route) {
                if (isset($route['method'], $route['path'], $route['handler'])) {
                    $middleware = $route['middleware'] ?? [];
                    $router->add($route['method'], $route['path'], $route['handler'], $middleware);
                }
            }
        }
    }
}

$router->get('/', function (Request $req, Response $res) {
    $res->json([
        'code' => 'success',
        'message' => 'Welcome to Al-Ameen Face API'
    ]);
});

// Static file serving for public folder
$router->get('/public/{path*}', function (Request $req, Response $res) {
    $path = isset($req->params['path*']) ? $req->params['path*'] : '';
    
    if (empty($path)) {
        $res->status(404)->json(['error' => 'File not found']);
        return;
    }
    
    $file = __DIR__ . '/public/' . $path;
    
    // Security: prevent directory traversal
    $realPath = realpath($file);
    $publicDir = realpath(__DIR__ . '/public');
    
    if ($realPath === false || strpos($realPath, $publicDir) !== 0) {
        $res->status(403)->json(['error' => 'Access denied']);
        return;
    }
    
    if (file_exists($file) && is_file($file)) {
        $mime = mime_content_type($file);
        $res->header('Content-Type', $mime);
        readfile($file);
        exit;
    } else {
        $res->status(404)->json(['error' => 'File not found']);
    }
});

try {
    $router->dispatch();
} catch (\Throwable $e) {
    http_response_code(500);
    echo 'Internal Server Error: ' . $e->getMessage();
}
