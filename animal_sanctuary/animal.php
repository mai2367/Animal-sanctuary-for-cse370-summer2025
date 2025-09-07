<?php
require_once "db.php"; require_once "auth.php"; require_login();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if(!$id){ header("Location: animals.php"); exit; }

$animal = $mysqli->query("SELECT a.*, e.NAME AS enclosure_name, e.Type AS enclosure_type, e.Habitat AS enclosure_habitat FROM animal a LEFT JOIN enclosure e ON e.enclosure_id=a.enclosure_id WHERE a.animal_id=$id")->fetch_assoc();
if(!$animal){ include "header.php"; echo "<p class='alert error'>Animal not found.</p>"; include "footer.php"; exit; }

// feeding coverage + list
$weekday = $mysqli->query("SELECT st.period, st.time_slot, s.name AS staff_name, s.Username FROM feed_assignments fa JOIN shift_types st ON st.shift_type_id=fa.shift_type_id LEFT JOIN staff s ON s.Username=fa.username WHERE fa.animal_id=$id AND st.period='WEEKDAY' ORDER BY st.time_slot");
$weekend = $mysqli->query("SELECT st.period, st.time_slot, s.name AS staff_name, s.Username FROM feed_assignments fa JOIN shift_types st ON st.shift_type_id=fa.shift_type_id LEFT JOIN staff s ON s.Username=fa.username WHERE fa.animal_id=$id AND st.period='WEEKEND' ORDER BY st.time_slot");
$has_weekday = $weekday->num_rows>0;
$has_weekend = $weekend->num_rows>0;

// health & medicine
$health = $mysqli->query("SELECT health_issue FROM health_issues WHERE animal_id=$id ORDER BY health_issue");
$meds = $mysqli->query("SELECT medicine_name FROM medicine WHERE animal_id=$id ORDER BY medicine_name");

// parents (if any)
$parents = $mysqli->query("SELECT b.mother_id, b.father_id FROM offspring o JOIN breeding b ON b.breeding_id=o.breeding_id WHERE o.offspring_id=$id LIMIT 1")->fetch_assoc();
$mother=null;$father=null;
if($parents){
  $mother=$mysqli->query("SELECT animal_id, Name, Species FROM animal WHERE animal_id=".$parents['mother_id'])->fetch_assoc();
  $father=$mysqli->query("SELECT animal_id, Name, Species FROM animal WHERE animal_id=".$parents['father_id'])->fetch_assoc();
}

// breeding as parent
$breedings = $mysqli->query("SELECT breeding_id, mating_date, due_date, mother_id, father_id FROM breeding WHERE mother_id=$id OR father_id=$id ORDER BY breeding_id DESC");

// offspring list
$offs = $mysqli->query("SELECT o.offspring_id, a.Name, a.Species FROM offspring o JOIN animal a ON a.animal_id=o.offspring_id WHERE o.breeding_id IN (SELECT breeding_id FROM breeding WHERE mother_id=$id OR father_id=$id) ORDER BY a.Name");

