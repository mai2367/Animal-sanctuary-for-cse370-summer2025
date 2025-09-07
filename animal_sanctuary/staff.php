<?php
require_once "db.php"; require_once "auth.php"; require_login();
if(!is_manager()){ include "header.php"; echo "<p class='alert error'>Only managers can access this page.</p>"; include "footer.php"; exit; }
$msg="";
if(isset($_POST['promote'])){
  $u=$mysqli->real_escape_string($_POST['username']);
  if($mysqli->query("UPDATE staff SET ismanager='Yes' WHERE Username='$u'")) $msg="Promoted $u to manager.";
}
$staff=$mysqli->query("SELECT Username,name,job,ismanager FROM staff ORDER BY name");
include "header.php"; ?>
<div class="card">
  <h2>Staff</h2>
  <?php if($msg): ?><p class="alert success"><?=$msg?></p><?php endif; ?>
  <div class="scrollbox compact">
  <table>
    <thead><tr><th>Name</th><th>Username</th><th>Job</th><th>Manager</th><th>Actions</th></tr></thead>
    <tbody>
      <?php while($s=$staff->fetch_assoc()): ?>
        <tr>
          <td><?=$s['name']?></td><td><?=$s['Username']?></td><td><?=$s['job']?></td><td><?=$s['ismanager']?></td>
          <td class="flex"><?php if($s['ismanager']!=='Yes'): ?><form method="post"><input type="hidden" name="username" value="<?=$s['Username']?>"><button class="btn warn" name="promote">Make Manager</button></form><?php endif; ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  </div>
</div>
<?php include "footer.php"; ?>
