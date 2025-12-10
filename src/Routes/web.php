<?php
// API routes
return function ($router) {
    // API group with JSON middleware
    $router->group('', function ($router) {
        $router->group('/third-party', function ($router) {
            $router->get('/register', 'App\Controllers\ThirdPartyController@render');
        });
    }, []);
};
