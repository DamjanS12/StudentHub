<div class="modal-content">
  <div class="modal-header">
    <h4 class="modal-title">Add task</h4>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
  </div>
  <div class="modal-body">
    <form method="POST" action="../../controllers/taskController.php">
      <input type="hidden" name="action" value="add">
      <div class="form-group">
        <label>Task Name</label>
        <input type="text" required name="task" class="form-control">

        <label>Description</label>
        <input type="text" required name="description" class="form-control">

        <label>Priority</label>
        <select name="priority" class="form-control">
          <option value="Low">Low</option>
          <option value="Medium">Medium</option>
          <option value="High">High</option>
        </select>

        <label>Due date</label>
        <input type="datetime-local" required name="due_date" class="form-control">

        <label>Project</label>
        <select required name="project" class="form-control" id="project-select">
          <option value="" disabled selected>Select a project</option>
          <?php while ($project = $projects->fetch_assoc()) { ?>
            <option value="<?php echo $project['id']; ?>">
              <?php echo htmlspecialchars($project['name']); ?>
            </option>
          <?php } ?>
        </select>

        <!-- Hidden input for "Assign to" -->
        <input type="hidden" name="assigned_to" id="assigned_to">

        <!-- Optional: Show student's name (read-only) -->
        <label>Assigned to (Student)</label>
        <input type="text" id="assigned_to_display" class="form-control" disabled placeholder="Select a project first">
      </div>

      <input type="submit" name="add" value="Add task" class="btn btn-success">
    </form>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
  </div>
</div>

<!-- JavaScript for dynamic assignment -->
<script>
document.getElementById('project-select').addEventListener('change', function () {
    const projectId = this.value;

    if (!projectId) return;

    fetch(`../../controllers/projectStudentLookup.php?project_id=${projectId}`)
        .then(response => response.json())
        .then(data => {
            if (data && data.created_by_id && data.username) {
                document.getElementById('assigned_to').value = data.created_by_id;
                document.getElementById('assigned_to_display').value = data.username;
            } else {
                alert("Could not find student for selected project.");
                document.getElementById('assigned_to').value = '';
                document.getElementById('assigned_to_display').value = '';
            }
        })
        .catch(error => {
            console.error("Error fetching student for project:", error);
            document.getElementById('assigned_to').value = '';
            document.getElementById('assigned_to_display').value = '';
        });
});
</script>
