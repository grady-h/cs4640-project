<?php
/**
 * Database Class
 *
 * Contains connection information to query PostgresSQL.
 */


class Database {
    private $dbConnector;

    public function __construct() {
        $host = Config::$db["host"];
        $user = Config::$db["user"];
        $database = Config::$db["database"];
        $password = Config::$db["pass"];
        $port = Config::$db["port"];
        $schema = Config::$db["schema"] ?? $user;

        $this->dbConnector = pg_connect("host=$host port=$port dbname=$database user=$user password=$password");

        $schema = preg_replace('/[^A-Za-z0-9_]/', '', $schema);

        @pg_query($this->dbConnector, "CREATE SCHEMA IF NOT EXISTS $schema AUTHORIZATION $user");
        @pg_query($this->dbConnector, "SET search_path TO $schema, public");
    }

    public function query($query, ...$params) {
        $res = pg_query_params($this->dbConnector, $query, $params);
        if ($res === false) {
            error_log('PG ERROR: ' . pg_last_error($this->dbConnector) . ' | SQL: ' . $query);
            return false;
        }
        return pg_fetch_all($res);
    }
}

