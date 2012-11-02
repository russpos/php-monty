<?php

abstract class Monty_Controller {

    public function __construct($request) {
        $this->request = $request;
    }

    public function action($name, $params) {
        if (method_exists($this, $name)) {
            unset($params[0]);
            $args = array_values($params);
            return call_user_func_array(array($this, $name), $args);
        }
    }

}

