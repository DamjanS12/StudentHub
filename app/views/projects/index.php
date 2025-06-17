<?php 
include '../../models/db.php';
include '../../models/project.php';
include '../../models/user.php';

session_start(); 

$projectName = filter_input(INPUT_GET, 'projectName', FILTER_SANITIZE_STRING);
$createdBy = filter_input(INPUT_GET, 'createdBy', FILTER_SANITIZE_STRING); 

$sql = "SELECT * FROM project WHERE 1=1";

if ($projectName) {
    $sql .= " AND name LIKE '%" . $db->real_escape_string($projectName) . "%'";
}

if ($createdBy) {
    $sql .= " AND created_by = '" . $db->real_escape_string($createdBy) . "'";
}
$rows = $db->query($sql);
?>

<?php include '../header.php'; ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<div class="container">
  <div class="column">
    <div class="row" style="margin-top: 70px;">
      <div>
        <?php if (isset($_SESSION['username']) && $_SESSION['role'] === 'student'): ?>
          <button type="button" data-target="#addProject" data-toggle="modal" class="btn btn-success">
            <i class="bi bi-plus-lg me-2"></i> Add Project
          </button>
        <?php endif; ?>

        <div id="addProject" class="modal fade" role="dialog">
          <div class="modal-dialog">
            <?php
            $professor_result = $db->query("SELECT id, first_name, last_name FROM user WHERE role = 'Professor'");
            $professors = [];
            while ($prof = $professor_result->fetch_assoc()) {
              $professors[] = $prof;
            }
            include 'add.php';
            ?>
          </div>
        </div>

        <table class="table">
          <hr>
          <form action="" method="GET">
            <div class="form-group">
              <label for="name">Name:</label>
              <input type="text" id="name" class="form-control" name="projectName" placeholder="Project Name">
            </div>
            <div class="form-group">
              <label for="created_by">Created By:</label>
              <input type="text" id="created_by" name="createdBy" class="form-control" placeholder="Created By">
            </div>
            <button type="submit" class="btn btn-outline-info">Filter</button>
          </form>

          <table class="table">
            <thead>
              <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Description</th>
                <th>Assigned tasks</th>
                <th>Documentation</th>
                <th>Created by</th>
                <th>Created at</th>
                <th></th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php while($row = $rows->fetch_assoc()): ?>
                <tr>
                  <td><?php echo $row['id'] ?></td>
                  <td><?php echo htmlspecialchars($row['name']) ?></td>
                  <td><?php echo htmlspecialchars($row['description']) ?></td>
                  <td>
                    <?php 
                      $tasks = get_tasks_by_project($row['id']);
                      if (!empty($tasks)) {
                          $taskTitles = implode(', ', array_map(function($task) {
                              return htmlspecialchars($task['title']);
                          }, $tasks));
                          echo $taskTitles;
                      } else {
                          echo "No tasks found.";
                      }
                    ?>
                  </td>
                  <td>
                    <?php if (!empty($row['file_path'])): ?>
                      <a href="../../controllers/download.php?file_path=<?php echo urlencode($row['file_path']); ?>" download>Download</a>
                    <?php else: ?>
                      <span>No file</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php 
                      $user = get_user($row['created_by']);
                      echo htmlspecialchars($user['first_name'] . " " . $user['last_name']);
                    ?>
                  </td>
                  <td><?php echo $row['created_at'] ?></td>

                 
                  <td>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['created_by']): ?>
                      <button type="button" class="btn btn-success" data-toggle="modal" data-target="#editProject" onclick="populateEditModal(
                          <?php echo $row['id']; ?>,
                          '<?php echo htmlspecialchars($row['name']); ?>',
                          '<?php echo htmlspecialchars($row['description']); ?>',
                          '<?php echo htmlspecialchars($row['created_by']); ?>'
                      )">
                        <i class="bi bi-pencil me-2"></i> Edit
                      </button>
                    <?php endif; ?>
                  </td>

                  
                  <td>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['created_by']): ?>
                      <form action="../../controllers/projectController.php" method="POST">
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
            </tbody>
          </table>
        </table>
      </div>
    </div>
  </div>

  <div id="editProject" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <?php include 'edit.php'; ?>
    </div>
  </div>

</div>

<script>
  function populateEditModal(projectId, name, description, created_by) {
    document.getElementById('editProjectId').value = projectId;
    document.getElementById('editProjectName').value = name;
    document.getElementById('editProjectDescription').value = description;
    document.getElementById('editProjectCreatedBy').value = created_by;
  }
</script>

<?php include '../footer.php'; ?>
