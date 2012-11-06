<?php

abstract class Monty_App {
    protected $options;
    protected $request_data;

    public function __construct($opts) {
        $this->options = $opts;
        Monty_DbConnection::getInstance($opts['database']);
    }

    abstract protected function getRoutes(); 

    protected function buildAction($request, $action_params, $matches) {
        return new Monty_Action($request, $action_params, $matches);
    }

    public function route($request) {
        $method = $request->getMethod();
        $routes = $this->getRoutes();
        if (!empty($routes[$method])) {
            $handlers = $routes[$method];
            foreach ($handlers as $matcher => $action_params) {
                preg_match('#^'.$matcher.'/?$#', $request->getPath(), $matches);
                if (!empty($matches)) {
                    return $this->buildAction($request, $action_params, $matches);
                }
            }
        }
        return $this->unknownAction($request);
    }

    public function unknownAction($request) {
        header('HTTP/1.0 404 Not Found');
        header('Content-type: text/plain');
        echo 'Route for '.$request->getMethod().' "'.$request->getPath().'" was not found.';
        exit();
    }

    public function serve($request_data, $query_params, $post_fields) {
        $request = new Monty_Request($request_data, $query_params, $post_fields);
        $action = $this->route($request);
        $response = $action->dispatch();
        $response->serve();
    }

}
