<?php
session_start();
include('connection.php');

if (isset($_SESSION["username"])) {
    header('Location:shop.php');
}
$usernameErr = $passwordErr = $username = $password = "";
$check = true;

if (isset($_POST['submit'])) {
    if (!empty($_POST['username'])) {
        $username = $_POST['username'];
    } else {
        $usernameErr = "Username is empty!";
        $check = false;
    }
    if (!empty($_POST['password'])) {
        $password = $_POST['password'];
    } else {
        $passwordErr = "Password is empty!";
        $check = false;
    }
    if ($check) {
        $sql = "SELECT * FROM user";
        $execute = $con->query($sql);
        while ($row = $execute->fetch_assoc()) {
            if ($row['username'] == $username && $row['password'] == $password) {
                $_SESSION["username"] = $username;
                $_SESSION["type"] = $row['type'];
                $_SESSION["id"] = $row['id'];
                header('Location:shop.php');
            }
        }
        $passwordErr = "Wrong password!";
    }
}

?>
<html>

<head>
    <link rel="stylesheet" type="text/css" href="styles.css" />
    <title>Welcome</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
</head>

<body>
    <div class="header">
        <div class="box">
            <h1>Welcome to the BookStore</h1>
            <p>Please login to access the website.</p>
        </div>
    </div>
    <div class="form">
        <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
            <span>Username</span>
            <input type="text" id="username" name="username" value="<?php echo $username ?>" placeholder="Enter username...">
            <span class="error"><?php echo $usernameErr ?></span>
            <br>
            <span>Password</span>
            <input type="password" id="password" name="password" value="<?php echo $password ?>" placeholder="Enter password...">
            <span class="error"><?php echo $passwordErr ?></span>
            <br>
            <input name="submit" type="submit" value="Login">
            <a href="register.php"><input type="button" value="Register"></a>
        </form>
    </div>
</body>

</html>