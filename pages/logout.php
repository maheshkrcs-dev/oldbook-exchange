<?php
session_start();

/* Destroy session */
$_SESSION = [];
session_unset();
session_destroy();

/* Redirect to home */
header("Location: ../index.php");
exit;