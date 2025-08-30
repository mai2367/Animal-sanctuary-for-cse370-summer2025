<?php 
include("db.php"); 
if(!isset($_SESSION['user'])){
    header("Location:index.php");
    exit;
}

// Get current page for active navigation
$current_page = basename($_SERVER['PHP_SELF']);

// Process shift deletion
if(isset($_POST['delete_shift'])) {
    $username = $_POST['username'];
    $date = $_POST['date'];
    
    // Check if there are any tasks assigned to this shift
    $check_tasks = $conn->prepare("SELECT 
        (SELECT COUNT(*) FROM feeds WHERE username=? AND feed_date=?) as feed_count,
        (SELECT COUNT(*) FROM cleans WHERE username=? AND clean_date=?) as clean_count");
    $check_tasks->bind_param("ssss", $username, $date, $username, $date);
    $check_tasks->execute();
    $result = $check_tasks->get_result();
    $task_counts = $result->fetch_assoc();
    
    if($task_counts['feed_count'] > 0 || $task_counts['clean_count'] > 0) {
        echo "<div class='alert alert-error'><p>Error: Cannot delete shift. There are tasks assigned to this shift. Please delete the tasks first.</p></div>";
    } else {
        $stmt = $conn->prepare("DELETE FROM shift WHERE username=? AND date=?");
        $stmt->bind_param("ss", $username, $date);
        
        if($stmt->execute()) {
            echo "<div class='alert alert-success'><p>Shift deleted successfully!</p></div>";
            echo "<meta http-equiv='refresh' content='1'>";
        } else {
            echo "<div class='alert alert-error'><p>Error deleting shift: " . $conn->error . "</p></div>";
        }
    }
}

// Process feeding task deletion
if(isset($_POST['delete_feed'])) {
    $username = $_POST['username'];
    $animal_id = $_POST['animal_id'];
    $feed_time = $_POST['feed_time'];
    $feed_date = $_POST['feed_date'];
    
    $stmt = $conn->prepare("DELETE FROM feeds WHERE username=? AND animal_id=? AND feed_time=? AND feed_date=?");
    $stmt->bind_param("siss", $username, $animal_id, $feed_time, $feed_date);
    
    if($stmt->execute()) {
        echo "<div class='alert alert-success'><p>Feeding task deleted successfully!</p></div>";
        echo "<meta http-equiv='refresh' content='1'>";
    } else {
        echo "<div class='alert alert-error'><p>Error deleting feeding task: " . $conn->error . "</p></div>";
    }
}

// Process cleaning task deletion
if(isset($_POST['delete_clean'])) {
    $username = $_POST['username'];
    $enclosure_id = $_POST['enclosure_id'];
    $clean_time = $_POST['clean_time'];
    $clean_date = $_POST['clean_date'];
    
    $stmt = $conn->prepare("DELETE FROM cleans WHERE username=? AND enclosure_id=? AND clean_time=? AND clean_date=?");
    $stmt->bind_param("siss", $username, $enclosure_id, $clean_time, $clean_date);
    
    if($stmt->execute()) {
        echo "<div class='alert alert-success'><p>Cleaning task deleted successfully!</p></div>";
        echo "<meta http-equiv='refresh' content='1'>";
    } else {
        echo "<div class='alert alert-error'><p>Error deleting cleaning task: " . $conn->error . "</p></div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Shifts</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function confirmDelete(action, details) {
            return confirm("Are you sure you want to delete this " + action + "?\n" + details + "\nThis action cannot be undone.");
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
        <h2>Shift Management</h2>
        
        <div class="card">
            <h3>All Shifts</h3>
            <table>
                <tr>
                    <th>Staff Member</th>
                    <th>Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Actions</th>
                </tr>
                <?php
                // Get all shifts with staff names
                $shifts_res = $conn->query("SELECT s.username, s.date, s.start, s.end, st.name 
                                           FROM shift s 
                                           JOIN staff st ON s.username = st.Username 
                                           ORDER BY s.date DESC, s.start ASC");
                while($shift = $shifts_res->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $shift['name'] . " (" . $shift['username'] . ")</td>
                            <td>" . $shift['date'] . "</td>
                            <td>" . $shift['start'] . "</td>
                            <td>" . $shift['end'] . "</td>
                            <td>
                                <form method='post' class='inline-form' onsubmit='return confirmDelete(\"shift\", \"Staff: " . $shift['name'] . "\\nDate: " . $shift['date'] . "\\nTime: " . $shift['start'] . " - " . $shift['end'] . "\")'>
                                    <input type='hidden' name='username' value='" . $shift['username'] . "'>
                                    <input type='hidden' name='date' value='" . $shift['date'] . "'>
                                    <input type='submit' name='delete_shift' value='Delete' class='button button-danger'>
                                </form>
                            </td>
                          </tr>";
                }
                ?>
            </table>
        </div>

        <div class="card">
            <h3>Assigned Feeding Schedule</h3>
            <table>
                <tr>
                    <th>Staff Member</th>
                    <th>Animal</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Actions</th>
                </tr>
                <?php
                // Get all feeding assignments with staff and animal names
                $feeds_res = $conn->query("SELECT f.username, f.animal_id, f.feed_date, f.feed_time, st.name as staff_name, a.Name as animal_name 
                                          FROM feeds f 
                                          JOIN staff st ON f.username = st.Username 
                                          JOIN animal a ON f.animal_id = a.animal_id 
                                          ORDER BY f.feed_date DESC, f.feed_time ASC");
                while($feed = $feeds_res->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $feed['staff_name'] . " (" . $feed['username'] . ")</td>
                            <td>" . $feed['animal_name'] . " (ID: " . $feed['animal_id'] . ")</td>
                            <td>" . $feed['feed_date'] . "</td>
                            <td>" . $feed['feed_time'] . "</td>
                            <td>
                                <form method='post' class='inline-form' onsubmit='return confirmDelete(\"feeding task\", \"Staff: " . $feed['staff_name'] . "\\nAnimal: " . $feed['animal_name'] . "\\nDate: " . $feed['feed_date'] . "\\nTime: " . $feed['feed_time'] . "\")'>
                                    <input type='hidden' name='username' value='" . $feed['username'] . "'>
                                    <input type='hidden' name='animal_id' value='" . $feed['animal_id'] . "'>
                                    <input type='hidden' name='feed_time' value='" . $feed['feed_time'] . "'>
                                    <input type='hidden' name='feed_date' value='" . $feed['feed_date'] . "'>
                                    <input type='submit' name='delete_feed' value='Delete' class='button button-danger'>
                                </form>
                            </td>
                          </tr>";
                }
                ?>
            </table>
        </div>

        <div class="card">
            <h3>Assigned Cleaning Schedule</h3>
            <table>
                <tr>
                    <th>Staff Member</th>
                    <th>Enclosure</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Actions</th>
                </tr>
                <?php
                // Get all cleaning assignments with staff names
                $cleans_res = $conn->query("SELECT c.username, c.enclosure_id, c.clean_date, c.clean_time, st.name as staff_name, e.Type as enclosure_type 
                                           FROM cleans c 
                                           JOIN staff st ON c.username = st.Username 
                                           JOIN enclosure e ON c.enclosure_id = e.enclosure_id 
                                           ORDER BY c.clean_date DESC, c.clean_time ASC");
                while($clean = $cleans_res->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $clean['staff_name'] . " (" . $clean['username'] . ")</td>
                            <td>" . $clean['enclosure_type'] . " (ID: " . $clean['enclosure_id'] . ")</td>
                            <td>" . $clean['clean_date'] . "</td>
                            <td>" . $clean['clean_time'] . "</td>
                            <td>
                                <form method='post' class='inline-form' onsubmit='return confirmDelete(\"cleaning task\", \"Staff: " . $clean['staff_name'] . "\\nEnclosure: " . $clean['enclosure_type'] . "\\nDate: " . $clean['clean_date'] . "\\nTime: " . $clean['clean_time'] . "\")'>
                                    <input type='hidden' name='username' value='" . $clean['username'] . "'>
                                    <input type='hidden' name='enclosure_id' value='" . $clean['enclosure_id'] . "'>
                                    <input type='hidden' name='clean_time' value='" . $clean['clean_time'] . "'>
                                    <input type='hidden' name='clean_date' value='" . $clean['clean_date'] . "'>
                                    <input type='submit' name='delete_clean' value='Delete' class='button button-danger'>
                                </form>
                            </td>
                          </tr>";
                }
                ?>
            </table>
        </div>

        <div class="card">
            <h3>Add New Shift</h3>
            <form method="post">
                Staff Member: 
                <select name="username" required>
                    <option value="">Select a staff member</option>
                    <?php
                    // Get all staff members with names
                    $staff_res = $conn->query("SELECT Username, name FROM staff ORDER BY name");
                    while($staff = $staff_res->fetch_assoc()) {
                        $selected = ($staff['Username'] == $_SESSION['user']) ? 'selected' : '';
                        echo "<option value='" . $staff['Username'] . "' $selected>" . $staff['name'] . " (" . $staff['Username'] . ")</option>";
                    }
                    ?>
                </select><br>
                Date: <input type="date" name="date" required><br>
                Start: <input type="time" name="start" required><br>
                End: <input type="time" name="end" required><br>
                <input type="submit" name="add_shift" value="Add Shift" class="button">
            </form>
        </div>

        <?php
        if(isset($_POST['add_shift'])){
            $username = $_POST['username'];
            $date = $_POST['date']; 
            $start = $_POST['start']; 
            $end = $_POST['end'];
            
            // Validate that start time is before end time
            if(strtotime($start) >= strtotime($end)) {
                echo "<div class='alert alert-error'><p>Error: Start time must be before end time. Please try again.</p></div>";
            }
            elseif($date <= date("Y-m-d")){ 
                echo "<div class='alert alert-error'><p>Error: Date must be in the future.</p></div>"; 
            }
            else {
                $stmt = $conn->prepare("SELECT * FROM shift WHERE username=? AND date=? AND ( (start < ? AND end > ?) OR (start < ? AND end > ?) )");
                $stmt->bind_param("ssssss", $username, $date, $end, $start, $end, $start);
                $stmt->execute();
                if($stmt->get_result()->num_rows > 0){ 
                    echo "<div class='alert alert-error'><p>Error: Shift overlaps with existing shift for this staff member!</p></div>"; 
                }
                else {
                    $stmt = $conn->prepare("INSERT INTO shift (start, end, date, username) VALUES (?,?,?,?)");
                    $stmt->bind_param("ssss", $start, $end, $date, $username);
                    if($stmt->execute()) {
                        echo "<div class='alert alert-success'><p>Shift added successfully!</p></div>";
                        echo "<meta http-equiv='refresh' content='1'>";
                    } else {
                        echo "<div class='alert alert-error'><p>Error adding shift: " . $conn->error . "</p></div>";
                    }
                }
            }
        }
        ?>
        
        <div class="card">
            <h3>Assign Feeding</h3>
            <form method="post">
                Staff Member: 
                <select name="username" required>
                    <option value="">Select a staff member</option>
                    <?php
                    // Get all staff members with names
                    $staff_res = $conn->query("SELECT Username, name FROM staff ORDER BY name");
                    while($staff = $staff_res->fetch_assoc()) {
                        echo "<option value='" . $staff['Username'] . "'>" . $staff['name'] . " (" . $staff['Username'] . ")</option>";
                    }
                    ?>
                </select><br>
                Animal: 
                <select name="aid" required>
                    <option value="">Select an animal</option>
                    <?php
                    // Get all animals
                    $animals_res = $conn->query("SELECT animal_id, Name, Species FROM animal ORDER BY Name");
                    while($animal = $animals_res->fetch_assoc()) {
                        echo "<option value='" . $animal['animal_id'] . "'>" . $animal['Name'] . " (" . $animal['Species'] . ") - ID: " . $animal['animal_id'] . "</option>";
                    }
                    ?>
                </select><br>
                Date: <input type="date" name="feed_date" required><br>
                Time: 
                <select name="feed_time" required>
                    <option value="">Select a time</option>
                    <?php
                    // Generate time options in 30-minute increments
                    for ($hour = 0; $hour < 24; $hour++) {
                        for ($minute = 0; $minute < 60; $minute += 30) {
                            $time_value = sprintf("%02d:%02d", $hour, $minute);
                            echo "<option value=\"$time_value\">$time_value</option>";
                        }
                    }
                    ?>
                </select><br>
                <input type="submit" name="feed" value="Assign Feed" class="button">
            </form>
        </div>

        <div class="card">
            <h3>Assign Cleaning</h3>
            <form method="post">
                Staff Member: 
                <select name="username" required>
                    <option value="">Select a staff member</option>
                    <?php
                    // Get all staff members with names
                    $staff_res = $conn->query("SELECT Username, name FROM staff ORDER BY name");
                    while($staff = $staff_res->fetch_assoc()) {
                        echo "<option value='" . $staff['Username'] . "'>" . $staff['name'] . " (" . $staff['Username'] . ")</option>";
                    }
                    ?>
                </select><br>
                Enclosure: 
                <select name="eid" required>
                    <option value="">Select an enclosure</option>
                    <?php
                    // Get all enclosures
                    $enclosures_res = $conn->query("SELECT enclosure_id, Type, Habitat FROM enclosure ORDER BY enclosure_id");
                    while($enclosure = $enclosures_res->fetch_assoc()) {
                        echo "<option value='" . $enclosure['enclosure_id'] . "'>ID: " . $enclosure['enclosure_id'] . " - " . $enclosure['Type'] . " (" . $enclosure['Habitat'] . ")</option>";
                    }
                    ?>
                </select><br>
                Date: <input type="date" name="clean_date" required><br>
                Time: 
                <select name="clean_time" required>
                    <option value="">Select a time</option>
                    <?php
                    // Generate time options in 30-minute increments
                    for ($hour = 0; $hour < 24; $hour++) {
                        for ($minute = 0; $minute < 60; $minute += 30) {
                            $time_value = sprintf("%02d:%02d", $hour, $minute);
                            echo "<option value=\"$time_value\">$time_value</option>";
                        }
                    }
                    ?>
                </select><br>
                <input type="submit" name="clean" value="Assign Clean" class="button">
            </form>
        </div>

        <?php
        function within_shift($conn, $u, $d, $t){
            $stmt = $conn->prepare("SELECT * FROM shift WHERE username=? AND date=? AND start<=? AND end>=?");
            $stmt->bind_param("ssss", $u, $d, $t, $t);
            $stmt->execute();
            return $stmt->get_result()->num_rows > 0;
        }

        // Check if more than 2 people are assigned to the same feeding
        function check_feeding_limit($conn, $a, $d, $t) {
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM feeds WHERE animal_id=? AND feed_date=? AND feed_time=?");
            $stmt->bind_param("iss", $a, $d, $t);
            $stmt->execute();
            $result = $stmt->get_result();
            $count = $result->fetch_assoc()['count'];
            return $count < 2; // Return true if less than 2 people assigned
        }

        // Check if more than 2 people are assigned to the same cleaning slot
        function check_cleaning_limit($conn, $e, $d, $t) {
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM cleans WHERE enclosure_id=? AND clean_date=? AND clean_time=?");
            $stmt->bind_param("iss", $e, $d, $t);
            $stmt->execute();
            $result = $stmt->get_result();
            $count = $result->fetch_assoc()['count'];
            return $count < 2; // Return true if less than 2 people assigned
        }

        if(isset($_POST['feed'])){
            $u = $_POST['username'];
            $a = $_POST['aid'];
            $d = $_POST['feed_date'];
            $t = $_POST['feed_time'];
            
            if(within_shift($conn, $u, $d, $t)){
                if(check_feeding_limit($conn, $a, $d, $t)) {
                    $stmt = $conn->prepare("INSERT INTO feeds (username, animal_id, feed_time, feed_date) VALUES (?,?,?,?)");
                    $stmt->bind_param("siss", $u, $a, $t, $d);
                    if($stmt->execute()) {
                        echo "<div class='alert alert-success'><p>Feeding assigned successfully!</p></div>";
                        echo "<meta http-equiv='refresh' content='1'>";
                    } else {
                        echo "<div class='alert alert-error'><p>Error assigning feeding: " . $conn->error . "</p></div>";
                    }
                } else {
                    echo "<div class='alert alert-error'><p>Error: Maximum of 2 staff members can be assigned to feed the same animal at the same time.</p></div>";
                }
            } else {
                echo "<div class='alert alert-error'><p>Error: Cannot assign feeding outside of staff member's shift hours.</p></div>";
            }
        }

        if(isset($_POST['clean'])){
            $u = $_POST['username'];
            $e = $_POST['eid'];
            $d = $_POST['clean_date'];
            $t = $_POST['clean_time'];
            
            if(within_shift($conn, $u, $d, $t)){
                if(check_cleaning_limit($conn, $e, $d, $t)) {
                    $stmt = $conn->prepare("INSERT INTO cleans (username, enclosure_id, clean_time, clean_date) VALUES (?,?,?,?)");
                    $stmt->bind_param("siss", $u, $e, $t, $d);
                    if($stmt->execute()) {
                        echo "<div class='alert alert-success'><p>Cleaning assigned successfully!</p></div>";
                        echo "<meta http-equiv='refresh' content='1'>";
                    } else {
                        echo "<div class='alert alert-error'><p>Error assigning cleaning: " . $conn->error . "</p></div>";
                    }
                } else {
                    echo "<div class='alert alert-error'><p>Error: Maximum of 2 staff members can be assigned to clean the same enclosure at the same time.</p></div>";
                }
            } else {
                echo "<div class='alert alert-error'><p>Error: Cannot assign cleaning outside of staff member's shift hours.</p></div>";
            }
        }
        ?>
    </main>
</div>
</body>
</html>