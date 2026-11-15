<?php
session_start();
session_unset();    // Remove all session variables
session_destroy();  // Destroy the session

// Redirect to login or homepage
echo "<script>alert('You have been logged out.'); window.location.href='login.php';</script>";
exit;
?>