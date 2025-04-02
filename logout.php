<?php
session_start();
session_unset(); // Delete all session variables
session_destroy(); // Destroy the session
header("Location: index.php"); // head to index.php
exit();
?> 