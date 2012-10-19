<?php

class Monty_Response_HTML extends Monty_Response {

    private $template_vars = array();

    public function assign($name, $val) {
        if (is_string($name)) {
            $this->template_vars[$name] = $val;
        } else if (is_array($name)) {
            $this->template_vars = array_merge($this->template_vars, $name);
        }
    }
}
