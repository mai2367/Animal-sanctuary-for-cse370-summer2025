<?php
require_once "db.php"; require_once "auth.php"; require_login();
$enclosures=$mysqli->query("SELECT * FROM enclosure ORDER BY NAME");
include "header.php"; ?>
<div class="card">
  <h2>Enclosures</h2>
  <div class="scrollbox compact">
  <table>
    <thead><tr><th>Name</th><th>Type</th><th>Habitat</</th><th>Capacity</th><th>Animals</th></tr></thead>
    <tbody>
      <?php while($e=$enclosures->fetch_assoc()):
        $animals=$mysqli->query("SELECT animal_id, Name, Species FROM animal WHERE enclosure_id=".$e['enclosure_id']." ORDER BY Name"); ?>
        <tr>
          <td><?=$e['NAME']?></td><td><?=$e['Type']?></td><td><?=$e['Habitat']?></td><td><?=$e['capacity']?></td>
          <td><?php if($animals->num_rows===0): ?><span class="note">N/A</span>
            <?php else: ?><?php while($a=$animals->fetch_assoc()): ?><span class="badge tag">#<?=$a['animal_id']?> <?=$a['Name']?> (<?=$a['Species']?>)</span><?php endwhile; ?><?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  </div>
</div>
<?php include "footer.php"; ?>
