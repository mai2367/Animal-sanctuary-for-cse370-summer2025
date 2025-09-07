<?php
require_once "db.php"; require_once "auth.php"; require_login();
$msg = "";

// next id
function next_animal_id($mysqli){ $r=$mysqli->query("SELECT IFNULL(MAX(animal_id),100)+1 nid FROM animal")->fetch_assoc(); return intval($r['nid']); }

// add animal
if(isset($_POST['add_animal'])){ 
  $id = intval($_POST['animal_id'] ?: next_animal_id($mysqli));
  $name=$mysqli->real_escape_string($_POST['Name']);
  $dob = $_POST['DOB'] ? "'".$mysqli->real_escape_string($_POST['DOB'])."'" : "NULL";
  $species=$mysqli->real_escape_string($_POST['Species']);
  $intake=intval($_POST['Intake_type']);
  $gender=intval($_POST['Gender']);
  $release = $_POST['Release_Date'] ? "'".$mysqli->real_escape_string($_POST['Release_Date'])."'" : "NULL";
  $diet=$mysqli->real_escape_string($_POST['Diet']);
  $intake_date="'".$mysqli->real_escape_string($_POST['Intake_Date'])."'";
  $enc=intval($_POST['enclosure_id']);
  $sql="INSERT INTO animal (animal_id,Name,DOB,Species,Intake_type,Gender,Release_Date,Diet,Intake_Date,enclosure_id)
        VALUES ($id,'$name',$dob,'$species',$intake,$gender,$release,'$diet',$intake_date,$enc)";
  if($mysqli->query($sql)){ $msg="Animal #$id created."; } else { $msg="Error: ".$mysqli->error; }
}

// delete animal (manager only)
if(isset($_POST['delete_animal']) && is_manager()){ 
  $aid=intval($_POST['animal_id']); $mysqli->begin_transaction(); 
  try{ 
    $mysqli->query("DELETE FROM offspring WHERE offspring_id=$aid");
    $mysqli->query("DELETE FROM offspring WHERE breeding_id IN (SELECT breeding_id FROM breeding WHERE mother_id=$aid OR father_id=$aid)");
    $mysqli->query("DELETE FROM breeding WHERE mother_id=$aid OR father_id=$aid");
    if(!$mysqli->query("DELETE FROM animal WHERE animal_id=$aid")){ throw new Exception($mysqli->error); }
    $mysqli->commit(); $msg="Animal #$aid deleted.";
  } catch (Throwable $e){ $mysqli->rollback(); $msg="Error deleting animal: ".$e->getMessage(); }
}

// search
$q = $_GET['q'] ?? "";
$qr = $mysqli->real_escape_string($q);

// Build search query to search across multiple fields
$search_conditions = [];
if (!empty($qr)) {
    // Check if search is specifically for gender
    $gender_search = false;
    $gender_value = null;
    
    if (strtolower($qr) === 'male') {
        $gender_search = true;
        $gender_value = 1;
    } elseif (strtolower($qr) === 'female') {
        $gender_search = true;
        $gender_value = 2;
    }
    
    if ($gender_search) {
        // If searching specifically for gender, only match the exact gender
        $search_conditions = ["a.Gender = $gender_value"];
    } else {
        // General search across all fields
        $search_conditions = [
            "a.animal_id LIKE '%$qr%'",
            "a.Name LIKE '%$qr%'",
            "a.Species LIKE '%$qr%'",
            "a.Diet LIKE '%$qr%'",
            "e.NAME LIKE '%$qr%'",
            // Search by gender numeric value
            "a.Gender LIKE '%$qr%'"
        ];
    }
}

// Build the complete query
$query = "SELECT a.*, e.NAME AS enclosure_name 
          FROM animal a 
          LEFT JOIN enclosure e ON e.enclosure_id=a.enclosure_id";

if (!empty($search_conditions)) {
    $query .= " WHERE " . implode(" OR ", $search_conditions);
}

$query .= " ORDER BY a.Name";

$animals = $mysqli->query($query);
$enclosures = $mysqli->query("SELECT enclosure_id, NAME FROM enclosure ORDER BY NAME");

include "header.php"; ?>
<style>
.search-container {
  display: flex;
  margin-bottom: 20px;
  gap: 10px;
  align-items: stretch;
  max-width: 800px;
}

.search-input {
  flex: 1;
  padding: 12px 15px;
  font-size: 16px;
  border: 1px solid #ddd;
  border-radius: 4px;
  min-width: 250px;
}

.search-btn {
  padding: 12px 20px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
  white-space: nowrap;
}

.search-btn:hover {
  background-color: #45a049;
}

