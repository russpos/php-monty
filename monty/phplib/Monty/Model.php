<?php

abstract class Monty_Model {

    const SQL_SELECT_QUERY = "SELECT %s FROM %s ";
    const SQL_INSERT_QUERY = "INSERT INTO %s (%s) VALUES (%s)";
    const SQL_DELETE_QUERY = "DELETE FROM %s WHERE %s";
    const SQL_UPDATE_QUERY = "UPDATE %s SET %s WHERE %s";

    const MODEL_PREFIX = 'Monty_Model_';
 
    const TYPE_INTEGER  = 1;
    const TYPE_TEXT     = 2;
    const TYPE_BOOL     = 3;
    const TYPE_UNIXTIME = 4;

    // Return vals
    const RETURN_MANY = 1;
    const RETURN_SINGLE = 2;

    protected static $connection_class = 'Monty_DbConnection';

    protected $data = array();

    public static $query_log = array();

    protected $db_state = array();

    protected $dirty = array();

    public static $schema = array();

    public function __construct($data=array(), $from_db=false) {
        foreach (static::$schema['columns'] as $field => $type) {
            if (isset($data[$field])) {
                $this->{$field} = $this->cast($data[$field], $type);
            }
        }
        if ($from_db) {
            $this->dirty = array();
            $this->db_state = $this->data;
        }
    }

    public function __set($name, $value) {
        if (isset(static::$schema['columns'][$name])) {
            if (!isset($this->data[$name]) || $this->data[$name] !== $value) {
                $this->data[$name] = $value;
                if (!isset($this->db_state[$name]) || $this->db_state[$name] !== $value) {
                    $this->dirty[$name] = $value;
                } else {
                    unset($this->dirty[$name]);
                }
                ksort($this->dirty);
                ksort($this->data);
            }
        }
    }

    public function __get($name) {
        if (!isset($this->data[$name])) {
            return null;
        }
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
        ksort($this->data);
        return $this->data;
    }

    private function generatePK() {
        // PK generation can be setup per app, or per model even.
        // If you want to auto-increment, don't do anything. If you
        // want to generate a pk from an external resource such as 
        // a ticket server, do that.
    }

    public function delete() {
        $table = static::$schema['table'];
        list($clause, $args) = $this->pkClause();
        $sql = sprintf(self::SQL_DELETE_QUERY, "`$table`", $clause);
        $this->query($sql, $args);
    }

    protected function pkClause() {
        $props = array();
        $props[$this->pk()] = $this->id();
        return static::generateClause($props);
    }

    protected static function generateClause($props) {
        $table = static::$schema['table'];
        $clauses = array();
        foreach ($props as $name => $value) {
            $clauses[] = "`$table`.`$name` = :$name";
        }
        $clause = implode(' AND ', $clauses);
        return array($clause, $props);
    }

    protected function pk() {
        return static::$schema['primary'];
    }

    protected function id() {
        $pk = static::$schema['primary'];
        return $this->{$pk};
    }

    public function store() {
        $this->update_date = MONTY_TIME;
        $insert = false;
        $pk = static::$schema['primary'];
        $table = static::$schema['table'];
        $id = $this->id();
        if (empty($id)) {
            $insert = true;
            $this->generatePK();
            $this->create_date = MONTY_TIME;
            $data = $this->toJSON();
        } 
        if ($insert) {
            $data = $this->toJSON();
            $keys = array_keys($data);
            $qs = implode(', ', array_map(function($name) {
                return ':'.$name;
            }, $keys));
            $keys = array_map(function($key) {
                return "`$key`";
            }, $keys);
            $columns = implode(", ", $keys);
            $sql = sprintf(self::SQL_INSERT_QUERY, "`$table`", $columns, $qs);
        } else {
            if (empty($this->dirty)) {
                return true;
            }
            $data = $this->dirty;
            $columns = "`$table`.".implode(", `$table`.", array_map(function($field) {
                return "`$field` = :$field";
            }, array_keys($this->dirty)));
            list($clause, $args) = $this->pkClause();
            $data[$pk] = $this->{$pk};
            $sql = sprintf(self::SQL_UPDATE_QUERY, "`$table`", $columns, $clause);
        }
        $returned = static::query($sql, $data, false);
        if ($returned && $insert && !$this->id()) {
            $this->{$pk} = static::getLastInsertId();
        }
        $this->dirty = array();
        $this->db_state = $this->data;
        return $returned;
    }

    protected static function getLastInsertId() {
        return static::getDbConnection()->lastInsertId();
    }

    protected static function select($params) {
        $schema = static::$schema;
        $params = array_merge(static::$query_defaults, $schema, $params);

        $table = $params['table'];
        $fields = array();
        ksort($params['columns']);
        foreach ($params['columns'] as $column => $type) {
            $fields[] = "`$table`.`$column`";
        }
        $fields = implode(', ', $fields);
        $sql = sprintf(self::SQL_SELECT_QUERY, $fields, "`$table`");
        $data = array();
        $clause = '';
        if (!empty($params['conditions'])) {
            $clause = 'WHERE ';
            $conditions = array();
            foreach ($params['conditions'] as $col => $val) {
                $conditions[] = "`$table`.`$col` = :$col";
                $data[$col] = $val;
            }
            $clause .= implode(' AND ', $conditions);
        }

        $results = static::query($sql.$clause, $data);

        if ($params['result'] === self::RETURN_SINGLE) {
            return static::returnSingle($results, $params);
        }
        return static::returnMany($results, $params);
    }

    protected static function returnMany($results, $params) {
        $values = array();
        if (empty($results)) {
            return $values;
        }
        foreach ($results as $raw_value) {
            if ($params['hydrate']) {
                $values[] = new static($raw_value, true);
            }
        }
        return $values;
    }

    protected static function returnSingle($results, $params) {
        $values = null;
        if (empty($results)) {
            return $values;
        }
        $values = $results[0];
        if ($params['hydrate']) {
            $values = new static($values, true);
        }
        return $values;
    }

    protected static function getDbConnection() {
        $class_name = static::$connection_class;
        return $class_name::getInstance();
    }

    protected static function query($sql, $params=array(), $fetch=true) {
        self::$query_log[] = array($sql, $params);
        try {
            $query = static::getDbConnection()->query($sql, $params);
        } catch (PDOException $e) {
            error_log("MontyDbException: ".$e->getMessage());
            return null;
        }
        if ($fetch) {
            return $query->fetchAll();
        }
        return $query;
    }

    public static function findAll() {
        return static::select(array(
            'result' => self::RETURN_MANY,
            'conditions' => array(),
        ));
    }

    public static function find($value) {
        $conditions = array();
        $conditions[static::$schema['primary']] = $value;
        return static::select(array(
            'result' => self::RETURN_SINGLE,
            'conditions' => $conditions,
        ));
    }

}
