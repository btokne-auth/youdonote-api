<?php
require 'db.php';
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $result = $conn->query("SELECT * FROM milestones WHERE id = $id");
            $milestone = $result->fetch_assoc();

            if ($milestone) {
                // include task counts so the app can show progress %, done/pending
                $taskStats = $conn->query("SELECT 
                    COUNT(*) AS total_tasks,
                    SUM(is_completed = 1) AS done_tasks,
                    SUM(is_completed = 0) AS pending_tasks
                    FROM tasks WHERE milestone_id = $id")->fetch_assoc();

                $milestone['total_tasks'] = (int)$taskStats['total_tasks'];
                $milestone['done_tasks'] = (int)$taskStats['done_tasks'];
                $milestone['pending_tasks'] = (int)$taskStats['pending_tasks'];
            }

            echo json_encode($milestone);
        } else {
            $result = $conn->query("SELECT * FROM milestones ORDER BY due_date ASC");
            $milestones = [];
            while ($row = $result->fetch_assoc()) $milestones[] = $row;
            echo json_encode($milestones);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $conn->prepare("INSERT INTO milestones (name, due_date) VALUES (?, ?)");
        $stmt->bind_param("ss",
            $data['name'], $data['due_date']
        );
        $stmt->execute();
        echo json_encode(["id" => $stmt->insert_id, "message" => "Milestone created"]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $id = intval($data['id']);
        $stmt = $conn->prepare("UPDATE milestones SET name=?, due_date=? WHERE id=?");
        $stmt->bind_param("ssi",
            $data['name'], $data['due_date'], $id
        );
        $stmt->execute();
        echo json_encode(["message" => "Milestone updated"]);
        break;

    case 'DELETE':
        $id = intval($_GET['id']);
        // tasks.milestone_id has ON DELETE SET NULL, so this is safe —
        // tasks under this milestone just become unassigned instead of erroring
        $conn->query("DELETE FROM milestones WHERE id = $id");
        echo json_encode(["message" => "Milestone deleted"]);
        break;
}

$conn->close();
?>
