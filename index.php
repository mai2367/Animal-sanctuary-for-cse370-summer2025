<?php include("db.php"); ?>
<!DOCTYPE html>
<html>
<head><title>Login</title><link rel="stylesheet" href="style.css"></head>
<body>
<h2>Login</h2>
<form method="post">
  Username: <input type="text" name="username" required><br>
  Password: <input type="password" name="password" required><br>
  <input type="submit" name="login" value="Login">
</form>
<a href="signup.php">Sign Up</a>

<?php
if(isset($_POST['login'])){
    $u = $_POST['username'];
    $p = $_POST['password'];
    $sql = "SELECT * FROM staff WHERE Username=? AND password=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $u, $p);
    $stmt->execute();
    $res = $stmt->get_result();
    if($res->num_rows > 0){
        $_SESSION['user'] = $u;
        header("Location: animals.php");
        exit;
    } else {
        echo "<p>Invalid login</p>";
    }
}
?>
</body>
</html>

