<?php

declare(strict_types=1);

namespace App\Middlewares;


use App\Core\Middleware;
use  App\Core\Request;
use  App\Core\Response;

/**
 * Authentication middleware.
 */
class AuthMiddleware implements Middleware
{
  public function handle(Request $req, Response $res, callable $next): void
  {
    if ($req->header('Authorization') !== 'secret') {
      $res->status(401)->send('Unauthorized');
      return;
    }
    $next();
  }
}
