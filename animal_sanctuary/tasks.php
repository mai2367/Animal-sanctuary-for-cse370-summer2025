<?php
require_once "db.php"; require_once "auth.php"; require_login();
$prefill_animal = isset($_GET['prefill_animal']) ? intval($_GET['prefill_animal']) : 0;
$prefill_username = isset($_GET['prefill_username']) ? $_GET['prefill_username'] : '';
if(!is_manager()){ include "header.php"; echo "<p class='alert error'>Only managers can assign shifts and tasks.</p>"; include "footer.php"; exit; }
$msg="";


if(isset($_POST['assign_shift'])){
  $user=$_POST['username']; $shift=intval($_POST['shift_type_id']);
  $period = $mysqli->query("SELECT period FROM shift_types WHERE shift_type_id=$shift")->fetch_assoc()['period'];
  $conflict = $mysqli->query("SELECT 1 FROM staff_shift_map s JOIN shift_types st ON st.shift_type_id=s.shift_type_id WHERE s.username='".$mysqli->real_escape_string($user)."' AND st.period='".$mysqli->real_escape_string($period)."' AND st.time_slot!= (SELECT time_slot FROM shift_types WHERE shift_type_id=$shift)")->num_rows>0;
  if($conflict){ $msg="Conflict: user already has a shift in $period for the other time slot."; }
  else{
    $mysqli->query("INSERT IGNORE INTO staff_shift_map (username,shift_type_id) VALUES ('".$mysqli->real_escape_string($user)."',$shift)");
    if(!empty($_POST['animal_id'])){ $aid=intval($_POST['animal_id']); $mysqli->query("INSERT IGNORE INTO feed_assignments (username,animal_id,shift_type_id) VALUES ('".$mysqli->real_escape_string($user)."',$aid,$shift)"); }
    if(!empty($_POST['enclosure_id'])){ $eid=intval($_POST['enclosure_id']); $mysqli->query("INSERT IGNORE INTO clean_assignments (username,enclosure_id,shift_type_id) VALUES ('".$mysqli->real_escape_string($user)."',$eid,$shift)"); }
    $msg="Assigned.";
  }
}


if(isset($_POST['unassign_shift'])){
  $user=$mysqli->real_escape_string($_POST['username']); $shift=intval($_POST['shift_type_id']);
  $mysqli->query("DELETE FROM feed_assignments WHERE username='$user' AND shift_type_id=$shift");
  $mysqli->query("DELETE FROM clean_assignments WHERE username='$user' AND shift_type_id=$shift");
  $mysqli->query("DELETE FROM staff_shift_map WHERE username='$user' AND shift_type_id=$shift");
  $msg="Shift removed.";
}


$fq = $_GET['q'] ?? ""; $slot = $_GET['slot'] ?? ""; $jq = $_GET['job'] ?? "";
$staff = $mysqli->query("SELECT Username,name,job FROM staff ORDER BY name");
$animals = $mysqli->query("SELECT animal_id, Name FROM animal ORDER BY Name");
$enclosures = $mysqli->query("SELECT enclosure_id, NAME FROM enclosure ORDER BY NAME");
$shifts = $mysqli->query("SELECT shift_type_id, period, time_slot FROM shift_types ORDER BY period, time_slot");
$where="WHERE 1=1 ";
if($fq){ $fq_e=$mysqli->real_escape_string($fq); $where.=" AND (s.Username LIKE '%$fq_e%' OR s.name LIKE '%$fq_e%')"; }
if($jq){ $jq_e=$mysqli->real_escape_string($jq); $where.=" AND s.job LIKE '%$jq_e%'"; }
if($slot){ $slot_e=$mysqli->real_escape_string($slot); $where.=" AND CONCAT(st.period,' ',st.time_slot) LIKE '%$slot_e%'"; }
$sql = "SELECT s.Username, s.name, s.job, st.period, st.time_slot, st.shift_type_id
        FROM staff_shift_map m JOIN staff s ON s.Username=m.username JOIN shift_types st ON st.shift_type_id=m.shift_type_id
        $where ORDER BY s.name, st.period, st.time_slot";
$list = $mysqli->query($sql);
include "header.php"; ?>


