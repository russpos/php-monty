<?php

class SampleApp extends Monty_App {

    protected function getRoutes() {
        return array(
            Monty_Request::HTTP_GET => array(
                '/' => array('Controller_Projects', 'index'),
                '/projects/(\d+)/?' => array('Controller_Projects', 'get'),
            ),
            Monty_Request::HTTP_POST => array(
                '/' => array('Controller_Projects', 'create'),
                '/projects/(\d+)/?' => array('Controller_Projects', 'update'),
            ),
        );
    } 

}
