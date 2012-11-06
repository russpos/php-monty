<?php

class Monty_TemplateHelper {

    public function h($data) {
        return htmlspecialchars($data);
    } 

    public function he($data) {
        $this->e($this->h($data));
    }

    public function e($data) {
        echo $data;
    }

    public function iff($var, $data, $else_data='') {
        if ($var) {
            echo $data;
        } else {
            echo $else_data;
        }
    }
}
