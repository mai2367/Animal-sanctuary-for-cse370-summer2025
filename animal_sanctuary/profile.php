<?php
require_once "db.php"; require_once "auth.php"; require_login();
$u=current_username(); $msg="";
if($_SERVER['REQUEST_METHOD']==='POST'){
  $n=$_POST['name']; $ph=intval($_POST['phone_no']); $e=$_POST['email']; $j=$_POST['job']; $p=$_POST['password'];
  $stmt=$mysqli->prepare("UPDATE staff SET name=?, phone_no=?, email=?, job=?, password=? WHERE Username=?");
  $stmt->bind_param("sissss",$n,$ph,$e,$j,$p,$u);
  if($stmt->execute()){ $_SESSION['user']['name']=$n; $_SESSION['user']['phone_no']=$ph; $_SESSION['user']['email']=$e; $_SESSION['user']['job']=$j; $_SESSION['user']['password']=$p; $msg="Profile updated."; }
}
$me=$mysqli->query("SELECT * FROM staff WHERE Username='".$mysqli->real_escape_string($u)."'")->fetch_assoc();
include "header.php"; ?>
<div class="card">
  <h2>My Profile</h2>
  <?php if($msg): ?><p class="alert success"><?=$msg?></p><?php endif; ?>
  <form method="post" class="grid grid-2">
    <div><label>Username</label><input disabled value="<?=$me['Username']?>"><div class="form-help">Username cannot be changed.</div></div>
    <div><label>Name</label><input name="name" value="<?=$me['name']?>"></div>
    <div><label>Phone</label><input name="phone_no" value="<?=$me['phone_no']?>"></div>
    <div><label>Email</label><input type="email" name="email" value="<?=$me['email']?>"></div>
    <div><label>Job</label><input name="job" value="<?=$me['job']?>"></div>
    <div><label>Password</label><input type="password" name="password" value="<?=$me['password']?>"></div>
    <div><button class="btn primary">Save</button></div>
  </form>
</div>
<?php include "footer.php"; ?>
