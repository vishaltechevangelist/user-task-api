<?php

class TaskGateway{

    private PDO $conn;
    public function __construct(Database $database) {
        $this->conn = $database->getConnection();
    }

    public function getAll(): array {
        $sql = "SELECT * FROM task";
        $result = $this->conn->query($sql);

        //return $result->fetchAll(PDO::FETCH_ASSOC);
        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $row['is_completed'] = (bool) $row['is_completed'];
            $data[] = $row;
        }
        return $data;
    }

    public function getAllTaskForUser(int $user_id): array {
        $sql = "SELECT * FROM task WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();

        //return $result->fetchAll(PDO::FETCH_ASSOC);
        $data = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['is_completed'] = (bool) $row['is_completed'];
            $data[] = $row;
        }
        return $data;
    }

    public function getTaskById(string $task_id): array | FALSE {
        $sql = "SELECT * FROM task where id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $task_id, PDO::PARAM_INT);
        $stmt->execute();

        $data = FALSE;
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['is_completed'] = (bool) $row['is_completed'];
            $data = $row;
        }
        return $data;
    }

    public function getTaskByIdForUser(int $user_id, string $task_id): array | FALSE {
        $sql = "SELECT * FROM task WHERE id = :id AND user_id = :user_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $task_id, PDO::PARAM_INT);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $data = FALSE;
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['is_completed'] = (bool) $row['is_completed'];
            $data = $row;
        }
        return $data;
    }

    public function saveTask(array $data) : string {
        $sql = "INSERT INTO task (name, priority, is_completed) VALUES (:name, :priority, :is_completed)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":name", $data["name"], PDO::PARAM_STR);
        $stmt->bindValue(":is_completed", $data["is_completed"]??FALSE, PDO::PARAM_BOOL);

        if (empty($data['priority'])) {
            $stmt->bindValue(":priority", null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(":priority", $data["priority"], PDO::PARAM_INT);
        }
        //print_r($stmt);die;
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    public function saveTaskForUser(int $user_id, array $data) : string {
        $sql = "INSERT INTO task (name, priority, is_completed, user_id) VALUES 
        (:name, :priority, :is_completed, :user_id)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":name", $data["name"], PDO::PARAM_STR);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->bindValue(":is_completed", $data["is_completed"]??FALSE, PDO::PARAM_BOOL);

        if (empty($data['priority'])) {
            $stmt->bindValue(":priority", null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(":priority", $data["priority"], PDO::PARAM_INT);
        }
        //print_r($stmt);die;
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    public function updateTask(string $task_id, array $data) : int {
        $fields = [];

        if (! empty($data["name"])) {
            $fields["name"] = [$data["name"], PDO::PARAM_STR]; 
        }

        if (! empty($data["priority"])) {
            $fields["priority"] = [$data["priority"], PDO::PARAM_INT]; 
        }

        if (! empty($data["is_completed"])) {
            $fields["is_completed"] = [$data["is_completed"], PDO::PARAM_BOOL]; 
        }

        foreach ($fields as $column=>$value) {
            $column_str .= " $column = :$column,"; 
        }   
        $column_str = trim($column_str, ",");
        $sql = "UPDATE task SET $column_str WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $task_id, PDO::PARAM_INT);
        foreach ($fields as $column=>$value) {
            $stmt->bindValue(":$column", $value[0], $value[1]);
        }

        $stmt->execute();
        return $stmt->rowCount();
    }

    public function updateTaskForUser(int $user_id, string $task_id, array $data) : int {
        $fields = [];

        if (! empty($data["name"])) {
            $fields["name"] = [$data["name"], PDO::PARAM_STR]; 
        }

        if (! empty($data["priority"])) {
            $fields["priority"] = [$data["priority"], PDO::PARAM_INT]; 
        }

        if (! empty($data["is_completed"])) {
            $fields["is_completed"] = [$data["is_completed"], PDO::PARAM_BOOL]; 
        }

        foreach ($fields as $column=>$value) {
            $column_str .= " $column = :$column,"; 
        }   
        $column_str = trim($column_str, ",");
        $sql = "UPDATE task SET $column_str WHERE id = :id AND user_id = :user_id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $task_id, PDO::PARAM_INT);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
        foreach ($fields as $column=>$value) {
            $stmt->bindValue(":$column", $value[0], $value[1]);
        }

        $stmt->execute();
        return $stmt->rowCount();
    }

    public function deleteTask(string $task_id) : int {
        $sql = "DELETE from task WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $task_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function deleteTaskForUser(int $user_id, string $task_id) : int {
        $sql = "DELETE from task WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $task_id, PDO::PARAM_INT);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }
}