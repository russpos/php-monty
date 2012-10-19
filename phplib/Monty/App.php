<?php

class Monty_App {
    private $options;
    private $request_data;

    public function __construct($opts) {
        $this->options = $opts;
        Monty_DbConnection::getInstance($opts['database']);
    }

    private function getRoutes() {
        return array(
            Monty_Request::HTTP_GET => array(
                '/' => array('Projects', 'index'),
                '/foo/([0-9]+)/par' => array('Jobs', 'index'),
                '/foo/([0-9]+)/par/([0-9]+)' => array('Jobs', 'get'),
            ),
        );
    } 

    private function buildAction($request, $action_params, $matches) {
        return new Monty_Action($request, $action_params, $matches);
    }

    private function route($request) {
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

    public function serve($request_data, $query_params, $post_fields) {
        $request = new Monty_Request($request_data, $query_params, $post_fields);
        $action = $this->route($request);
        $response = $action->dispatch();
        $response->serve();
    }

}
