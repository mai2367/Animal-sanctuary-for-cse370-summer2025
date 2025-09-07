<?php
require_once "db.php"; session_start();
$msg = "";
if ($_SERVER['REQUEST_METHOD']==='POST'){
  $u = $_POST['username'] ?? '';
  $p = $_POST['password'] ?? '';
  $stmt=$mysqli->prepare("SELECT Username,name,phone_no,email,job,password,ismanager FROM staff WHERE Username=? AND password=?");
  $stmt->bind_param("ss",$u,$p); $stmt->execute(); $res=$stmt->get_result();
  if($row=$res->fetch_assoc()){ $_SESSION['user']=$row; header("Location: index.php"); exit; } else { $msg="Invalid username or password"; }
}
include "header.php";
?>
<div class="card">
  <h2>Login</h2>
  <?php if($msg): ?><p class="alert error"><?=$msg?></p><?php endif; ?>
  <form method="post" class="grid grid-2">
    <div><label>Username</label><input required name="username"></div>
    <div><label>Password</label><input required type="password" name="password"></div>
    <div><button class="btn primary">Login</button></div>
    <div class="note">No account? <a href="signup.php">Sign up</a></div>
  </form>
</div>
<?php include "footer.php"; ?>
