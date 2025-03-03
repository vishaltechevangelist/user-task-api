<?php

class UserGateway {
    private PDO $conn;
    public function __construct(Database $database) {
        $this->conn = $database->getConnection();
    }

    public function saveUser($data) : string {
    
        $password_hash = password_hash($data["password"], PASSWORD_DEFAULT);
        $api_key = bin2hex(random_bytes(16));

        $sql = "INSERT INTO user (name, username, password_hash, api_key) VALUES
        (:name, :username, :password_hash, :api_key)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":name", $data["name"], PDO::PARAM_STR);
        $stmt->bindValue(":username", $data["username"], PDO::PARAM_STR);
        $stmt->bindValue(":password_hash", $password_hash, PDO::PARAM_STR);
        $stmt->bindValue(":api_key", $api_key, PDO::PARAM_STR);
        $stmt->execute();
        
        return $api_key;
    }

    public function getByAPIKey(string $api_key) : array | FALSE {
        $sql = "SELECT * FROM user WHERE api_key = :api_key";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":api_key", $api_key, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByUsername(string $username) : array | FALSE {
        $sql = "SELECT * FROM user where username = :username";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":username", $username, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getById(int $id) : array | FALSE {
        $sql = "SELECT * FROM user where id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}