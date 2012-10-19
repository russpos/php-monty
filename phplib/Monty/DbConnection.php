<?php

class Monty_DbConnection {

    const SQL_CREATE_SCHEMA = "CREATE TABLE IF NOT EXISTS schema_version (version INTEGER PRIMARY KEY)";
    const SQL_GET_SCHEMA_VERSION = "SELECT COALESCE(MAX(version), 0) FROM schema_version";
    const SQL_INSERT_SCHEMA_VERSION = "INSERT INTO schema_version (version) VALUES (?)";

    private static $instance;

    private static $defaults = array(
        'type' => 'mysql',
        'host' => 'localhost',
        'database' => 'monty',
        'user' => 'monty',
        'password' => 'mypass',
    );

    private function __construct($connect_opts=array()) {
        $this->conn = new PDO($connect_opts['type'].':host='.$connect_opts['host'].';dbname='.$connect_opts['database'],
            $connect_opts['user'], $connect_opts['password']);
        $this->createSchema();
        echo ".... create schema\n";
        if (empty($connect_opts['auto_upgrade'])) {
            echo "... upgrade \n";
            $this->upgrade();
        }
    }

    private function upgrade() {
        $version = $this->getSchemaVersion();
        while ($this->updateSchema(++$version));
    }

    private function updateSchema($new_version) {

    }

    private function getSchemaVersion() {
        return $this->queryValue(self::SQL_GET_SCHEMA_VERSION);
    }

    private function queryValue($sql, $default=null, $column=0) {
        $statement = $this->query($sql);
        $row = $statement->fetch();
        if (empty($row)) {
            return $default;
        }
        return $row[$column];
    }

    public function query($sql, $params = array(), $execute=true) {
        echo "\n$sql\n";
        $statement = $this->conn->prepare($sql);
        if ($execute) {
            $statement->execute();
        }
        return $statement;
    }

    public function createSchema() {
        $this->query(self::SQL_CREATE_SCHEMA);
    }

    public static function getInstance($opts=array()) {
        if (empty(self::$instance)) {
            $opts = array_merge(self::$defaults, $opts);
            self::$instance = new Monty_DbConnection($opts);
        }
        return self::$instance;
    }

}
