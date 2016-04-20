<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * MySQL.php
 * Main entry point for all MySQL connections
 */

namespace SmoothPHP\Framework\Database;

use SmoothPHP\Framework\Core\Config;
use SmoothPHP\Framework\Database\Mapper\MySQLObjectMapper;

class MySQL {
    private $connection;

    public function __construct(Config $config) {
        $prefix = ini_get('mysqli.allow_persistent') ? 'p:' : '';
        $this->connection = new \mysqli($prefix . $config->mysql_host, $config->mysql_user, $config->mysql_password, $config->mysql_database);
    }

    public function prepare($query, $returnsData = true) {
        return $returnsData ? new MySQLStatementWithResult($this->connection, $query)
            : new MySQLStatementWithoutResult($this->connection, $query);
    }

    public function execute($query, array $params = array()) {
        $this->prepare($query, false)->execute($params);
    }

    public function fetch($query, array $params = array()) {
        return $this->prepare($query, true)->execute($params);
    }

    public function map($clazz) {
        return new MySQLObjectMapper($this, $clazz);
    }

    public static function checkError($source) {
        if ($source->errno)
            throw new MySQLException($source->error);
    }

}
