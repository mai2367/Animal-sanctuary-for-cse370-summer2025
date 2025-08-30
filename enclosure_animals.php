
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
    <title>Enclosure Animals</title>
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
        <?php
        if(isset($_GET['id'])){
            $enclosure_id = intval($_GET['id']);
            
            // Get enclosure details
            $stmt = $conn->prepare("SELECT * FROM enclosure WHERE enclosure_id=?");
            $stmt->bind_param("i", $enclosure_id);
            $stmt->execute();
            $enclosure = $stmt->get_result()->fetch_assoc();
            
            if($enclosure){
                echo "<h2>Animals in Enclosure #" . $enclosure['enclosure_id'] . "</h2>";
                echo "<div class='card'>";
                echo "<p><strong>Type:</strong> " . $enclosure['Type'] . "</p>";
                echo "<p><strong>Habitat:</strong> " . $enclosure['Habitat'] . "</p>";
                echo "<p><strong>Capacity:</strong> " . $enclosure['capacity'] . "</p>";
                echo "</div>";
                
                // Get animals in this enclosure
                $stmt = $conn->prepare("SELECT * FROM animal WHERE enclosure_id=?");
                $stmt->bind_param("i", $enclosure_id);
                $stmt->execute();
                $animals = $stmt->get_result();
                
                if($animals->num_rows > 0){
                    echo "<h3>Animals in this Enclosure</h3>";
                    echo "<table>";
                    echo "<tr>
                            <th>Name</th>
                            <th>Species</th>
                            <th>Gender</th>
                            <th>Intake Type</th>
                            <th>Details</th>
                          </tr>";
                    
                    while($animal = $animals->fetch_assoc()){
                        $gender = ($animal['Gender'] == 1 ? "Male" : "Female");
                        $intakeType = ($animal['Intake_type'] == 1 ? "Permanent" : "Temporary");
                        
                        echo "<tr>
                                <td>" . $animal['Name'] . "</td>
                                <td>" . $animal['Species'] . "</td>
                                <td>" . $gender . "</td>
                                <td>" . $intakeType . "</td>
                                <td><a href='animal_detail.php?id=" . $animal['animal_id'] . "' class='button'>View Details</a></td>
                              </tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<div class='alert'>No animals found in this enclosure.</div>";
                }
            } else {
                echo "<div class='alert alert-error'>Enclosure not found.</div>";
            }
        } else {
            echo "<div class='alert alert-error'>No enclosure selected.</div>";
        }
        ?>
        
        <div class="nav-links">
            <a href="enclosures.php" class="button">Back to Enclosures</a>
            <a href="animals.php" class="button">View All Animals</a>
        </div>
    </main>
</div>
</body>
</html>
