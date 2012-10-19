<?php

class Monty_Model_Project extends Monty_Model {

    static function configure() {
        return array(
            'table' => 'projects',
            'primary' => array('project_id'),
            'columns' => array(
                'project_id' => Monty_Model::TYPE_INTEGER,
                'name'       => Monty_Model::TYPE_TEXT,
            ),
        );
    }

}

Monty_Model_Project::register();
