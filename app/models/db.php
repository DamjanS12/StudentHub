<?php

$db = new Mysqli;

$db->connect('localhost', 'root', 'new_password', 'tasktracker');

if ($db->connect_error) {
  die("Connection failed: " . $db->connect_error);
}
?>