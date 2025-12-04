# Auto Router

A lightweight PHP auto-router inspired by Express.js, featuring dynamic parameters, middleware support, and PSR-4 autoloading.

## Features

- **Dynamic Routing**: Support for parameterized routes like `/user/{id}`
- **Middleware**: Chain middleware functions before route handlers
- **Controller Support**: Use `Controller@method` syntax with autoloading
- **PSR-4 Autoloading**: Industry-standard namespace mapping
- **Type Safety**: Strict typing and modern PHP features

## Project Structure

```
├── src/
│   ├── Core/           # Core classes (AutoRouter, Request, Response)
│   ├── Controllers/    # Application controllers
│   └── Routes/         # Route definitions
├── bootstrap.php       # Application entry point
├── composer.json       # Dependencies and autoload config
└── README.md
```

## Installation

1. Install dependencies:
   ```bash
   composer install
   ```

2. Start the development server:
   ```bash
   composer run serve
   # or
   php -S localhost:8000 bootstrap.php
   ```

## Usage

### Defining Routes

Routes are defined in `src/Routes/*.php` files. Each file should return a callable that receives the router instance:

```php
<?php
return function($router) {
    // Global logging middleware
    $router->use(function($req, $res, $next) {
        error_log('Request: ' . $req->method . ' ' . $req->path);
        $next();
    });

    // Route groups with middleware
    $router->group('/api', function($router) {
        $router->get('/users', 'UserController@index');
        $router->post('/users', 'UserController@store');
    }, [function($req, $res, $next) {
        // API authentication middleware
        if (!$req->header('Authorization')) {
            $res->status(401)->send('Unauthorized');
            return;
        }
        $next();
    }]);

    // Simple route
    $router->get('/', function($req, $res) {
        $res->send('Hello World!');
    });

    // Route with parameters
    $router->get('/user/{id}', function($req, $res) {
        $id = $req->param('id');
        $res->json(['user_id' => $id]);
    });

    // Controller route
    $router->get('/user/{id}/profile', 'UserController@show');

    // Route with middleware
    $router->get('/protected', function($req, $res) {
        $res->send('Secret content');
    }, [function($req, $res, $next) {
        if ($req->header('Authorization') !== 'secret') {
            $res->status(401)->send('Unauthorized');
            return;
        }
        $next();
    }]);
};
```

### Controllers

Controllers should be placed in `src/Controllers/` and follow PSR-4 naming:

```php
<?php
namespace App\Controllers;

use App\Request;
use App\Response;

class UserController
{
    public function show(Request $req, Response $res): void
    {
        $id = $req->param('id');
        $res->json(['message' => 'User profile', 'id' => $id]);
    }
}
```

### Request Object

The `Request` object provides access to HTTP request data:

- `$req->method`: HTTP method
- `$req->path`: Request path
- `$req->query`: Query parameters
- `$req->body`: Raw request body
- `$req->headers`: Request headers
- `$req->params`: Route parameters
- `$req->param('name')`: Get route parameter
- `$req->query('name')`: Get query parameter
- `$req->header('name')`: Get header
- `$req->json()`: Parse JSON body

### Response Object

The `Response` object handles HTTP responses:

- `$res->status(404)`: Set status code
- `$res->header('Content-Type', 'application/json')`: Set header
- `$res->send('data')`: Send text response
- `$res->json(['key' => 'value'])`: Send JSON response
- `$res->redirect('/path')`: Redirect

### Route Groups

Group routes with shared prefixes and middleware:

```php
$router->group('/admin', function($router) {
    $router->get('/users', 'AdminController@users');
    $router->post('/users', 'AdminController@createUser');
}, [function($req, $res, $next) {
    // Admin authentication middleware
    if (!isAdmin($req)) {
        $res->status(403)->send('Admin access required');
        return;
    }
    $next();
}]);
```

Groups can be nested and inherit middleware from parent groups.

## Examples

Visit these URLs after starting the server:

- `http://localhost:8000/` → Hello message
- `http://localhost:8000/user` → User list (requires `User-Auth: valid` header)
- `http://localhost:8000/user/123` → User ID display
- `http://localhost:8000/user/123/post/456` → User and post params
- `http://localhost:8000/user/ctrl/123` → Controller response
- `http://localhost:8000/api/data` → API data (requires `API-Key: secret` header)
- `http://localhost:8000/protected` → 401 unless `Authorization: secret` header

## Requirements

- PHP 8.0+
- Composer (for autoloading)

## License

MIT
