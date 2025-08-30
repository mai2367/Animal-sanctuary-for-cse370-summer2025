<?php 
include("db.php"); 
if(!isset($_SESSION['user'])) {
    header("Location:index.php");
    exit;
}

// Get current page for active navigation
$current_page = basename($_SERVER['PHP_SELF']);

// Process animal deletion
if(isset($_POST['delete_animal'])) {
    $animal_id = $_POST['animal_id'];
    
    // Get animal name for confirmation message
    $stmt = $conn->prepare("SELECT Name FROM animal WHERE animal_id = ?");
    $stmt->bind_param("i", $animal_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0) {
        $animal = $result->fetch_assoc();
        $animal_name = $animal['Name'];
        
        // Delete the animal
        $stmt = $conn->prepare("DELETE FROM animal WHERE animal_id = ?");
        $stmt->bind_param("i", $animal_id);
        
        if($stmt->execute()){
            echo "<div class='alert alert-success'><p>Animal '$animal_name' deleted successfully!</p></div>";
            echo "<meta http-equiv='refresh' content='1'>"; // refresh to show changes
        } else {
            echo "<div class='alert alert-error'><p>Error deleting animal: ".$conn->error."</p></div>";
        }
    } else {
        echo "<div class='alert alert-error'><p>Error: Animal not found.</p></div>";
    }
}

// Process enclosure change
if(isset($_POST['change_enclosure'])) {
    $animal_id = $_POST['animal_id'];
    $new_enclosure_id = $_POST['new_enclosure_id'];
    
    // Check if new enclosure has capacity
    $stmt = $conn->prepare("SELECT capacity FROM enclosure WHERE enclosure_id = ?");
    $stmt->bind_param("i", $new_enclosure_id);
    $stmt->execute();
    $enclosure_result = $stmt->get_result();
    
    if($enclosure_result->num_rows === 0) {
        echo "<div class='alert alert-error'><p>Error: Enclosure ID $new_enclosure_id does not exist.</p></div>";
    } else {
        $enclosure = $enclosure_result->fetch_assoc();
        $capacity = $enclosure['capacity'];
        
        // Count current animals in the new enclosure
        $stmt = $conn->prepare("SELECT COUNT(*) as animal_count FROM animal WHERE enclosure_id = ?");
        $stmt->bind_param("i", $new_enclosure_id);
        $stmt->execute();
        $count_result = $stmt->get_result();
        $animal_count = $count_result->fetch_assoc()['animal_count'];
        
        if($animal_count >= $capacity && $capacity > 0) {
            echo "<div class='alert alert-error'><p>Error: Enclosure $new_enclosure_id has reached its maximum capacity of $capacity animals.</p></div>";
        } else {
            // Update the animal's enclosure
            $stmt = $conn->prepare("UPDATE animal SET enclosure_id = ? WHERE animal_id = ?");
            $stmt->bind_param("ii", $new_enclosure_id, $animal_id);
            
            if($stmt->execute()){
                echo "<div class='alert alert-success'><p>Animal's enclosure updated successfully!</p></div>";
                echo "<meta http-equiv='refresh' content='1'>"; // refresh to show changes
            } else {
                echo "<div class='alert alert-error'><p>Error updating enclosure: ".$conn->error."</p></div>";
            }
        }
    }
}

