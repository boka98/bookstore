<?php
session_start();
include('connection.php');
if (isset($_SESSION["username"])) {
    header('Location:shop.php');
}
$username = $password = $usernameErr = $passwordErr = "";
$check = true;

if (isset($_POST['submit'])) {
    if (!empty($_POST['username'])) {
        if (strlen($_POST['username']) <= 20) {
            $username = $_POST['username'];
        } else {
            $usernameErr = "Username must be shorter than 20 chars";
            $check = false;
        }
    } else {
        $usernameErr = "Username is empty";
        $check = false;
    }
    if (!empty($_POST['password'])) {
        if (strlen($_POST['password']) <= 20) {
            $password = $_POST['password'];
        } else {
            $passwordErr = "Password must be shorter than 20 chars";
            $check = false;
        }
    } else {
        $passwordErr = "Password is empty";
        $check = false;
    }
    if ($check) {

        $check1 = true;
        $sql = "SELECT username FROM user";
        $execute = $con->query($sql);
        while ($row = $execute->fetch_assoc()) {
            if (strtolower($username) == strtolower($row['username'])) {
                $check1 = false;
                $usernameErr = "Username already exists.";
                break;
            }
        }
        if ($check1) {
            $sql = "INSERT INTO user(username, password, type) VALUES (?, ?, 2)";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();
            $stmt->close();

            $sql = "SELECT * FROM user WHERE username='" . $username . "'";
            $execute = $con->query($sql);
            $row = $execute->fetch_assoc();
            $_SESSION["username"] = $username;
            $_SESSION["type"] = 2;
            $_SESSION["id"] = $row['id'];
            header('Location:shop.php');
        }
    }
}
?>
<html>

<head>
    <link rel="stylesheet" type="text/css" href="styles.css" />
    <title>Register</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">

    <script>
        function checkUsername() {
            let username = document.getElementById("username").value;
            if (username.length > 20) {
                document.getElementById("usernameErr").innerText = "Username must be shorter than 20 chars";
            } else {
                document.getElementById("usernameErr").innerText = "";
            }
        }

        function checkPassword() {
            let password = document.getElementById("password").value;
            if (password.length > 20) {
                document.getElementById("passwordErr").innerText = "Password must be shorter than 20 chars";
            } else {
                document.getElementById("passwordErr").innerText = "";
            }
        }
    </script>
</head>

<body>
    <div class="header">
        <div class="box">
            <h1>Register</h1>
            <p>Make an account</p>
            <a href="shop.php"><input type="button" value="Welcome"></a>
        </div>
    </div>
    <div>
        <form class="form" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
            <span>Username</span>
            <input type="text" id="username" name="username" placeholder="Enter username..." onkeyup="checkUsername()">
            <span id="usernameErr" class="error"><?php echo $usernameErr ?></span>
            <br>
            <span>Password</span>
            <input type="password" id="password" name="password" placeholder="Enter password..." onkeyup="checkPassword()">
            <span id="passwordErr" class="error"><?php echo $passwordErr ?></span>
            <br>
            <input name="submit" type="submit" value="Register">
        </form>

    </div>
</body>

</html>