<div class="card">
  <h2>Assign Shifts & Tasks</h2>
  <?php if($msg): ?><p class="alert <?=strpos($msg,'Error')!==false?'error':'success'?>"><?=$msg?></p><?php endif; ?>
  <form method="post" class="grid grid-3">
    <div><label>Staff</label>
      <select name="username" required><?php while($s=$staff->fetch_assoc()): ?><option value="<?=$s['Username']?>" <?=($prefill_username==$s['Username']?'selected':'')?>><?=$s['name']?> (<?=$s['Username']?>)</option><?php endwhile; ?></select>
    </div>
    <div><label>Shift Slot</label>
      <select name="shift_type_id" required><?php while($s=$shifts->fetch_assoc()): ?><option value="<?=$s['shift_type_id']?>"><?=$s['period']?> <?=$s['time_slot']?></option><?php endwhile; ?></select>
    </div>
    <div class="note">Optional: also assign Feeding (animal) and/or Cleaning (enclosure) for this same shift.</div>
    <div><label>Feeding (Animal)</label><select name="animal_id"><option value="">— None —</option><?php while($a=$animals->fetch_assoc()): ?><option value="<?=$a['animal_id']?>" <?=($prefill_animal==$a['animal_id']?'selected':'')?>>#<?=$a['animal_id']?> <?=$a['Name']?></option><?php endwhile; ?></select></div>
    <div><label>Cleaning (Enclosure)</label><select name="enclosure_id"><option value="">— None —</option><?php while($e=$enclosures->fetch_assoc()): ?><option value="<?=$e['enclosure_id']?>"><?=$e['NAME']?></option><?php endwhile; ?></select></div>
    <div style="align-self:end"><button class="btn primary" name="assign_shift">Assign</button></div>
  </form>
</div>


<div class="card">
  <h2>All Assigned Shifts & Tasks</h2>
  <form method="get" class="row"><input name="q" value="<?=htmlspecialchars($fq)?>" placeholder="Search name or username"><input name="job" value="<?=htmlspecialchars($jq)?>" placeholder="Job type"><input name="slot" value="<?=htmlspecialchars($slot)?>" placeholder="Slot e.g., WEEKEND MORNING"><button class="btn">Filter</button></form>
  <div class="scrollbox compact" style="margin-top:10px">
    <table>
      <thead><tr><th>Staff</th><th>Job</th><th>Period</th><th>Time Slot</th><th>Feeding</th><th>Cleaning</th><th>Actions</th></tr></thead>
      <tbody>
        <?php while($row=$list->fetch_assoc()):
          $u=$mysqli->real_escape_string($row['Username']); $sid=intval($row['shift_type_id']);
          $feed=$mysqli->query("SELECT a.Name FROM feed_assignments fa JOIN animal a ON a.animal_id=fa.animal_id WHERE fa.username='$u' AND fa.shift_type_id=$sid");
          $clean=$mysqli->query("SELECT e.NAME FROM clean_assignments ca JOIN enclosure e ON e.enclosure_id=ca.enclosure_id WHERE ca.username='$u' AND ca.shift_type_id=$sid");
        ?>
          <tr>
            <td><?=$row['name']?> (<?=$row['Username']?>)</td><td><?=$row['job']?></td><td><?=$row['period']?></td><td><?=$row['time_slot']?></td>
            <td><?php if($feed->num_rows===0): ?><span class="note">N/A</span><?php else: while($f=$feed->fetch_assoc()): ?><span class="badge tag"><?=$f['Name']?></span><?php endwhile; endif; ?></td>
            <td><?php if($clean->num_rows===0): ?><span class="note">N/A</span><?php else: while($c=$clean->fetch_assoc()): ?><span class="badge tag"><?=$c['NAME']?></span><?php endwhile; endif; ?></td>
            <td>
              <form method="post" onsubmit="return confirm('Remove this shift (and related tasks) for <?=$row['name']?>?');" style="display:inline">
                <input type="hidden" name="username" value="<?=$row['Username']?>">
                <input type="hidden" name="shift_type_id" value="<?=$sid?>">
                <button class="btn danger" name="unassign_shift">Remove</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include "footer.php"; ?>
