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

    protected static $schema;

    protected static $query_defaults = array(
        'result' => self::RETURN_MANY,
        'conditions' => array(),
        'limit' => null,
        'offset' => null,
        'order' => null,
        'hydrate' => false,
    );

    public static function configure() {
        return array();
    }

    public static function register() {
        static::$schema = static::configure();
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
        $results = self::query($sql);
    }

    private static function query($sql) {
        $conn = Monty_DbConnection::getInstance();
    }

    public static function findAll() {
        return self::select(array(
            'result' => self::RETURN_MANY,
            'conditions' => array(),
        ));
    }

}
