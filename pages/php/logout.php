<?php
// Start a session
session_start();

session_unset();

session_destroy();

// Redirect to the login page after logging out
header("Location: ../..");
exit();
?>