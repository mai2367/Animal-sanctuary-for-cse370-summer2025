<?php
require_once "db.php"; require_once "auth.php"; require_login();
$u = current_username(); $isMgr = is_manager();

$myShifts=[]; $stmt=$mysqli->prepare("SELECT st.period, st.time_slot FROM staff_shift_map ssm JOIN shift_types st ON ssm.shift_type_id=st.shift_type_id WHERE ssm.username=? ORDER BY st.period, st.time_slot");
$stmt->bind_param("s",$u); $stmt->execute(); $res=$stmt->get_result(); while($row=$res->fetch_assoc()) $myShifts[]=$row;
$myFeed=$mysqli->prepare("SELECT a.animal_id,a.Name,st.period,st.time_slot FROM feed_assignments fa JOIN animal a ON a.animal_id=fa.animal_id JOIN shift_types st ON st.shift_type_id=fa.shift_type_id WHERE fa.username=? ORDER BY st.period,st.time_slot,a.Name");
$myFeed->bind_param("s",$u); $myFeed->execute(); $feedRes=$myFeed->get_result();
$myClean=$mysqli->prepare("SELECT e.NAME enclosure_name,st.period,st.time_slot FROM clean_assignments ca JOIN enclosure e ON e.enclosure_id=ca.enclosure_id JOIN shift_types st ON st.shift_type_id=ca.shift_type_id WHERE ca.username=? ORDER BY st.period,st.time_slot,e.NAME");
$myClean->bind_param("s",$u); $myClean->execute(); $cleanRes=$myClean->get_result();
include "header.php"; ?>
<div class="grid grid-2">
  <div class="card">
    <h2>Welcome, <?=$_SESSION['user']['name']?> 👋</h2>
    <p>Your role: <span class="badge"><?=$_SESSION['user']['ismanager']==='Yes'?'Manager':'Staff'?></span></p>
    <h3>My Shifts</h3>
    <?php if(empty($myShifts)): ?><p class="note">No shifts assigned yet.</p>
    <?php else: ?><div class="flex"><?php foreach($myShifts as $s): ?><span class="badge tag"><?=$s['period']?> <?=$s['time_slot']?></span><?php endforeach;?></div><?php endif; ?>
    <hr class="sep">
    <h3>My Tasks</h3>
    <h4>Feeding</h4>
    <?php if($feedRes->num_rows===0): ?><p class="note">No feeding tasks.</p><?php else: ?>
    <div class="scrollbox compact"><table><thead><tr><th>Period</th><th>Slot</th><th>Animal</th></tr></thead><tbody>
      <?php while($f=$feedRes->fetch_assoc()): ?><tr><td><?=$f['period']?></td><td><?=$f['time_slot']?></td><td>#<?=$f['animal_id']?> <?=$f['Name']?></td></tr><?php endwhile; ?></tbody></table></div>
    <?php endif; ?>
    <h4>Cleaning</h4>
    <?php if($cleanRes->num_rows===0): ?><p class="note">No cleaning tasks.</p><?php else: ?>
    <div class="scrollbox compact"><table><thead><tr><th>Period</th><th>Slot</th><th>Enclosure</th></tr></thead><tbody>
      <?php while($c=$cleanRes->fetch_assoc()): ?><tr><td><?=$c['period']?></td><td><?=$c['time_slot']?></td><td><?=$c['enclosure_name']?></td></tr><?php endwhile; ?></tbody></table></div>
    <?php endif; ?>
  </div>
  <?php if($isMgr): ?>
  <div class="card">
    <h2>Manager Panel</h2>
    <div class="flex">
      <a class="btn" href="tasks.php">Assign Shifts & Tasks</a>
      <a class="btn" href="staff.php">Manage Staff</a>
      <a class="btn" href="animals.php">Manage Animals</a>
    </div>
  </div>
  <?php endif; ?>
</div>
<?php include "footer.php"; ?>
