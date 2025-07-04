<?php
require_once(__DIR__ . '/../models/db.php');
require(__DIR__ . '/../models/task.php');  
require(__DIR__ . '/../models/project.php');
require(__DIR__ . '/../models/user.php');
require(__DIR__ . '/../libraries/taskLibrary.php');
require_once(__DIR__ . '/../../send_email.php');

$action = filter_input(INPUT_POST, 'action');

class taskController {

    public function index() {
        session_start();

    if (!isset($_SESSION['username'])) {
        echo $_SESSION['username'];
        header("Location: app/views/login_register/login_index.php");
        exit();
    }
        header("Location: app/views/tasks/index.php");
    }
}

$controller = new taskController();

if ($action == 'add') {
    session_start();

    $title = $_POST['task'];
    $description = $_POST['description'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];
    $project_id = $_POST['project'];
    $query = "SELECT created_by FROM project WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $assigned_to = $row['created_by']; 
     } else {
         $_SESSION['error_message'] = "Selected project does not exist.";
        header("Location: ../views/tasks/index.php");
        exit;
}
    $professor_id = $_COOKIE['user_id'];  

    if (!TaskLibrary::validateDates($due_date)) {
        $_SESSION['error_message'] = 'Invalid due date. Please select a future date.';
        header("Location: ../views/tasks/index.php");
        exit;
    }

    if (!project_exists($project_id)) {
        $_SESSION['error_message'] = "Project does not exist.";
        header("Location: ../views/tasks/index.php");
        exit;
    }

    
    $query = "SELECT professor_id FROM project WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $project = $result->fetch_assoc();

    if (!$project || $project['professor_id'] != $professor_id) {
        $_SESSION['error_message'] = "You are not assigned to this project.";
        header("Location: ../views/tasks/index.php");
        exit;
    }

    // If allowed, add task
    add_task($title, $description, $priority, $due_date, $project_id, $professor_id, $assigned_to);

    $task = [
        'title' => $title,
        'description' => $description,
        'due_date' => $due_date
    ];

    $email = get_user_email($assigned_to);
    send_email($task, $email);

    header("Location: ../views/tasks/index.php");

} elseif($action == 'delete') {
    $id = $_POST['id'];

    delete_task($id);

    header("Location: ../views/tasks/index.php");
} elseif($action == "edit") {
    $id = $_POST['id'];
    $title = $_POST['task'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];
    $project_id = $_POST['project'];
    $assigned_to = $_POST['assigned_to'];

    if (!TaskLibrary::validateDates($due_date)) {
        $_SESSION['error_message'] = 'Invalid due date. Please select a future date.';
        header("Location: ../views/tasks/index.php");
        exit;
    }

    edit_task($id, $title, $description, $status, $priority, $due_date, $project_id, $assigned_to);

    header("Location: ../views/tasks/index.php");

} elseif ($action == 'filter') {

    $taskName = filter_input(INPUT_GET, 'taskName', FILTER_SANITIZE_STRING);
    $priority = filter_input(INPUT_GET, 'priority', FILTER_SANITIZE_STRING);

    $sql = "SELECT * FROM task WHERE 1=1";
    $params = [];
    $types = '';

    if (!empty($taskName)) {
        $sql .= " AND title LIKE ?";
        $params[] = "%$taskName%";
        $types .= 's';
    }

    if (!empty($priority)) {
        $sql .= " AND status = ?";
        $params[] = $priority;
        $types .= 's';
    }

    $statement = $db->prepare($sql);
    if (!empty($params)) {
        $statement->bind_param($types, ...$params);
    }

    if (!$statement->execute()) {
        echo "SQL error: " . $statement->error;
    } else {
        $result = $statement->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);

        if (empty($rows)) {
            echo "No tasks found.";
        }

        include '../views/tasks/index.php';
        exit;
    }
} else echo "fail";