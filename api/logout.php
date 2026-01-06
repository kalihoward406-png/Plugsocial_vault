<?php
session_start();
session_unset(); // Clears variables
session_destroy(); // Kills the session
header("Location: login.php");
exit();
?>
