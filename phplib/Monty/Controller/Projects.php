<?php

class Monty_Controller_Projects extends Monty_Controller {

    public function index() {
        $projects = Monty_Model_Project::findAll();
        $project_data = Monty_Tools::jsonify($projects);

        $this->response = new Monty_Response_HTML();
        $this->response->assign('projects', $project_data);
        return $this->response;
    }
}