.clear-btn {
  padding: 12px 20px;
  background-color: #6c757d;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
  white-space: nowrap;
  text-decoration: none;
  display: inline-block;
  text-align: center;
}

.clear-btn:hover {
  background-color: #5a6268;
}

@media (max-width: 768px) {
  .search-container {
    flex-direction: column;
  }
  
  .search-input {
    min-width: auto;
  }
}
</style>

<div class="card">
  <h2>Animals <span class="note">v5</span></h2>
  <form method="post" class="grid grid-2">
    <div><label>Animal ID (optional)</label><input name="animal_id" placeholder="Leave blank for auto"></div>
    <div><label>Name</label><input required name="Name"></div>
    <div><label>Date of Birth</label><input type="date" name="DOB"></div>
    <div><label>Species</label><input required name="Species"></div>
    <div><label>Intake Type</label>
      <select name="Intake_type" required>
        <option value="1">1 – Rescue/Admission</option>
        <option value="2">2 – Release/Transfer</option>
      </select>
    </div>
    <div><label>Gender</label><select name="Gender" required><option value="1">Male</option><option value="2">Female</option></select></div>
    <div><label>Release Date</label><input type="date" name="Release_Date"></div>
    <div><label>Diet</label><input required name="Diet"></div>
    <div><label>Intake Date</label><input type="date" name="Intake_Date" required></div>
    <div><label>Enclosure</label>
      <select name="enclosure_id" required>
        <?php $enclosures->data_seek(0); while($e=$enclosures->fetch_assoc()): ?>
          <option value="<?=$e['enclosure_id']?>"><?=$e['NAME']?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div style="grid-column:1/-1"><button class="btn primary" name="add_animal">Save Animal</button></div>
  </form>
  <?php if($msg): ?><p class="alert <?=strpos($msg,'Error')===false?'success':'error'?>"><?=$msg?></p><?php endif; ?>
</div>

<div class="card">
  <h3>Search Animals</h3>
  <!-- Improved search form -->
  <form method="get" class="search-container">
    <input type="text" name="q" value="<?=htmlspecialchars($q)?>" placeholder="Search by ID, name, species, diet, enclosure, or gender" class="search-input">
    <button type="submit" class="search-btn">Search</button>
    <?php if(!empty($q)): ?>
      <a href="animals.php" class="clear-btn">Clear</a>
    <?php endif; ?>
  </form>
  
  <div class="scrollbox compact" style="margin-top:10px">
    <table>
      <thead><tr><th>ID</th><th>Name</th><th>Species</th><th>Gender</th><th>Diet</th><th>Enclosure</th><th>Actions</th></tr></thead>
      <tbody>
        <?php if($animals->num_rows > 0): ?>
          <?php while($a=$animals->fetch_assoc()): ?>
            <tr>
              <td><?=$a['animal_id']?></td><td><?=$a['Name']?></td><td><?=$a['Species']?></td>
              <td><?=$a['Gender']==1?'Male':($a['Gender']==2?'Female':'?')?></td>
              <td><?=$a['Diet']?></td><td><?=$a['enclosure_name']?></td>
              <td>
                <div class="flex">
                  <a class="btn" href="animal.php?id=<?=$a['animal_id']?>">See more</a>
                  <?php
                    $aid = intval($a['animal_id']);
                    $wk = $mysqli->query("SELECT 1 FROM feed_assignments fa JOIN shift_types st ON st.shift_type_id=fa.shift_type_id WHERE fa.animal_id=$aid AND st.period='WEEKDAY' LIMIT 1")->num_rows>0;
                    $we = $mysqli->query("SELECT 1 FROM feed_assignments fa JOIN shift_types st ON st.shift_type_id=fa.shift_type_id WHERE fa.animal_id=$aid AND st.period='WEEKEND' LIMIT 1")->num_rows>0;
                  ?>
                  <?php if(!$wk): ?><a class="btn warn" href="tasks.php?prefill_animal=<?=$a['animal_id']?>">⚠️ Not fed on WEEKDAY</a><?php endif; ?>
                  <?php if(!$we): ?><a class="btn warn" href="tasks.php?prefill_animal=<?=$a['animal_id']?>">⚠️ Not fed on WEEKEND</a><?php endif; ?>
                  <?php if(is_manager()): ?>
                    <form method="post" onsubmit="return confirm('Delete animal #<?=$a['animal_id']?> (<?=$a['Name']?>)?');" style="display:inline">
                      <input type="hidden" name="animal_id" value="<?=$a['animal_id']?>">
                      <button class="btn danger" name="delete_animal">Delete</button>
                    </form>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" style="text-align: center;">
              <?= empty($q) ? 'No animals found.' : 'No animals found matching "' . htmlspecialchars($q) . '"' ?>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include "footer.php"; ?>