<?php
require_once "db.php"; require_once "auth.php"; require_login();
$msg = "";

// add health
if(isset($_POST['add_health'])){
  $aid=intval($_POST['animal_id']); $issue=$mysqli->real_escape_string($_POST['health_issue']);
  if($mysqli->query("INSERT INTO health_issues (animal_id,health_issue) VALUES ($aid,'$issue')")) $msg="Health issue added.";
  else $msg="Error: ".$mysqli->error;
}
// add medicine
if(isset($_POST['add_medicine'])){
  $aid=intval($_POST['animal_id']); $med=$mysqli->real_escape_string($_POST['medicine_name']);
  if($mysqli->query("INSERT INTO medicine (animal_id,medicine_name) VALUES ($aid,'$med')")) $msg="Medicine recorded.";
  else $msg="Error: ".$mysqli->error;
}

// search health list
$hq = $_GET['hq'] ?? "";
$hqe = $mysqli->real_escape_string($hq);
$list = $mysqli->query("
  SELECT hi.animal_id, a.Name, a.Species, hi.health_issue
  FROM health_issues hi
  LEFT JOIN animal a ON a.animal_id=hi.animal_id
  WHERE CONCAT_WS(' ', hi.animal_id, a.Name, a.Species, hi.health_issue) LIKE '%$hqe%'
  ORDER BY hi.animal_id DESC, hi.health_issue ASC
  LIMIT 400
");

include "header.php"; ?>
<div class="card">
  <h2>Health</h2>
  <?php if($msg): ?><p class="alert <?=strpos($msg,'Error')===false?'success':'error'?>"><?=$msg?></p><?php endif; ?>
  <div class="grid grid-2">
    <form method="post">
      <h3>Add Health Issue</h3>
      <div><label>Animal ID</label><input required name="animal_id" placeholder="e.g., 101"></div>
      <div><label>Health Issue</label><input name="health_issue" placeholder="e.g., Minor wound"></div>
      <div style="margin-top:8px"><button class="btn primary" name="add_health">Add Issue</button></div>
    </form>
    <form method="post">
      <h3>Add Medicine</h3>
      <div><label>Animal ID</label><input required name="animal_id" placeholder="e.g., 101"></div>
      <div><label>Medicine</label><input name="medicine_name" placeholder="e.g., Amoxicillin"></div>
      <div style="margin-top:8px"><button class="btn" name="add_medicine">Add Medicine</button></div>
    </form>
  </div>
</div>

<div class="card">
  <h3>Search Health Issues</h3>
  <form method="get" class="row"><input name="hq" value="<?=htmlspecialchars($hq)?>" placeholder="Search by animal name, species, issue or id"><button class="btn">Search</button></form>
  <div class="scrollbox compact" style="margin-top:10px">
    <table>
      <thead><tr><th>Animal</th><th>Species</th><th>Issue</th></tr></thead>
      <tbody>
        <?php while($h=$list->fetch_assoc()): ?>
          <tr>
            <td>#<?=$h['animal_id']?> — <?=$h['Name']?></td>
            <td><?=$h['Species']?></td>
            <td><?=$h['health_issue']?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include "footer.php"; ?>
