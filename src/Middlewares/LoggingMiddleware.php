<?php
declare(strict_types=1);

namespace App\Middlewares;

use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;
use App\Helpers\Logger;

/**
 * Logging middleware to log requests using Monolog.
 */
class LoggingMiddleware implements Middleware
{
    public function handle(Request $req, Response $res, callable $next): void
    {
        $timestamp = date('Y-m-d\TH:i:s.v\Z');
        $requestId = 'ID:' . time() . rand(100, 999);
        $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        // Create fancy request log
        $requestLog = "┏━ {$req->method} {$req->path} {$requestId} {$clientIp} {$timestamp}\n";
        $requestLog .= "┏─ Request Details\n";
        
        $requestData = [
            'headers' => $this->redactSensitiveHeaders($req->headers),
            'query' => $_GET ?? [],
            'params' => [],
            'body' => $req->json() ?? []
        ];
        
        $jsonLines = explode("\n", json_encode($requestData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        foreach ($jsonLines as $line) {
            $requestLog .= "│ " . $line . "\n";
        }
        $requestLog .= "┗─ End Request Details";
        
        Logger::info($requestLog, [], 'requests');
        
        $startTime = microtime(true);
        $next();
        $endTime = microtime(true);
        
        $duration = round(($endTime - $startTime) * 1000, 2);
        
        // Create fancy response log
        $responseLog = "┏━ Response 200 {$req->method} {$req->path} | {$duration}ms\n";
        $responseLog .= "│ {\n";
        $responseLog .= "│   \"status\": 200,\n";
        $responseLog .= "│   \"durationMs\": {$duration}\n";
        $responseLog .= "│ }\n";
        $responseLog .= "┗━ End Response";
        
        Logger::info($responseLog, [], 'requests');
    }
    
    private function redactSensitiveHeaders(array $headers): array
    {
        $redacted = $headers;
        if (isset($redacted['authorization'])) {
            $redacted['authorization'] = 'Bearer ***REDACTED***';
        }
        if (isset($redacted['Authorization'])) {
            $redacted['Authorization'] = 'Bearer ***REDACTED***';
        }
        return $redacted;
    }
}