
<?php 
include("db.php"); 
if(!isset($_SESSION['user'])) {
    header("Location:index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Enclosures</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <header>
        <div class="header-content">
            <h1>Animal Sanctuary Management System</h1>
            <nav>
                <ul>
                    <li><a href="animals.php">Animals</a></li>
                    <li><a href="enclosures.php">Enclosures</a></li>
                    <li><a href="shifts.php">Shifts</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <h2>Enclosure Management</h2>
        
        <table>
            <tr>
                <th>Enclosure ID</th>
                <th>Type</th>
                <th>Habitat</th>
                <th>Capacity</th>
                <th>Actions</th>
            </tr>
        <?php
        $res = $conn->query("SELECT * FROM enclosure");
        while($r = $res->fetch_assoc()){
            echo "<tr>
                    <td>" . $r['enclosure_id'] . "</td>
                    <td>" . $r['Type'] . "</td>
                    <td>" . $r['Habitat'] . "</td>
                    <td>" . $r['capacity'] . "</td>
                    <td><a href='enclosure_animals.php?id=" . $r['enclosure_id'] . "' class='button'>View Animals</a></td>
                  </tr>";
        }
        ?>
        </table>
        
        <div class="nav-links">
            <a href="animals.php" class="button">Back to Animals</a>
        </div>
    </main>
</div>
</body>
</html>
