<?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Animal Sanctuary</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<header class="topbar">
  <h1>Animal Sanctuary</h1>
  <nav>
    <?php if(isset($_SESSION['user'])): ?>
      <a href="index.php">Dashboard</a>
      <a href="animals.php">Animals</a>
      <a href="breeding.php">Breeding & Birth</a>
      <a href="health.php">Health</a>
      <a href="enclosures.php">Enclosures</a>
      <a href="inventory.php">Inventory</a>
      <a href="tasks.php">Tasks</a>
      <?php if($_SESSION['user']['ismanager']==='Yes'): ?><a href="staff.php">Staff</a><?php endif; ?>
      <a href="profile.php">My Profile</a>
      <a class="danger" href="logout.php">Logout</a>
    <?php else: ?>
      <a href="login.php">Login</a>
      <a href="signup.php">Sign Up</a>
    <?php endif; ?>
  </nav>
</header>
<main class="container">
