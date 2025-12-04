<?php
declare(strict_types=1);

namespace App\Middlewares;

use App\Core\Middleware;
use  App\Core\Request;
use  App\Core\Response;

/**
 * Logging middleware to log requests.
 */
class LoggingMiddleware implements Middleware
{
    public function handle(Request $req, Response $res, callable $next): void
    {
        error_log('Request: ' . $req->method . ' ' . $req->path);
        $next();
    }
}