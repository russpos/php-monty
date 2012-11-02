<?php

class Controller_Projects extends Monty_Controller {

    public function index() {
        $project_data = Model_Project::findAll();

        $this->response = new Monty_Response_HTML();
        $this->response->assign('projects', $project_data);
        $this->response->render('projects/index.php');
        return $this->response;
    }

    public function create() {
        $project = new Model_Project();

        $project->name = $this->request->post['name'];
        $project->store();
        $this->index();
    }
}
