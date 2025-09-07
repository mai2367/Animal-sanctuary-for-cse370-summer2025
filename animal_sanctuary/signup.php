<?php
require_once "db.php"; session_start(); $msg="";
if($_SERVER['REQUEST_METHOD']==='POST'){
  $u=$_POST['username']; $n=$_POST['name']; $ph=intval($_POST['phone_no']); $e=$_POST['email']; $j=$_POST['job']; $p=$_POST['password'];
  $chk=$mysqli->prepare("SELECT 1 FROM staff WHERE Username=?"); $chk->bind_param("s",$u); $chk->execute(); $chk->store_result();
  if($chk->num_rows>0){ $msg="Username already exists"; }
  else{
    $stmt=$mysqli->prepare("INSERT INTO staff (Username,name,phone_no,email,job,password,ismanager) VALUES (?,?,?,?,?,?,'No')");
    $stmt->bind_param("ssisss",$u,$n,$ph,$e,$j,$p);
    if($stmt->execute()){ header("Location: login.php"); exit; } else { $msg="Error: ".$mysqli->error; }
  }
}
include "header.php"; ?>
<div class="card">
  <h2>Sign Up</h2>
  <?php if($msg): ?><p class="alert error"><?=$msg?></p><?php endif; ?>
  <form method="post" class="grid grid-2">
    <div><label>Username</label><input required name="username"></div>
    <div><label>Name</label><input required name="name"></div>
    <div><label>Phone</label><input required name="phone_no"></div>
    <div><label>Email</label><input required type="email" name="email"></div>
    <div><label>Job Type</label><input required name="job"></div>
    <div><label>Password</label><input required type="password" name="password"></div>
    <div><button class="btn primary">Create account</button></div>
  </form>
</div>
<?php include "footer.php"; ?>
