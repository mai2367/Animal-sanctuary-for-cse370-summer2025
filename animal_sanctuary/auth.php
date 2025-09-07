<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
function require_login() { if (!isset($_SESSION['user'])) { header("Location: login.php"); exit; } }
function is_manager() { return isset($_SESSION['user']) && ($_SESSION['user']['ismanager'] === 'Yes'); }
function current_username() { return $_SESSION['user']['Username'] ?? null; }
?>
