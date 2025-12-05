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
    private string $logFile;
    private int $maxLogSize;

    public function __construct(string $logFile = 'logs/app.log', int $maxLogSize = 10485760) // 10MB default
    {
        $this->logFile = $logFile;
        $this->maxLogSize = $maxLogSize;
        
        // Ensure log directory exists
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }

    public function handle(Request $req, Response $res, callable $next): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $headers = json_encode($req->headers);
        $logEntry = "[{$timestamp}] {$req->method} {$req->path} | Headers: {$headers}\n";
        
        // Check if log file needs truncation
        if (file_exists($this->logFile) && filesize($this->logFile) > $this->maxLogSize) {
            $this->truncateLog();
        }
        
        // Write log entry
        file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
        
        // Also log to error_log for debugging
        error_log('Request: ' . $req->method . ' ' . $req->path);
        
        $next();
    }

    private function truncateLog(): void
    {
        // Keep last 50% of the log file
        $content = file_get_contents($this->logFile);
        $lines = explode("\n", $content);
        $keepLines = (int)(count($lines) / 2);
        $newContent = implode("\n", array_slice($lines, -$keepLines));
        
        file_put_contents($this->logFile, $newContent, LOCK_EX);
    }
}