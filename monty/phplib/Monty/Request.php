<?php

class Monty_Request {

    const HTTP_POST   = 'POST';
    const HTTP_GET    = 'GET';
    const HTTP_DELETE = 'DELETE';
    const HTTP_PUT    = 'PUT';
    const HTTP_UNKNOWN = 'UNKNOWN';

    public function __construct($server, $get, $post) {
        $this->server = $server;
        $this->get    = $get;
        $this->post   = $post;
    }

    public function getMethod() {
        switch ($this->server['REQUEST_METHOD']) {
        case self::HTTP_POST:
        case self::HTTP_GET:
        case self::HTTP_DELETE:
        case self::HTTP_PUT:
            return $this->server['REQUEST_METHOD'];
        default:
            return self::HTTP_UNKNOWN;
        }
    }

    public function getPath() {
        if (!empty($this->server['REQUEST_URI'])) {
            return $this->server['REQUEST_URI'];
        }
        return '/';
    }
}
