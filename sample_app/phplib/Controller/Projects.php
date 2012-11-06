<?php

class Controller_Projects extends Monty_Controller {

    public function index() {
        $project_data = Model_Project::findAll();

        $this->response = new Monty_Response_HTML();
        $this->response->assign('projects', $project_data);
        $this->response->render('projects/index.php');
        return $this->response;
    }

    public function get($id) {
        $project = Model_Project::find($id);
        $this->response = new Monty_Response_HTML();
        if (empty($project)) {
            $this->notFound($id);
        } else {
            $this->renderProject($project);
        }
        return $this->response;
    }

    protected function renderProject($project) {
        $this->response->assign('project', $project);
        $this->response->render('projects/view.php');
    }

    protected function notFound($id) {
        $this->response->assign('id', $id);
        $this->response->render('projects/not_found.php');
    }

    public function update($id) {
        $project = Model_Project::find($id);
        $this->response = new Monty_Response_HTML();
        if (empty($project)) {
            $this->notFound($id);
        } else {
            $errors = array();
            if (empty($this->request->post['name'])) {
                $errors[] = "Name must not be blank.";
            } else {
                $project->name = $this->request->post['name'];
            }

            if (empty($this->request->post['description'])) {
                $errors[] = "Description must not be blank.";
            } else {
                $project->description = $this->request->post['description'];
            }

            $project->is_active = !empty($this->request->post['is_active']);
            if (empty($errors)) {
                if (!$project->store()) {
                    $errors[] = 'An error occurred while saving your changes!';
                } else {
                    $this->response->assign('success', 'Your changes have been saved!');
                }
            }

            $this->response->assign('errors', $errors);
            $this->renderProject($project);
        }
        return $this->response;
    }

    public function create() {
        $project = new Model_Project();

        $project->name = $this->request->post['name'];
        $project->store();
        return $this->index();
    }
}
