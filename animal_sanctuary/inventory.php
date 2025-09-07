<?php
require_once "db.php"; require_once "auth.php"; require_login();
$msg="";
$search = isset($_GET['search']) ? $mysqli->real_escape_string($_GET['search']) : '';

if(isset($_POST['add_item'])){
  $name=$mysqli->real_escape_string($_POST['item_name']); $qty=intval($_POST['quantity']); $unit=$mysqli->real_escape_string($_POST['unit']); $type=$mysqli->real_escape_string($_POST['item_type']); $date=$mysqli->real_escape_string($_POST['last_restocked']);
  if($mysqli->query("INSERT INTO inventory (item_name,last_restocked,quantity,item_type,unit) VALUES ('$name','$date',$qty,'$type','$unit')")) $msg="Item added."; else $msg="Error: ".$mysqli->error;
}

if(isset($_POST['save_row'])){
  foreach($_POST['row'] as $name=>$row){
    $qty=intval($row['quantity']); $date=$mysqli->real_escape_string($row['last_restocked']);
    $mysqli->query("UPDATE inventory SET quantity=$qty, last_restocked='$date' WHERE item_name='".$mysqli->real_escape_string($name)."'");
  }
  $msg="Saved.";
}

// Handle item deletion
if(isset($_POST['delete_item'])){
  $item_to_delete = $mysqli->real_escape_string($_POST['delete_item']);
  if($mysqli->query("DELETE FROM inventory WHERE item_name='$item_to_delete'")) {
    $msg="Item deleted.";
  } else {
    $msg="Error: ".$mysqli->error;
  }
}

// Build the query with optional search filter
$query = "SELECT * FROM inventory";
if (!empty($search)) {
    $query .= " WHERE item_name LIKE '%$search%' OR item_type LIKE '%$search%'";
}
$query .= " ORDER BY item_name";

$items=$mysqli->query($query);
include "header.php"; ?>
<style>
.search-container {
  display: flex;
  margin-bottom: 20px;
  gap: 10px;
  align-items: stretch;
}

.search-input {
  flex: 1;
  padding: 12px 15px;
  font-size: 16px;
  border: 1px solid #ddd;
  border-radius: 4px;
  min-width: 300px;
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
  background-color: #f44336;
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
  background-color: #d32f2f;
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
  <h2>Inventory</h2>
  <?php if($msg): ?><p class="alert <?=strpos($msg,'Error')===false?'success':'error'?>"><?=$msg?></p><?php endif; ?>
  <h3>Add New Item</h3>
  <form method="post" class="grid grid-3">
    <div><label>Item Name</label><input required name="item_name"></div>
    <div><label>Quantity</label><input required name="quantity" type="number" min="0"></div>
    <div><label>Unit</label><input required name="unit"></div>
    <div><label>Item Type</label><input required name="item_type"></div>
    <div><label>Last Restocked</label><input required type="date" name="last_restocked"></div>
    <div style="align-self:end"><button class="btn primary" name="add_item">Add</button></div>
  </form>
</div>
<div class="card">
  <h3>Manage Inventory</h3>
  <!-- Improved Search form -->
  <form method="get" class="search-container">
    <input type="text" name="search" placeholder="Search by name or type..." value="<?= htmlspecialchars($search) ?>" class="search-input">
    <button type="submit" class="search-btn">Search</button>
    <?php if(!empty($search)): ?>
      <a href="inventory.php" class="clear-btn">Clear</a>
    <?php endif; ?>
  </form>
  
  <div class="scrollbox compact">
  <form method="post" id="inventoryForm">
  <table>
    <thead>
      <tr>
        <th>Name</th>
        <th>Type</th>
        <th>Unit</th>
        <th>Qty</th>
        <th>Last Restocked</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if($items->num_rows > 0): ?>
        <?php while($it=$items->fetch_assoc()): ?>
          <tr>
            <td><?=$it['item_name']?></td>
            <td><?=$it['item_type']?></td>
            <td><?=$it['unit']?></td>
            <td><input type="number" name="row[<?=$it['item_name']?>][quantity]" value="<?=$it['quantity']?>"></td>
            <td><input type="date" name="row[<?=$it['item_name']?>][last_restocked]" value="<?=$it['last_restocked']?>"></td>
            <td>
              <button type="submit" name="delete_item" value="<?=$it['item_name']?>" class="btn danger" 
                      onclick="return confirm('Are you sure you want to delete <?=$it['item_name']?>?')">Delete</button>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="6" style="text-align: center;">
            <?= empty($search) ? 'No inventory items found.' : 'No items found matching "' . htmlspecialchars($search) . '"' ?>
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
  <?php if($items->num_rows > 0): ?>
    <div style="margin-top:10px"><button class="btn primary" name="save_row">Save Changes</button></div>
  <?php endif; ?>
  </form>
  </div>
</div>
<?php include "footer.php"; ?>