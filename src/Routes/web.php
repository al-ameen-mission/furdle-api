<?php
// Web routes
return function ($router) {
    // Third-party group with CORS middleware
    $router->group('/third-party', function ($router) {
        $router->get('/register', 'App\Controllers\ThirdPartyController@render');
        $router->post('/register', 'App\Controllers\ThirdPartyController@register');
        $router->post('/delete', 'App\Controllers\ThirdPartyController@delete');
    }, ['App\Middlewares\CorsMiddleware']);
};
