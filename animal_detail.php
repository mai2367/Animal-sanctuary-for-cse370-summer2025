<?php 
include("db.php"); 
if(!isset($_SESSION['user'])) {
    header("Location:index.php");
    exit;
}

// Get current page for active navigation
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Animal Detail</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <header>
        <div class="header-content">
            <h1>Animal Sanctuary Management System</h1>
            <nav>
                <ul>
                    <li><a href="animals.php" <?php echo $current_page == 'animals.php' ? 'class="active"' : ''; ?>>Animals</a></li>
                    <li><a href="enclosures.php" <?php echo $current_page == 'enclosures.php' ? 'class="active"' : ''; ?>>Enclosures</a></li>
                    <li><a href="shifts.php" <?php echo $current_page == 'shifts.php' ? 'class="active"' : ''; ?>>Shifts</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <?php
        if(isset($_GET['id'])){
            $id = intval($_GET['id']);
            $stmt = $conn->prepare("SELECT * FROM animal WHERE animal_id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $animal = $stmt->get_result()->fetch_assoc();

            if($animal){
                $gender = ($animal['Gender'] == 1 ? "Male" : "Female");
                $intakeType = ($animal['Intake_type'] == 1 ? "Permanent" : "Temporary");

                echo "<div class='animal-detail'>";
                echo "<h2>".$animal['Name']."</h2>";
                echo "<p><strong>Species:</strong> ".$animal['Species']."</p>";
                echo "<p><strong>Diet:</strong> ".$animal['Diet']."</p>";
                echo "<p><strong>Gender:</strong> ".$gender."</p>";
                echo "<p><strong>Intake Type:</strong> ".$intakeType."</p>";
                echo "<p><strong>Date of Birth:</strong> ".$animal['DOB']."</p>";
                echo "<p><strong>Intake Date:</strong> ".$animal['Intake_Date']."</p>";
                if(!empty($animal['Release_Date'])){
                    echo "<p><strong>Planned Release:</strong> ".$animal['Release_Date']."</p>";
                }
                echo "<p><strong>Enclosure ID:</strong> ".$animal['enclosure_id']."</p>";
                echo "</div>";
            } else {
                echo "<div class='alert alert-error'><p>Animal not found.</p></div>";
            }
        } else {
            echo "<div class='alert alert-error'><p>No animal selected.</p></div>";
        }
        ?>
        
        <div class="nav-links">
            <a href="animals.php" class="button">Back to Animal List</a>
        </div>
    </main>
</div>
</body>
</html>
