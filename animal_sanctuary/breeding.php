<?php
require_once "db.php"; require_once "auth.php"; require_login();
$breeding_msg = ""; $birth_msg = "";

// helpers
function next_breeding_id($mysqli){
  $r=$mysqli->query("SELECT IFNULL(MAX(breeding_id),0)+1 AS nid FROM breeding")->fetch_assoc(); return intval($r['nid']);
}
function next_animal_id($mysqli){
  $r=$mysqli->query("SELECT IFNULL(MAX(animal_id),100)+1 AS nid FROM animal")->fetch_assoc(); return intval($r['nid']);
}
function animal_info($mysqli,$id){
  return $mysqli->query("SELECT animal_id, Name, Species, Gender FROM animal WHERE animal_id=".intval($id))->fetch_assoc();
}
function parents_of($mysqli,$aid){
  $sql="SELECT b.mother_id, b.father_id FROM offspring o JOIN breeding b ON b.breeding_id=o.breeding_id WHERE o.offspring_id=".intval($aid)." LIMIT 1";
  $res=$mysqli->query($sql); return $res->fetch_assoc() ?: ['mother_id'=>null,'father_id'=>null];
}

// breed submission
if(isset($_POST['breed'])){
  $mother=intval($_POST['mother_id']); $father=intval($_POST['father_id']); $mating=$_POST['mating_date']; $due=$_POST['due_date'];
  if($mother===$father){ $breeding_msg="Invalid: an animal cannot breed with itself."; }
  else{
    $mi=animal_info($mysqli,$mother); $fi=animal_info($mysqli,$father);
    if(!$mi||!$fi){ $breeding_msg="Invalid animal selections."; }
    elseif(intval($mi['Gender'])!==2 || intval($fi['Gender'])!==1){ $breeding_msg="Mother must be female and Father must be male."; }
    elseif($mi['Species']!==$fi['Species']){ $breeding_msg="Species must match."; }
    else{
      $mp=parents_of($mysqli,$mother); $fp=parents_of($mysqli,$father);
      if(($mp['mother_id'] && ($mp['mother_id']==$fp['mother_id'] || $mp['mother_id']==$fp['father_id'])) ||
         ($mp['father_id'] && ($mp['father_id']==$fp['mother_id'] || $mp['father_id']==$fp['father_id']))){
        $breeding_msg="No inbreeding allowed: selected parents share at least one parent.";
      } else {
        $bid=next_breeding_id($mysqli);
        if($mysqli->query("INSERT INTO breeding (breeding_id,mating_date,mother_id,father_id,due_date) VALUES ($bid,'".$mysqli->real_escape_string($mating)."',$mother,$father,'".$mysqli->real_escape_string($due)."')")){
          $breeding_msg="Breeding #$bid recorded.";
        } else { $breeding_msg="Error: ".$mysqli->error; }
      }
    }
  }
}

// record birth
if(isset($_POST['record_birth'])){
  $bid=intval($_POST['breeding_id']);
  $newid=next_animal_id($mysqli);
  $name=$mysqli->real_escape_string($_POST['Name']); $species=$mysqli->real_escape_string($_POST['Species']); $gender=intval($_POST['Gender']); $diet=$mysqli->real_escape_string($_POST['Diet']);
  $dob = $_POST['DOB'] ? "'".$mysqli->real_escape_string($_POST['DOB'])."'" : "NULL";
  $intake_date = $_POST['Intake_Date'] ? "'".$mysqli->real_escape_string($_POST['Intake_Date'])."'" : "CURDATE()";
  $enc=intval($_POST['enclosure_id']); $intake=intval($_POST['Intake_type']);
  $sql="INSERT INTO animal (animal_id,Name,DOB,Species,Intake_type,Gender,Release_Date,Diet,Intake_Date,enclosure_id) VALUES ($newid,'$name',$dob,'$species',$intake,$gender,NULL,'$diet',$intake_date,$enc)";
  if($mysqli->query($sql) && $mysqli->query("INSERT INTO offspring (breeding_id,offspring_id) VALUES ($bid,$newid)")){
    $birth_msg="Birth recorded. New animal #$newid.";
  } else { $birth_msg="Error: ".$mysqli->error; }
}

// preloading select queries
$females=$mysqli->query("SELECT animal_id, Name, Species FROM animal WHERE Gender=2 ORDER BY Species, Name");
$males=$mysqli->query("SELECT animal_id, Name, Species FROM animal WHERE Gender=1 ORDER BY Species, Name");
$enclosures=$mysqli->query("SELECT enclosure_id, NAME FROM enclosure ORDER BY NAME");