// Process add animal
if(isset($_POST['add_animal'])){
    $enclosure_id = $_POST['enclosure_id'];
    
    // Check if enclosure exists
    $stmt = $conn->prepare("SELECT capacity FROM enclosure WHERE enclosure_id = ?");
    $stmt->bind_param("i", $enclosure_id);
    $stmt->execute();
    $enclosure_result = $stmt->get_result();
    
    if($enclosure_result->num_rows === 0) {
        echo "<div class='alert alert-error'><p>Error: Enclosure ID $enclosure_id does not exist.</p></div>";
    } else {
        $enclosure = $enclosure_result->fetch_assoc();
        $capacity = $enclosure['capacity'];
        
        // Count current animals in the enclosure
        $stmt = $conn->prepare("SELECT COUNT(*) as animal_count FROM animal WHERE enclosure_id = ?");
        $stmt->bind_param("i", $enclosure_id);
        $stmt->execute();
        $count_result = $stmt->get_result();
        $animal_count = $count_result->fetch_assoc()['animal_count'];
        
        if($animal_count >= $capacity && $capacity > 0) {
            echo "<div class='alert alert-error'><p>Error: Enclosure $enclosure_id has reached its maximum capacity of $capacity animals.</p></div>";
        } else {
            $stmt = $conn->prepare("INSERT INTO animal (Name, DOB, Species, Intake_type, Gender, Release_Date, Diet, Intake_Date, enclosure_id) 
                                    VALUES (?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("sssiiissi", 
                $_POST['name'], 
                $_POST['dob'], 
                $_POST['species'], 
                $_POST['intake'], 
                $_POST['gender'], 
                $_POST['release_date'], 
                $_POST['diet'], 
                $_POST['intake_date'], 
                $enclosure_id
            );
            if($stmt->execute()){
                echo "<div class='alert alert-success'><p>Animal added successfully!</p></div>";
                echo "<meta http-equiv='refresh' content='1'>"; // refresh to show new animal
            } else {
                echo "<div class='alert alert-error'><p>Error adding animal: ".$conn->error."</p></div>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Animals</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function confirmDelete(animalName) {
            return confirm("Are you sure you want to delete \"" + animalName + "\"? This action cannot be undone.");
        }
        
        function confirmEnclosureChange(animalName) {
            return confirm("Are you sure you want to change the enclosure for \"" + animalName + "\"?");
        }
    </script>
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
        <h2>Animal List</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Gender</th>
                <th>Intake Type</th>
                <th>Actions</th>
            </tr>
        <?php
        $res = $conn->query("SELECT animal_id, Name, Gender, Intake_type FROM animal");
        while($row = $res->fetch_assoc()){
            $gender = ($row['Gender'] == 1 ? "Male" : "Female");
            $intakeType = ($row['Intake_type'] == 1 ? "Permanent" : "Temporary");
            echo "<tr>
                    <td><a href='animal_detail.php?id=".$row['animal_id']."'>".$row['Name']."</a></td>
                    <td>".$gender."</td>
                    <td>".$intakeType."</td>
                    <td>
                        <a href='animal_detail.php?id=".$row['animal_id']."' class='button'>View Details</a>
                        <form method='post' class='inline-form' onsubmit='return confirmDelete(\"".$row['Name']."\")'>
                            <input type='hidden' name='animal_id' value='".$row['animal_id']."'>
                            <input type='submit' name='delete_animal' value='Delete' class='button button-danger'>
                        </form>
                    </td>
                  </tr>";
        }
        ?>
        </table>

        <hr>
        
        <h3>Change Animal Enclosure</h3>
        <form method="post">
            <label for="animal_select">Select Animal:</label>
            <select name="animal_id" id="animal_select" required>
                <option value="">Select an animal</option>
                <?php
                $animals_res = $conn->query("SELECT animal_id, Name FROM animal ORDER BY Name");
                while($animal = $animals_res->fetch_assoc()) {
                    echo "<option value='" . $animal['animal_id'] . "'>" . $animal['Name'] . "</option>";
                }
                ?>
            </select><br>
            
            <label for="new_enclosure">New Enclosure:</label>
            <select name="new_enclosure_id" id="new_enclosure" required>
                <option value="">Select an enclosure</option>
                <?php
                // Get all enclosures with available capacity
                $enclosures_res = $conn->query("
                    SELECT e.enclosure_id, e.Type, e.Habitat, e.capacity, 
                           COUNT(a.animal_id) as current_animals
                    FROM enclosure e 
                    LEFT JOIN animal a ON e.enclosure_id = a.enclosure_id 
                    GROUP BY e.enclosure_id 
                    HAVING current_animals < e.capacity OR e.capacity = 0
                    ORDER BY e.enclosure_id
                ");
                while($enclosure = $enclosures_res->fetch_assoc()) {
                    $available = $enclosure['capacity'] - $enclosure['current_animals'];
                    echo "<option value='" . $enclosure['enclosure_id'] . "'>ID: " . $enclosure['enclosure_id'] . 
                         " - " . $enclosure['Type'] . " (" . $enclosure['Habitat'] . ") - Available: " . 
                         $available . "/" . $enclosure['capacity'] . "</option>";
                }
                ?>
            </select><br>
            
            <input type="submit" name="change_enclosure" value="Change Enclosure" class="button" onclick="return confirmEnclosureChange(document.getElementById('animal_select').options[document.getElementById('animal_select').selectedIndex].text);">
        </form>

        <hr>
        
        <h3>Add New Animal</h3>
        <form method="post">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" required><br>
            
            <label for="species">Species:</label>
            <input type="text" name="species" id="species" required><br>
            
            <label for="dob">Date of Birth:</label>
            <input type="date" name="dob" id="dob"><br>
            
            <label for="gender">Gender:</label>
            <select name="gender" id="gender" required>
                <option value="1">Male</option>
                <option value="2">Female</option>
            </select><br>
            
            <label for="intake">Intake Type:</label>
            <select name="intake" id="intake" required>
                <option value="1">Permanent</option>
                <option value="2">Temporary</option>
            </select><br>
            
            <label for="diet">Diet:</label>
            <select name="diet" id="diet" required>
                <option value="Herbivore">Herbivore</option>
                <option value="Carnivore">Carnivore</option>
                <option value="Omnivore">Omnivore</option>
            </select><br>
            
            <label for="intake_date">Intake Date:</label>
            <input type="date" name="intake_date" id="intake_date" required><br>
            
            <label for="release_date">Release Date (if temporary):</label>
            <input type="date" name="release_date" id="release_date"><br>
            
            <label for="enclosure_id">Enclosure:</label>
            <select name="enclosure_id" id="enclosure_id" required>
                <option value="">Select an enclosure</option>
                <?php
                // Re-query enclosures for the add form
                $enclosures_res = $conn->query("
                    SELECT e.enclosure_id, e.Type, e.Habitat, e.capacity, 
                           COUNT(a.animal_id) as current_animals
                    FROM enclosure e 
                    LEFT JOIN animal a ON e.enclosure_id = a.enclosure_id 
                    GROUP BY e.enclosure_id 
                    HAVING current_animals < e.capacity OR e.capacity = 0
                    ORDER BY e.enclosure_id
                ");
                while($enclosure = $enclosures_res->fetch_assoc()) {
                    $available = $enclosure['capacity'] - $enclosure['current_animals'];
                    echo "<option value='" . $enclosure['enclosure_id'] . "'>ID: " . $enclosure['enclosure_id'] . 
                         " - " . $enclosure['Type'] . " (" . $enclosure['Habitat'] . ") - Available: " . 
                         $available . "/" . $enclosure['capacity'] . "</option>";
                }
                ?>
            </select><br>
            
            <input type="submit" name="add_animal" value="Add Animal" class="button">
        </form>
    </main>
</div>
</body>
</html>