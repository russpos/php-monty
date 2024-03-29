<?php

class MockDbConnection {

    static $instance;

    public $return_vals = array(true);

    public $query_stack = array();

    public function fetchAll() {
        return $this->return_vals;
    }

    public function lastInsertId() {
        return rand();
    }

    public function query() {
        $this->query_stack[] = func_get_args();
        return $this;
    }

    public static function reset() {
        $instance = self::getInstance();
        $instance->query_stack = array();
        $instance->return_vals = array(true);
    }

    public static function getInstance() {
        if (empty(self::$instance)) {
            self::$instance = new MockDbConnection();
        }
        return self::$instance; 
    }
}

class SampleModel extends Monty_Model {

    static $schema = array(
        'primary' => 'sample_id',
        'table' => 'samples',
        'columns' => array(
            'sample_id' => Monty_Model::TYPE_INTEGER,
            'is_true'   => Monty_Model::TYPE_BOOL,
            'name'      => Monty_Model::TYPE_TEXT,

            // These are magic!
            'create_date' => Monty_Model::TYPE_UNIXTIME,
            'update_date' => Monty_Model::TYPE_UNIXTIME,
        )
    );

    static $connection_class = 'MockDbConnection';

}

class ModelTest extends TPTest {

    protected $model;
    protected $conn;

    public function beforeEach() {
        MockDbConnection::reset();
        $this->model = new SampleModel();
        $this->conn = MockDbConnection::getInstance();

        $this->model->name = 'Sam';
        $this->model->is_true = true;
        $this->model->store();
    }

    public function itShouldInsert() {
        $this->expect(count($this->conn->query_stack))->toEqual(1);
        list($sql, $args) = $this->conn->query_stack[0];
        $this->expect($sql)->toEqual(
            'INSERT INTO `samples` (`create_date`, `is_true`, `name`, `update_date`) VALUES (:create_date, :is_true, :name, :update_date)'
        );
        $this->expect($args['name'])->toEqual('Sam');
        $this->expect($args['is_true'])->toEqual(true);
    }

    public function itShouldSelect() {
        $all = SampleModel::findAll();
        $this->expect(count($this->conn->query_stack))->toEqual(2);
        list($sql, $args) = $this->conn->query_stack[1];

        $this->expect(trim($sql))->toEqual(
            'SELECT `samples`.`create_date`, `samples`.`is_true`, `samples`.`name`, `samples`.`sample_id`, `samples`.`update_date` FROM `samples`'
        );
        $this->expect($args)->toEqual(array());
    }

    public function itShouldDelete() {
        $this->model->delete();
        $this->expect(count($this->conn->query_stack))->toEqual(2);
        list($sql, $args) = $this->conn->query_stack[1];
        $this->expect(trim($sql))->toEqual(
            'DELETE FROM `samples` WHERE `samples`.`sample_id` = :sample_id'
        );
        $this->expect($args['sample_id'])->toEqual($this->model->sample_id);
    }

    public function itShouldUpdate() {
        $this->model->name = 'Doug';
        $this->model->store();
        $this->expect(count($this->conn->query_stack))->toEqual(2);
        list($sql, $args) = $this->conn->query_stack[1];
        $this->expect(trim($sql))->toEqual(
            'UPDATE `samples` SET `samples`.`name` = :name WHERE `samples`.`sample_id` = :sample_id'
        );
        $this->expect($args['name'])->toEqual('Doug');
        $this->expect($args['sample_id'])->toEqual($this->model->sample_id);
    }

    public function itShouldUpdateMulitples() {
        $this->model->name = 'Doug';
        $this->model->is_true = false;
        $this->model->store();
        $this->expect(count($this->conn->query_stack))->toEqual(2);
        list($sql, $args) = $this->conn->query_stack[1];
        $this->expect(trim($sql))->toEqual(
            'UPDATE `samples` SET `samples`.`is_true` = :is_true, `samples`.`name` = :name WHERE `samples`.`sample_id` = :sample_id'
        );
        $this->expect($args['name'])->toEqual('Doug');
        $this->expect($args['is_true'])->toEqual(false);
        $this->expect($args['sample_id'])->toEqual($this->model->sample_id);
    }

    public function itShouldBeNoop() {
        $this->model->name = 'Doug';
        $this->model->name = 'Sam';
        $this->model->store();

        $this->expect(count($this->conn->query_stack))->toEqual(1);
    }

    public function itShouldHydrateArrayFindMany() {
        $this->conn->return_vals = array(
            array(
                'sample_id' => 123,
                'name' => 'Bob',
                'is_true' => false,
            ),
            array(
                'sample_id' => 140,
                'name' => 'Robert',
                'is_true' => true
            ),
        );

        $results = SampleModel::findAll();

        $this->expect($results)->toHaveCount(2);
        $model = $results[0];
        $this->expect($model)->toBeInstanceOf('SampleModel');
        $this->expect($model->name)->toEqual('Bob');
        $this->expect($model->is_true)->toBe(false);
        $this->expect($model->sample_id)->toBe(123);

        $model = $results[1];
        $this->expect($model)->toBeInstanceOf('SampleModel');
        $this->expect($model->name)->toEqual('Robert');
        $this->expect($model->is_true)->toBe(true);
        $this->expect($model->sample_id)->toBe(140);
    }

    public function itShouldHydrateInstanceFindOne() {
        $this->conn->return_vals = array(
            array(
                'sample_id' => 123,
                'name' => 'Bob',
                'is_true' => false,
            ),
        );
        $model = SampleModel::find(123);
        $this->expect($model)->toBeInstanceOf('SampleModel');
        $this->expect($model->name)->toEqual('Bob');
        $this->expect($model->is_true)->toBe(false);
        $this->expect($model->sample_id)->toBe(123);


    }
}
new ModelTest($args);
