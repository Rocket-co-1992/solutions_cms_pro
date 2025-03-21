<?php

namespace Pandao\Common\Core;

use \PDO;
use \PDOException;

class Database extends PDO
{
    public $isConnected = false;

    /**
     * Database constructor. Establishes a database connection using PDO.
     *
     * @param string $host Database host
     * @param string $dbname Database name
     * @param int $port Database port
     * @param string $username Database username
     * @param string $password Database password
     * @param array $options PDO options (optional)
     */
    public function __construct($host, $dbname, $port, $username, $password, $options = [])
    {
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";
        $defaultOptions = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        $options = array_replace($defaultOptions, $options);

        try {
            parent::__construct($dsn, $username, $password, $options);
            $this->isConnected = true;
        } catch (PDOException $e) {
            $this->isConnected = false;
        }
    }

    /**
     * Get the number of rows returned by the last query.
     *
     * @return int
     */
    public function last_row_count()
    {
        return $this->query('SELECT FOUND_ROWS()')->fetchColumn();
    }
}
