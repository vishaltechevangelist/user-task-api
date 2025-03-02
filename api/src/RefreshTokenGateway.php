<?php

class RefreshTokenGateway {
    private PDO $conn;
    public function __construct(Database $database, string $key) {
        $this->conn = $database->getConnection();
        $this->key = $key;
    }

    public function saveRefreshToken(string $token, int $expires_at) : bool {
        $token_hash = hash_hmac("sha256", $token, $this->key);

        $sql = "INSERT INTO refresh_token (token_hash, expires_at)  VALUES (:token_hash, :expires_at)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":token_hash", $token_hash, PDO::PARAM_STR);
        $stmt->bindValue(":expires_at", $expires_at, PDO::PARAM_INT);
        return $stmt->execute();
        return $this->conn->lastInsertId();
    }

    public function deleteOldRefreshToken($old_refresh_token) : int {
        $token_hash = hash_hmac("sha256", $old_refresh_token, $this->key);

        $sql = "DELETE FROM refresh_token WHERE token_hash = :token_hash";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":token_hash", $token_hash, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function isRefreshTokenValid($old_refresh_token) : array | FALSE {
        $token_hash = hash_hmac("sha256", $old_refresh_token, $this->key);

        $sql = "SELECT * FROM refresh_token WHERE token_hash = :token_hash";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":token_hash", $token_hash, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}