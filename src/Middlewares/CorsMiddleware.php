<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;

/**
 * CORS middleware.
 */
class CorsMiddleware implements Middleware
{
  public function handle(Request $req, Response $res, callable $next): void
  {
    $res->header('Access-Control-Allow-Origin', '*');
    $res->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    $res->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');

    if ($req->method === 'OPTIONS') {
      $res->status(200)->send('');
      return;
    }

    $next();
  }
}
