<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Interface for middleware classes.
 */
interface Middleware
{
    /**
     * Handle the middleware.
     *
     * @param Request $req
     * @param Response $res
     * @param callable $next
     */
    public function handle(Request $req, Response $res, callable $next): void;
}