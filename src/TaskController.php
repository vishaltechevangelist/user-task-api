<?php
namespace App;

use App\TaskGateway;

class TaskController {

    public function __construct(private TaskGateway $obj_task_gateway, private $user_id) {
    }

    public function processRequest(string $method, ?string $task_id) : void {

        if ($task_id === NULL) {
            if ($method == "GET") {
                //echo json_encode($this->obj_task_gateway->getAll());
                echo json_encode($this->obj_task_gateway->getAllTaskForUser($this->user_id));
            } elseif ($method == "POST") {
                $data = $_POST;
                $errors = $this->getValidationErrors($data, true);
                if (!empty($errors)) {
                    $this->invalidRequest($errors);
                } else {
                    //$task_id = $this->obj_task_gateway->saveTask($data);
                    $task_id = $this->obj_task_gateway->saveTaskForUser($this->user_id, $data);
                    $this->resourceCreated($task_id);    
                }
            } else {
                $this->responseMethodNotAllowed("GET, POST");
            }
        } else {
            //$task = $this->obj_task_gateway->getTaskById($task_id);
            $task = $this->obj_task_gateway->getTaskByIdForUser($this->user_id, $task_id);
            if ($task === FALSE) {
                $this->resourceNotFound($task_id);
                return;
            }
            
            if ($method == "GET") {
                echo json_encode($task);
            } elseif ($method == "PATCH") {

                $data = array();
                parse_str(file_get_contents('php://input'), $data);

                $errors = $this->getValidationErrors($data);
                
                if (!empty($errors)) {
                    $this->invalidRequest($errors);
                } else {
                    //$rows = $this->obj_task_gateway->updateTask($task_id, $data);
                    $rows = $this->obj_task_gateway->updateTaskForUser($this->user_id, $task_id, $data);
                    if ($rows>0) {
                        echo json_encode(["message" => "Task is updated", "rows" => $rows]);
                    } else {

                    }
                }
            } elseif ($method == "DELETE") {
                // $rows = $this->obj_task_gateway->deleteTask($task_id);
                $rows = $this->obj_task_gateway->deleteTaskForUser($this->user_id, $task_id);
                echo json_encode(["message"=>"Task is deleted", "rows"=>$rows]);
            } else {
                $this->responseMethodNotAllowed("GET, PATCH, DELETE");
            }
        }
    }

    private function responseMethodNotAllowed(string $methodNotAllowed):void {
        http_response_code(405);
        header("Allow: $methodNotAllowed");
    }

    private function resourceNotFound(string $task_id) : void {
        http_response_code(404);
        echo json_encode(["message"=>"Task with $task_id is not found"]);
    }

    private function resourceCreated(string $task_id) {
        http_response_code(201);
        echo json_encode(["message" => "Task {$task_id} is created"]);
    }

    private function getValidationErrors(array $data, $is_new = FALSE) : array {
        $errors = array();
        if($is_new && empty($data["name"])) {
            $errors[] = "Task name is required";
        }
        if (!empty($data['priority']) && !is_numeric($data['priority'])) {
            $errors[] = "Task priority should be integer";
        }
        return $errors;
    }

    private function invalidRequest($errors) : void {
        http_response_code(422);
        echo json_encode(["errors" => $errors]);
    }
}