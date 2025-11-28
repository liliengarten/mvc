<?php

namespace Src;
use Error;

class Request {
    protected array $body;
    public string $method;
    public array $headers;

    public function __construct() {
        $this->body = $_REQUEST;
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->headers = getallheaders() ?? [];
    }

    public function all(): array {
        return $this->body + $this->files();
    }

    public function set($field, $value) {
        $this->body[$field] = $value;
    }

    public function get($field) {
        return $this->body[$field];
    }

    public function files(): array {
        return $_FILES;
    }

    public function __get($key) {
        if (array_key_exists($key, $this->body)) {
            return $this->body[$key];
        }
        throw new Error('Accessing a non-existing property: ' . $key);
    }
}