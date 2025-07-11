<?php

include '../../models/db.php';

?>

<div class="modal-content">
  <div class="modal-header">
    <h4 class="modal-title">Edit task</h4>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
  </div>
  <div class="modal-body">
    <form method="POST" action="../../controllers/taskController.php">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" id="editTaskId" name="id">
      <div class="form-group">
        <label>Task Name</label>
        <input type="text" id="editTaskName" required name="task" class="form-control">
        <label>Description</label>
        <input type="text" id="editDescription" required name="description" class="form-control">
        <label>Status</label>
        <select id="editTaskStatus" required name="status" class="form-control">
          <option value="To Do">To Do</option>
          <option value="In Progress">In Progress</option>
          <option value="Done">Done</option>
        </select>
        <label>Priority</label>
        <select id="editTaskPriority" required name="priority" class="form-control">
          <option value="Low">Low</option>
          <option value="Medium">Medium</option>
          <option value="High">High</option>
        </select>
        <label>Due date</label>
        <input type="datetime-local" id="editDueDate" required name="due_date" class="form-control">
      </div>
      <input type="submit" name="save" value="Save" class="btn btn-success">
    </form>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
  </div>
</div>