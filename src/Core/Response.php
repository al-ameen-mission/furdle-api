<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Response class to handle HTTP responses.
 */
class Response
{
    /** @var int */
    protected $statusCode = 200;
    /** @var array<string, string> */
    protected $headers = [];
    /** @var bool */
    protected $sent = false;

    /**
     * Set the HTTP status code.
     *
     * @param int $code
     * @return self
     */
    public function status(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * Set a response header.
     *
     * @param string $name
     * @param string $value
     * @return self
     */
    public function header(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Send JSON response.
     *
     * @param mixed $data
     */
    public function json($data): void
    {
        $this->header('Content-Type', 'application/json');
        $this->send(json_encode($data));
    }

    /**
     * Send response data.
     *
     * @param string $data
     */
    public function send(string $data): void
    {
        if ($this->sent) {
            return;
        }
        $this->sent = true;
        http_response_code($this->statusCode);
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        echo $data;
    }

    /**
     * Redirect to a URL.
     *
     * @param string $url
     * @param int $status
     */
    public function redirect(string $url, int $status = 302): void
    {
        $this->status($status)->header('Location', $url)->send('');
    }
}