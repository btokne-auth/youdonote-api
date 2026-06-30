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
        $stmt = $conn->prepare("INSERT INTO tasks (title, description, milestone_id, priority, category_tag, estimated_time, mental_effort, due_date, due_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssississs",
            $data['title'], $data['description'], $data['milestone_id'],
            $data['priority'], $data['category_tag'], $data['estimated_time'],
            $data['mental_effort'], $data['due_date'], $data['due_time']
        );
        $stmt->execute();
        echo json_encode(["id" => $stmt->insert_id, "message" => "Task created"]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $id = intval($data['id']);
        $stmt = $conn->prepare("UPDATE tasks SET title=?, description=?, milestone_id=?, priority=?, category_tag=?, estimated_time=?, mental_effort=?, due_date=?, due_time=?, is_completed=? WHERE id=?");
        $stmt->bind_param("ssississsii",
            $data['titles'], $data['description'], $data['milestone_id'],
            $data['priority'], $data['category_tag'], $data['estimated_time'],
            $data['mental_effort'], $data['due_date'], $data['due_time'],
            $data['is_completed'], $id
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
