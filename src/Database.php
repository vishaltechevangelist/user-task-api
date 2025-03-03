<?php
namespace App;

use PDO;

class Database {

    private PDO $conn;

    public function __construct(
        private string $host, 
        private string $name, 
        private string $user, 
        private string $password
        ) {
    }

    public function getConnection() : PDO {

        if (!isset($this->conn)) {
            $dsn = "mysql:host={$this->host};dbname={$this->name}";
            $this->conn = new PDO($dsn, $this->user, $this->password,[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        }
        return $this->conn;
    }
}