<?php 
include '../../models/db.php'; 
include '../../models/project.php'; 
include '../../models/user.php';
session_start();

$taskName = filter_input(INPUT_GET, 'taskName', FILTER_SANITIZE_STRING);
$dueDate = filter_input(INPUT_GET, 'dueDate', FILTER_SANITIZE_STRING);
$priority = filter_input(INPUT_GET, 'priority', FILTER_SANITIZE_STRING);

$sql = "SELECT * FROM task WHERE 1=1";

if ($taskName) {
    $sql .= " AND title LIKE '%" . $db->real_escape_string($taskName) . "%'";
}
if ($dueDate) {
    $sql .= " AND due_date = '" . $db->real_escape_string($dueDate) . "'";
}
if ($priority) {
    $sql .= " AND priority = '" . $db->real_escape_string($priority) . "'";
}

$rows = $db->query($sql);
?>

<?php
$sql2 = "SELECT name, id FROM project";
$projects = $db->query($sql2);

$sql3 = "SELECT username, id FROM user";
$users = $db->query($sql3);

if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error_message'] . '</div>';
    unset($_SESSION['error_message']);
}
?>

<?php include '../header.php'; ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<div class="container">
    <div class="column">
        <div class="row" style="margin-top: 70px;">
            <div>
            <?php
                if (isset($_SESSION['username']) && $_SESSION['role'] === 'professor'){
                    echo '<button type="button" data-target="#addTask" data-toggle="modal" class="btn btn-success">
                    <i class="bi bi-plus-lg"></i> Add Task
                    </button>';
                }
            ?>
                <hr>
                <form action="" method="GET">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" id="name" class="form-control" name="taskName" placeholder="Task Name" value="<?php echo htmlspecialchars($taskName); ?>">
                    </div>

                    <div class="form-group">
                        <label for="priority">Priority:</label>
                        <select id="priority" class="form-control" name="priority">
                            <option value="">Select Priority</option>
                            <option value="Low" <?php echo ($priority == 'Low') ? 'selected' : ''; ?>>Low</option>
                            <option value="Medium" <?php echo ($priority == 'Medium') ? 'selected' : ''; ?>>Medium</option>
                            <option value="High" <?php echo ($priority == 'High') ? 'selected' : ''; ?>>High</option>
                        </select>
                    </div>
                    <input type="hidden" name="action" value="filter">
                    <button type="submit" class="btn btn-outline-info">Filter</button>
                </form>

                <div id="addTask" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                        <?php include 'add.php'; ?>
                    </div>
                </div>

                <div id="editTask" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                        <?php include 'edit.php'; ?>
                    </div>
                </div>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Due date</th>
                            <th>Project</th>
                            <th>Created by</th>
                            <th>Assigned to</th>
                            <th>Priority</th>
                            <th>Created at</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($rows->num_rows > 0): ?>
                            <?php while($row = $rows->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id'] ?></td>
                                    <td class="col-2"><?php echo htmlspecialchars($row['title']) ?></td>
                                    <td class="col-4"><?php echo htmlspecialchars($row['description']) ?></td>
                                    <td><?php echo htmlspecialchars($row['status']) ?></td>
                                    <td><?php echo htmlspecialchars($row['due_date']) ?></td>
                                    <td>
                                        <?php $project = get_project($row['project_id']); 
                                        echo htmlspecialchars($project['name']); ?>
                                    </td>
                                    <td>
                                        <?php $creator = get_user($row['created_by']); 
                                        echo htmlspecialchars($creator['first_name'] . ' ' . $creator['last_name']); ?>
                                    </td>
                                    <td>
                                        <?php $assignee = get_user($row['assigned_to']); 
                                        echo htmlspecialchars($assignee['first_name'] . ' ' . $assignee['last_name']); ?>
                                    </td>
                                    <td style="background-color: 
                                        <?php 
                                            if ($row['priority'] == 'Low') echo '#fff176';
                                            elseif ($row['priority'] == 'Medium') echo '#ffb74d';
                                            elseif ($row['priority'] == 'High') echo '#ff8a65';
                                            else echo 'transparent'; ?>">
                                        <?php echo htmlspecialchars($row['priority']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>

                                    
                                    <td>
                                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['created_by']): ?>
                                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#editTask" onclick="populateEditModal(
                                                <?php echo $row['id']; ?>, 
                                                '<?php echo htmlspecialchars($row['title']); ?>', 
                                                '<?php echo htmlspecialchars($row['description']); ?>', 
                                                '<?php echo htmlspecialchars($row['status']); ?>',
                                                '<?php echo htmlspecialchars($row['priority']); ?>',
                                                '<?php echo htmlspecialchars($row['due_date']); ?>',
                                                <?php echo $row['project_id']; ?>,
                                                <?php echo $row['assigned_to']; ?>
                                            )">
                                            <i class="bi bi-pencil me-2"></i> Edit
                                            </button>
                                        <?php endif; ?>
                                    </td>

                                    
                                    <td> 
                                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['created_by']): ?>
                                            <form action="../../controllers/taskController.php" method="POST">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" class="btn btn-danger">
                                                    <i class="bi bi-trash3"></i> Delete
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="12">No tasks found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function populateEditModal(taskId, title, description, status, priority, dueDate, projectId, assignedTo) {
        document.getElementById('editTaskId').value = taskId;
        document.getElementById('editTaskName').value = title;
        document.getElementById('editDescription').value = description;
        document.getElementById('editTaskStatus').value = status;
        document.getElementById('editTaskPriority').value = priority;
        document.getElementById('editDueDate').value = dueDate;

        document.getElementById('editProject').value = projectId;
        document.getElementById('editAssignedTo').value = assignedTo;
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

<?php include '../footer.php'; ?>