include "header.php"; ?>
<div class="card">
  <h2>Animal #<?=$animal['animal_id']?> — <?=$animal['Name']?></h2>
  <div class="grid grid-2">
    <div>
      <h3>Details</h3>
      <table class="compact">
        <tr><th style="width:180px">Species</th><td><?=$animal['Species']?></td></tr>
        <tr><th>Gender</th><td><?=$animal['Gender']==1?'Male':'Female'?></td></tr>
        <tr><th>DOB</th><td><?=$animal['DOB']?></td></tr>
        <tr><th>Intake Type</th><td><?=$animal['Intake_type']?></td></tr>
        <tr><th>Intake Date</th><td><?=$animal['Intake_Date']?></td></tr>
        <tr><th>Release Date</th><td><?=$animal['Release_Date']?:'—'?></td></tr>
        <tr><th>Diet</th><td><?=$animal['Diet']?></td></tr>
        <tr><th>Enclosure</th><td><?=$animal['enclosure_name']?> (<?=$animal['enclosure_type']?>, <?=$animal['enclosure_habitat']?>)</td></tr>
      </table>
      <div class="flex" style="margin-top:10px">
        <a class="btn" href="animals.php">Back to Animals</a>
        <a class="btn" href="breeding.php">Breeding & Birth</a>
        <a class="btn" href="health.php">Health</a>
        <a class="btn warn" href="tasks.php?prefill_animal=<?=$animal['animal_id']?>">Assign Feeding</a>
      </div>
    </div>
    <div>
      <h3>Feeding coverage</h3>
      <?php if(!$has_weekday): ?><p class="alert error">⚠️ Not fed on WEEKDAY. <a href="tasks.php?prefill_animal=<?=$animal['animal_id']?>">Assign now</a>.</p><?php endif; ?>
      <?php if(!$has_weekend): ?><p class="alert error">⚠️ Not fed on WEEKEND. <a href="tasks.php?prefill_animal=<?=$animal['animal_id']?>">Assign now</a>.</p><?php endif; ?>
      <div class="grid grid-2">
        <div>
          <h4>Weekday</h4>
          <?php if($weekday->num_rows===0): ?><p class="note">No assignments.</p>
          <?php else: ?><ul><?php while($w=$weekday->fetch_assoc()): ?><li><?=$w['time_slot']?> — <?=$w['staff_name']?:$w['Username']?></li><?php endwhile; ?></ul><?php endif; ?>
        </div>
        <div>
          <h4>Weekend</h4>
          <?php if($weekend->num_rows===0): ?><p class="note">No assignments.</p>
          <?php else: ?><ul><?php while($w=$weekend->fetch_assoc()): ?><li><?=$w['time_slot']?> — <?=$w['staff_name']?:$w['Username']?></li><?php endwhile; ?></ul><?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="grid grid-2">
  <div class="card">
    <h3>Parents</h3>
    <?php if(!$parents): ?><p class="note">Unknown</p>
    <?php else: ?>
      <p>Mother: <?php if($mother): ?>#<?=$mother['animal_id']?> — <?=$mother['Name']?> (<?=$mother['Species']?>)<?php else: ?>Unknown<?php endif; ?></p>
      <p>Father: <?php if($father): ?>#<?=$father['animal_id']?> — <?=$father['Name']?> (<?=$father['Species']?>)<?php else: ?>Unknown<?php endif; ?></p>
    <?php endif; ?>
  </div>
  <div class="card">
    <h3>Breeding as Parent</h3>
    <?php if($breedings->num_rows===0): ?><p class="note">No breedings recorded.</p>
    <?php else: ?>
      <div class="scrollbox compact">
        <table><thead><tr><th>ID</th><th>Mating</th><th>Due</th><th>Mother</th><th>Father</th></tr></thead><tbody>
          <?php while($b=$breedings->fetch_assoc()): ?>
            <tr><td><?=$b['breeding_id']?></td><td><?=$b['mating_date']?></td><td><?=$b['due_date']?></td><td><?=$b['mother_id']?></td><td><?=$b['father_id']?></td></tr>
          <?php endwhile; ?>
        </tbody></table>
      </div>
    <?php endif; ?>
  </div>
</div>

<div class="grid grid-2">
  <div class="card">
    <h3>Offspring</h3>
    <?php if($offs->num_rows===0): ?><p class="note">No offspring recorded.</p>
    <?php else: ?>
      <div class="scrollbox compact">
        <table><thead><tr><th>ID</th><th>Name</th><th>Species</th></tr></thead><tbody>
        <?php while($o=$offs->fetch_assoc()): ?>
          <tr><td><?=$o['offspring_id']?></td><td><?=$o['Name']?></td><td><?=$o['Species']?></td></tr>
        <?php endwhile; ?>
        </tbody></table>
      </div>
    <?php endif; ?>
  </div>
  <div class="card">
    <h3>Health & Medicine</h3>
    <div class="grid grid-2">
      <div>
        <h4>Health Issues</h4>
        <?php if($health->num_rows===0): ?><p class="note">None recorded.</p>
        <?php else: ?><ul><?php while($h=$health->fetch_assoc()): ?><li><?=$h['health_issue']?></li><?php endwhile; ?></ul><?php endif; ?>
      </div>
      <div>
        <h4>Medicine</h4>
        <?php if($meds->num_rows===0): ?><p class="note">None recorded.</p>
        <?php else: ?><ul><?php while($m=$meds->fetch_assoc()): ?><li><?=$m['medicine_name']?></li><?php endwhile; ?></ul><?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>
