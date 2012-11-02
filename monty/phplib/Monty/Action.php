<?php

class Monty_Action {

    public function __construct($request, $action_params, $params) {
        $class_name = $action_params[0];
        $this->controller = new $class_name($request);
        $this->action_name = $action_params[1];
        $this->params = $params;
    }

    public function dispatch() {
        return $this->controller->action($this->action_name, $this->params);
    }
}
