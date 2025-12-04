<?php
// Web routes
return function ($router) {
    // Global logging middleware
    $router->use('App\Middlewares\LoggingMiddleware');


    // Root route
    $router->get('/', function ($req, $res) {
        $res->send("Hello — welcome to the homepage.");
    });

    $router->get("/{id}", "App\Controllers\UserController@show");


    // Route with its own middleware
    $router->get('/protected', function ($req, $res) {
        $res->send('This is protected');
    }, ['App\Middlewares\AuthMiddleware']);

    // File upload route
    $router->post('/upload', function ($req, $res) {
        if ($req->hasFile('file')) {
            $file = $req->file('file');
            $res->json([
                'message' => 'File uploaded successfully',
                'file' => [
                    'name' => $file['name'],
                    'type' => $file['type'],
                    'size' => $file['size'],
                    'tmp_name' => $file['tmp_name']
                ]
            ]);
        } else {
            $res->status(400)->json(['error' => 'No file uploaded']);
        }
    });
};
