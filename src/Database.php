<?php

class Database {

    private ?PDO $conn = NULL;

    public function __construct(
        private string $host, 
        private string $name, 
        private string $user, 
        private string $password
        ) {
    }

    public function getConnection() : PDO {

        if ($this->conn === NULL) {
            $dsn = "mysql:host={$this->host};dbname={$this->name}";
            $this->conn = new PDO($dsn, $this->user, $this->password,[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        }
        return $this->conn;
    }
}