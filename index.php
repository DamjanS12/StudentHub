<?php
session_start();

// If the user is already logged in, redirect to tasks page
if (isset($_SESSION['user_id'])) {
    header("Location: app/views/tasks/index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Hub - Home</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container text-center mt-5">
    <h1 class="display-4">Welcome to Student Hub</h1>
    <p class="lead mt-3">
        Organize your tasks, manage your projects, and collaborate with ease.<br>
        Please login or sign up to get started.
    </p>

    <div class="mt-4">
        <a href="app/views/login_register/login_index.php" class="btn btn-primary btn-lg mr-2">Login</a>
        <a href="app/views/login_register/register_index.php" class="btn btn-success btn-lg">Sign Up</a>
    </div>
</div>

</body>
</html>
