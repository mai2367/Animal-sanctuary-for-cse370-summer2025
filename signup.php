<?php include("db.php"); ?>
<!DOCTYPE html>
<html>
<head><title>Sign Up</title><link rel="stylesheet" href="style.css"></head>
<body>
<h2>Sign Up</h2>
<form method="post">
  Username: <input type="text" name="username" required><br>
  Password: <input type="password" name="password" required><br>
  Phone: <input type="text" name="phone" required><br>
  Email: <input type="email" name="email" required><br>
  Job: <input type="text" name="job" required><br>
  <input type="submit" name="signup" value="Sign Up">
</form>
<a href="index.php">Back to login</a>

<?php
if(isset($_POST['signup'])){
    $u = $_POST['username'];
    $p = $_POST['password'];
    if(strlen($p) < 8){
        echo "<p>Password must be at least 8 characters</p>";
    } else {
        $stmt = $conn->prepare("SELECT * FROM staff WHERE Username=?");
        $stmt->bind_param("s", $u);
        $stmt->execute();
        if($stmt->get_result()->num_rows > 0){
            echo "<p>Username already exists</p>";
        } else {
            $stmt = $conn->prepare("INSERT INTO staff (Username, phone_no, email, job, password) VALUES (?,?,?,?,?)");
            $stmt->bind_param("sssss", $u, $_POST['phone'], $_POST['email'], $_POST['job'], $p);
            if($stmt->execute()) echo "<p>Account created!</p>";
        }
    }
}
?>
</body>
</html>
