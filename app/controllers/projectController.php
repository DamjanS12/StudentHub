<?php
require('../models/db.php');  
require('../models/project.php');  

$action = filter_input(INPUT_POST, 'action');

if (isset($_GET['file_path'])) {
    $file_path = urldecode($_GET['file_path']); 

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_path));

    ob_clean();
    flush();
    readfile($file_path);
    exit;
}

if ($action == 'add') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $professor_id = $_POST['professor_id'] ?? null;
    $created_by = $_COOKIE['user_id'];
    


    if (empty($name) || empty($description) || empty($professor_id)) {
        echo "All fields are required.";
        exit;
    }

    $file_path = null;
if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
    $target_dir = __DIR__ . '/../../uploads/';
    $filename = basename($_FILES['file']['name']);
    $target_file = $target_dir . $filename;
    $uploadOk = 1;

    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0775, true);
    }

    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    if ($_FILES['file']['size'] > 2000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    $allowed = ['jpg', 'png', 'pdf', 'docx'];
    $ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        echo "Only JPG, PNG, PDF & DOCX files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk && move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
        $file_path = '../../uploads/' . $filename; 
        echo "File uploaded successfully.";
    } else {
        echo "Failed to upload file.";
    }
} else {
    echo "No file uploaded or there was an error.";
}


    add_project($name, $description, $created_by, $file_path, $professor_id);

    
    header("Location: ../views/projects/index.php");
    exit;

} elseif ($action == 'delete') {

        $id = $_POST['id'];
        delete_project($id);
        header("Location: ../views/projects/index.php");
        exit;
    
} elseif ($action == "edit") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    
    edit_project($id, $name, $description);
    header("Location: ../views/projects/index.php");
} elseif ($action == 'filter') {
    echo "<pre>";
    print_r($_GET);
    echo "</pre>";

    $projectName = filter_input(INPUT_GET, 'projectName', FILTER_SANITIZE_STRING);
    $createdBy = filter_input(INPUT_GET, 'createdBy', FILTER_SANITIZE_STRING);
    

    $sql = "SELECT * FROM task WHERE 1=1";
    $params = [];
    $types = '';

    if (!empty($projectName)) {
        $sql .= " AND title LIKE ?";
        $params[] = "%$projectName%";
        $types .= 's';
    }

    if (!empty($createdBy)) {
        $sql .= " AND created_by = ?";
        $params[] = $createdBy;
        $types .= 'i';
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

        include '../views/projects/index.php';
        exit;
    }
}
 else {
    echo "Invalid action";
}
?>
