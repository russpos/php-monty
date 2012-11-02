<?php

abstract class Monty_Model {

    const SQL_SELECT_QUERY = "SELECT %s FROM %s ";

    const MODEL_PREFIX = 'Monty_Model_';

    const TYPE_INTEGER = 1;
    const TYPE_TEXT    = 2;
    const TYPE_BOOL    = 3;

    // Return vals
    const RETURN_MANY = 1;
    const RETURN_SINGLE = 2;

    protected $data = array();

    public function __construct($data=array()) {
        foreach (static::$schema['columns'] as $field => $type) {
            if (isset($data[$field])) {
                $this->{$field} = $this->cast($data[$field], $type);
            }
        }
    }

    public function __set($name, $value) {
        if (isset(static::$schema['columns'][$name])) {
            $this->data[$name] = $value;
        }
    }

    public function __get($name) {
        return $this->data[$name];
    }

    protected static $query_defaults = array(
        'result' => self::RETURN_MANY,
        'conditions' => array(),
        'limit' => null,
        'offset' => null,
        'order' => null,
        'hydrate' => true,
    );

    protected function cast($data, $type) {
        switch ($type) {
        case self::TYPE_INTEGER: return (int) $data;
        case self::TYPE_TEXT:    return (string) $data;
        case self::TYPE_BOOL:    return !!$data;
        }
        return $data;
    }

    public static function configure() {
        return array();
    }

    public function toJSON() {
        print_r($this->data);
        return $this->data;
    }

    public function store() {
        $this->update_date = time();
        $insert = false;
        $pk = self::$schema['primary'];
        if (empty($this->{$pk})) {
            $insert = true;
            $this->{$pk} = rand();
            $this->create_date = time();
        }
        $data = $this->toJSON();
        print_r($data);
    }

    protected static function select($params) {
        $schema = static::$schema;
        $params = array_merge(self::$query_defaults, $schema, $params);

        $table = $params['table'];
        $fields = array();
        foreach ($params['columns'] as $column => $type) {
            $fields[] = $table.'.'.$column;
        }
        $fields = implode(', ', $fields);
        $sql = sprintf(self::SQL_SELECT_QUERY, $fields, $table);
        $data = array();
        $results = self::query($sql, $data);

        if ($params['result'] === self::RETURN_SINGLE) {
            return self::returnSingle($results, $params);
        }
        return self::returnMany($results, $params);
    }

    protected static function returnMany($results, $params) {
        $values = array();
        if (empty($results)) {
            return $values;
        }
        foreach ($results as $raw_value) {
            if ($params['hydrate']) {
                $values[] = new static($raw_value);
            }
        }
        return $values;
    }

    protected static function returnSingle($results, $params) {
        // TODO
    }

    protected static function query($sql, $params=array()) {
        return Monty_DbConnection::getInstance()->query($sql, $params)->fetchAll();
    }

    public static function findAll() {
        return self::select(array(
            'result' => self::RETURN_MANY,
            'conditions' => array(),
        ));
    }

}