// list search
$bq = $_GET['bq'] ?? "";
$bqe = $mysqli->real_escape_string($bq);
$breeding_list = $mysqli->query("
  SELECT b.*, m.Name AS mother_name, m.Species AS mother_species, f.Name AS father_name, f.Species AS father_species
  FROM breeding b
  LEFT JOIN animal m ON m.animal_id=b.mother_id
  LEFT JOIN animal f ON f.animal_id=b.father_id
  WHERE CONCAT_WS(' ', b.breeding_id, b.mother_id, m.Name, m.Species, b.father_id, f.Name, f.Species) LIKE '%$bqe%'
  ORDER BY b.breeding_id DESC
  LIMIT 200
");

include "header.php"; ?>
<div class="card">
  <h2>Breeding</h2>
  <?php if($breeding_msg): ?><p class="alert <?=strpos($breeding_msg,'Error')===false && strpos($breeding_msg,'Invalid')===false ? 'success':'error'?>"><?=$breeding_msg?></p><?php endif; ?>
  <form method="post" class="grid grid-2">
    <div>
      <label>Mother (Female)</label>
      <select name="mother_id" required>
        <option value="">— Select Mother —</option>
        <?php $females->data_seek(0); while($f=$females->fetch_assoc()): ?>
          <option value="<?=$f['animal_id']?>">#<?=$f['animal_id']?> — <?=$f['Name']?> (<?=$f['Species']?>)</option>
        <?php endwhile; ?>
      </select>
      <div class="form-help">Site enforces mother=Female.</div>
    </div>
    <div>
      <label>Father (Male)</label>
      <select name="father_id" required>
        <option value="">— Select Father —</option>
        <?php $males->data_seek(0); while($m=$males->fetch_assoc()): ?>
          <option value="<?=$m['animal_id']?>">#<?=$m['animal_id']?> — <?=$m['Name']?> (<?=$m['Species']?>)</option>
        <?php endwhile; ?>
      </select>
      <div class="form-help">Site enforces father=Male.</div>
    </div>
    <div><label>Mating Date</label><input type="date" name="mating_date" required></div>
    <div><label>Due Date</label><input type="date" name="due_date" required></div>
    <div style="grid-column:1/-1"><button class="btn primary" name="breed">Record Breeding</button></div>
    <div class="note" style="grid-column:1/-1">Rules enforced: same species, no inbreeding.</div>
  </form>
</div>

<div class="card">
  <h2>Record Birth</h2>
  <?php if($birth_msg): ?><p class="alert <?=strpos($birth_msg,'Error')===false ? 'success':'error'?>"><?=$birth_msg?></p><?php endif; ?>
  <form method="post" class="grid grid-2">
    <div><label>Breeding ID</label><input required name="breeding_id" placeholder="Source breeding id"></div>
    <div><label>Name</label><input required name="Name" placeholder="Newborn name"></div>
    <div><label>Date of Birth</label><input type="date" name="DOB"></div>
    <div><label>Species</label><input required name="Species" placeholder="Same as parents"></div>
    <div><label>Intake Type</label>
      <select name="Intake_type" required><option value="1">1 – Rescue/Admission</option><option value="2">2 – Release/Transfer</option></select>
    </div>
    <div><label>Gender</label><select name="Gender" required><option value="1">Male</option><option value="2">Female</option></select></div>
    <div><label>Diet</label><input required name="Diet" placeholder="e.g., Milk"></div>
    <div><label>Intake Date</label><input type="date" name="Intake_Date"></div>
    <div><label>Enclosure</label>
      <select name="enclosure_id" required>
        <?php $enclosures->data_seek(0); while($e=$enclosures->fetch_assoc()): ?>
          <option value="<?=$e['enclosure_id']?>"><?=$e['NAME']?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div style="grid-column:1/-1"><button class="btn primary" name="record_birth">Save Offspring</button></div>
  </form>
</div>

<div class="card">
  <h3>Search Breeding Records</h3>
  <form method="get" class="row"><input name="bq" value="<?=htmlspecialchars($bq)?>" placeholder="Search by mother/father name, species, id or breeding id"><button class="btn">Search</button></form>
  <div class="scrollbox compact" style="margin-top:10px">
    <table>
      <thead><tr><th>ID</th><th>Mother</th><th>Father</th><th>Mating</th><th>Due</th></tr></thead>
      <tbody>
        <?php while($b=$breeding_list->fetch_assoc()): ?>
          <tr>
            <td><?=$b['breeding_id']?></td>
            <td>#<?=$b['mother_id']?> — <?=$b['mother_name']?> (<?=$b['mother_species']?>)</td>
            <td>#<?=$b['father_id']?> — <?=$b['father_name']?> (<?=$b['father_species']?>)</td>
            <td><?=$b['mating_date']?></td><td><?=$b['due_date']?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include "footer.php"; ?>
