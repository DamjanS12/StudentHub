<?php

function get_task($task_id) {
    global $db;
    $query = 'SELECT * FROM task
    WHERE id = :id';
    $statement = $db->prepare($query);
    $statement->bindValue(":id", $task_id);
    $statement->execute();
    $result = $statement->get_result();
    $task = $result->fetch_assoc();
    $statement->close();
    
    return $task;
}

function get_all_tasks() {
    global $db;
    $query = 'SELECT * FROM task';
    $statement = $db->prepare($query);
    $statement->execute();
    $result = $statement->get_result();
    $tasks = $result->fetch_assoc();
    $statement->close();
    
    return $tasks;
}

function delete_task($task_id) {
    global $db;
    $query = 'DELETE FROM task
    WHERE id = ?';
    $statement = $db->prepare($query);
    $statement->bind_param("i", $task_id);
    $statement->execute();
    $statement->close();
}

function add_task($title, $description, $priority, $due_date, $project_id, $created_by, $assigned_to) {
    global $db;
    $query = 'INSERT INTO task
                (title, description, priority, due_date, project_id, created_by, assigned_to)
                VALUES 
                (?, ?, ?, ?, ?, ?, ?)';
    
    $statement = $db->prepare($query);

    $statement->bind_param('ssssiis', $title, $description, $priority, $due_date, $project_id, $created_by, $assigned_to);

    $statement->execute();
    $statement->close();
}
function edit_task($task_id, $title, $description, $status, $priority, $due_date, $project_id, $assigned_to) {
    global $db;

    $query = 'UPDATE task SET title = ?, description = ?, status = ?, priority = ?, due_date = ?, project_id = ?, assigned_to = ?
              WHERE id = ?';
    
    $statement = $db->prepare($query);
    
    $statement->bind_param('ssssssii', $title, $description, $status, $priority, $due_date, $project_id, $assigned_to, $task_id);
    
    $statement->close();
}

function get_task_count_by_user($userId, $role, $status = null) {
    global $db;
    $sql = "SELECT COUNT(*) AS cnt FROM task t JOIN project p ON t.project_id = p.id ";
    if ($role === 'professor') {
        $sql .= "WHERE p.professor_id = ?";
    } else {
        $sql .= "WHERE p.created_by = ?";
    }
    if ($status) {
        $sql .= " AND t.status = ?";
    }
    $stmt = $db->prepare($sql);
    if ($status) {
        $stmt->bind_param($role === 'professor' ? "is" : "si", $userId, $status);
    } else {
        $stmt->bind_param("i", $userId);
    }
    $stmt->execute();
    return (int) $stmt->get_result()->fetch_assoc()['cnt'];
}

function get_due_tasks_today($userId, $role) {
    global $db;
    $today = date('Y-m-d');
    $sql = "SELECT COUNT(*) AS cnt FROM task t JOIN project p ON t.project_id = p.id ";
    if ($role === 'professor') {
        $sql .= "WHERE p.professor_id = ?";
    } else {
        $sql .= "WHERE p.created_by = ?";
    }
    $sql .= " AND DATE(t.due_date) = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("is", $userId, $today);
    $stmt->execute();
    return (int) $stmt->get_result()->fetch_assoc()['cnt'];
}


?>