<?php

class DummyRequest {

    public function __construct($method, $path) {
        $this->method = $method;
        $this->path = $path;
    }

    public function getMethod() {
        return $this->method;
    }

    public function getPath() {
        return $this->path;
    }
}

class SampleApp extends Monty_App {

    public $actions = array();

    public function __construct() {

    }

    public function getRoutes() {
        return array(
            'GET' => array(
                '/' => array('Foo', 'index'),
                '/samples/(\d+)/?' => array('Foo', 'get'),
            ),
        );
    }

    protected function buildAction($request, $action_params, $matches) {
        $this->actions[] = func_get_args();
    }

}

class AppTest extends TPTest {
    
    public function beforeEach() {
        $this->app = new SampleApp();
    }

    public function itMatchesRoutes() {
        $this->app->route(new DummyRequest('GET', '/')); 
        $action = $this->app->actions[0];
        $this->expect($action[1])->toEqual(array('Foo', 'index'));
    }

    public function itMatchesNumericRoutes() {
        $this->app->route(new DummyRequest('GET', '/samples/1234')); 
        $action = $this->app->actions[0];
        $this->expect($action[1])->toEqual(array('Foo', 'get'));
        $this->expect($action[2][1])->toEqual('1234');
    }

    
}

new AppTest(array('verbose' => true));
