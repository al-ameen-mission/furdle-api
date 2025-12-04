<?php
declare(strict_types=1);

namespace App\Middlewares;

use App\Core\Middleware;
use  App\Core\Request;
use  App\Core\Response;

/**
 * JSON response middleware.
 */
class JsonMiddleware implements Middleware
{
    public function handle(Request $req, Response $res, callable $next): void
    {
        $res->header('Content-Type', 'application/json');
        $next();
    }
}