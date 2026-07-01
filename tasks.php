<?php
require 'db.php';
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $result = $conn->query("SELECT * FROM tasks WHERE id = $id");
            echo json_encode($result->fetch_assoc());
        } else {
            $result = $conn->query("SELECT * FROM tasks ORDER BY due_date ASC");
            $tasks = [];
            while ($row = $result->fetch_assoc()) $tasks[] = $row;
            echo json_encode($tasks);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data || !isset($data['title'])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing required field: title"]);
            break;
        }

        $title         = $data['title'];
        $description   = $data['description'] ?? null;
        $milestone_id  = isset($data['milestone_id']) ? intval($data['milestone_id']) : null;
        $priority      = $data['priority'] ?? 'Low';
        $category_tag  = $data['category_tag'] ?? null;
        $estimated_time = intval($data['estimated_time'] ?? 1);
        $mental_effort = $data['mental_effort'] ?? 'Light';
        $due_date      = $data['due_date'] ?? null;
        $due_time      = $data['due_time'] ?? null;

        $stmt = $conn->prepare("INSERT INTO tasks (title, description, milestone_id, priority, category_tag, estimated_time, mental_effort, due_date, due_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssississs",
            $title, $description, $milestone_id,
            $priority, $category_tag, $estimated_time,
            $mental_effort, $due_date, $due_time
        );
        $stmt->execute();
        echo json_encode(["id" => $stmt->insert_id, "message" => "Task created"]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
    
        if (!$data || !isset($data['id']) || !isset($data['title'])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing required fields", "received" => $data]);
            break;
        }
    
        $id             = intval($data['id']);
        $title          = $data['title'];
        $description    = $data['description'] ?? null;
        $milestone_id   = isset($data['milestone_id']) ? intval($data['milestone_id']) : null;
        $priority       = $data['priority'] ?? 'Low';
        $category_tag   = $data['category_tag'] ?? null;
        $estimated_time = intval($data['estimated_time'] ?? 1);
        $mental_effort  = $data['mental_effort'] ?? 'Light';
        $due_date       = $data['due_date'] ?? null;
        $due_time       = $data['due_time'] ?? null;
        $is_completed   = isset($data['is_completed']) ? ($data['is_completed'] ? 1 : 0) : 0;
    
        // FIX: set completed_at to current timestamp when marking done, clear it when uncompleting
        $stmt = $conn->prepare("UPDATE tasks SET title=?, description=?, milestone_id=?, priority=?, category_tag=?, estimated_time=?, mental_effort=?, due_date=?, due_time=?, is_completed=?, completed_at=? WHERE id=?");
        $completed_at = $is_completed ? date('Y-m-d H:i:s') : null;
        $stmt->bind_param("ssississsisi",
            $title, $description, $milestone_id,
            $priority, $category_tag, $estimated_time,
            $mental_effort, $due_date, $due_time,
            $is_completed, $completed_at, $id
        );
        $stmt->execute();
        echo json_encode(["message" => "Task updated"]);
        break;

    case 'DELETE':
        $id = intval($_GET['id']);
        $conn->query("DELETE FROM tasks WHERE id = $id");
        echo json_encode(["message" => "Task deleted"]);
        break;
}
$conn->close();
?>
