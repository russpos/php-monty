<?php

class Model_Project extends Monty_Model {

    public static $schema = array(
        'table' => 'projects',
        'primary' => 'id',
        'columns' => array(
            'id'    => Monty_Model::TYPE_INTEGER,
            'name'  => Monty_Model::TYPE_TEXT,
            'description' => Monty_Model::TYPE_TEXT,
            'is_active'   => Monty_Model::TYPE_BOOL,
            'create_date' => Monty_Model::TYPE_INTEGER,
            'update_date' => Monty_Model::TYPE_INTEGER,
        ),
    );

}

