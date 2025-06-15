<?php
require_once(__DIR__ . '/../models/db.php');

$project_id = $_GET['project_id'] ?? null;

if (!$project_id) {
    echo json_encode(['error' => 'Project ID missing']);
    exit;
}

$query = "SELECT u.id AS created_by_id, u.username 
          FROM project p 
          JOIN user u ON p.created_by = u.id 
          WHERE p.id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();

if ($data = $result->fetch_assoc()) {
    echo json_encode($data);
} else {
    echo json_encode(['error' => 'Project not found']);
}
