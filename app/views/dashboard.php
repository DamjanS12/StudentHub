<?php
session_start();
require_once(__DIR__ . '/../../app/models/db.php');
require_once(__DIR__ . '/../../app/models/task.php');
require_once(__DIR__ . '/../../app/models/project.php');


if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

$userId = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Fetch stats
$totalTasks = get_task_count_by_user($userId, $role);
$completedTasks = get_task_count_by_user($userId, $role, 'completed');
$dueToday = get_due_tasks_today($userId, $role);
$totalProjects = get_project_count_by_user($userId, $role);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard – StudentHub</title>
  <link rel="stylesheet" href="/css/style.css">
</head>
<body>
  <?php include __DIR__ . '/header.php'; ?>

  <div class="container mt-5">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
    <p>Role: <?= htmlspecialchars(ucfirst($role)) ?></p>

    <div class="card-deck mt-4">
      <div class="card">
        <div class="card-body">
          <h5>Total Tasks</h5>
          <h2><?= $totalTasks ?></h2>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <h5>Completed Tasks</h5>
          <h2><?= $completedTasks ?></h2>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <h5>Due Today</h5>
          <h2><?= $dueToday ?></h2>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <h5>Total Projects</h5>
          <h2><?= $totalProjects ?></h2>
        </div>
      </div>
    </div>

    <div class="mt-4">
      <a href="tasks/index.php" class="btn">Go to Tasks</a>
      <a href="projects/index.php" class="btn secondary">Go to Projects</a>
    </div>
  </div>

  <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
