<?php

namespace App\Core;

class DB
{
    protected static $mySqlInstance;
    protected static $pgSqlInstance;

    private function __construct() { }

    /**
     * Get MySql Instance
     * @return \PDO
     */
    public static function getMySqlInstance() {
        try {
            if (is_null(self::$mySqlInstance)) {
                $dsn = 'mysql:host=' . env('DB_MYSQL_HOST') . ':' . env('DB_MYSQL_PORT') . ';dbname=' . env('DB_MYSQL_NAME');
                self::$mySqlInstance = new \PDO($dsn, env('DB_MYSQL_USER'), env('DB_MYSQL_PASS'));
                self::$mySqlInstance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            }
        } catch(\PDOException $exception) {
            throw $exception;
        }

        return self::$mySqlInstance;
    }

    /**
     * Get PostgreSql Instance
     * @return \PDO
     */
    public static function getPgSqlInstance() {
        try {
            if (is_null(self::$pgSqlInstance)) {
                $dsn = 'pgsql:host=' . env('DB_PGSQL_HOST') . ';port=' . env('DB_PGSQL_PORT') . ';dbname=' . env('DB_PGSQL_NAME');
                self::$pgSqlInstance = new \PDO($dsn, env('DB_PGSQL_USER'), env('DB_PGSQL_PASS'));
                self::$pgSqlInstance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            }
        } catch(\PDOException $exception) {
            throw $exception;
        }

        return self::$pgSqlInstance;
    }
}