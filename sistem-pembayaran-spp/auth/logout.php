<?php
/**
 * File logout.php
 * Menghapus session login dan redirect ke halaman login
 */

session_start();
session_destroy();

// Redirect ke halaman login
header("Location: login.php");
exit;
?>

