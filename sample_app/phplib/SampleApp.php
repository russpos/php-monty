<?php

class SampleApp extends Monty_App {

    protected function getRoutes() {
        return array(
            Monty_Request::HTTP_GET => array(
                '/' => array('Controller_Projects', 'index'),
                '/foo/([0-9]+)/par' => array('Controller_Jobs', 'index'),
                '/foo/([0-9]+)/par/([0-9]+)' => array('Controller_Jobs', 'get'),
            ),
            Monty_Request::HTTP_POST => array(
                '/' => array('Projects', 'create'),
            ),
        );
    } 

}
