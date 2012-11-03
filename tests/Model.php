<?php

class MockDbConnection {

    static $instance;

    public $query_stack = array();

    public function fetchAll() {
        return array();
    }

    public function query() {
        $this->query_stack[] = func_get_args();
        return $this;
    }

    public static function reset() {
        $instance = self::getInstance();
        $instance->query_stack = array();
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
            'INSERT INTO samples (is_true, name, sample_id) VALUES (:is_true, :name, :sample_id)'
        );
        $this->expect($args['name'])->toEqual('Sam');
        $this->expect($args['is_true'])->toEqual(true);
        $this->expect(is_int($args['sample_id']))->toBeTruthy();
    }

    public function itShouldSelect() {
        $all = SampleModel::findAll();
        $this->expect(count($this->conn->query_stack))->toEqual(2);
        list($sql, $args) = $this->conn->query_stack[1];

        $this->expect(trim($sql))->toEqual(
            'SELECT samples.is_true, samples.name, samples.sample_id FROM samples'
        );
        $this->expect($args)->toEqual(array());
    }

    public function itShouldDelete() {
        $this->model->delete();
        $this->expect(count($this->conn->query_stack))->toEqual(2);
        list($sql, $args) = $this->conn->query_stack[1];
        $this->expect(trim($sql))->toEqual(
            'DELETE FROM samples WHERE samples.sample_id = :sample_id'
        );
        $this->expect($args['sample_id'])->toEqual($this->model->sample_id);
    }

    public function itShouldUpdate() {
        $this->model->name = 'Doug';
        $this->model->store();
        $this->expect(count($this->conn->query_stack))->toEqual(2);
        list($sql, $args) = $this->conn->query_stack[1];
        $this->expect(trim($sql))->toEqual(
            'UPDATE samples SET samples.name = :name WHERE samples.sample_id = :sample_id'
        );
        $this->expect($args['name'])->toEqual('Doug');
        $this->expect($args['sample_id'])->toEqual($this->model->sample_id);
    }

    public function itShouldBeNoop() {
        $this->model->name = 'Doug';
        $this->model->name = 'Sam';
        $this->model->store();

        $this->expect(count($this->conn->query_stack))->toEqual(1);
    }
}
new ModelTest($args);
