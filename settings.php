<?php
session_start();
include('connection.php');
if (!isset($_SESSION["username"])) {
    header('Location:index.php');
}
$sql = "SELECT * FROM user WHERE id=" . $_SESSION['id'];
$execute = $con->query($sql);
$row = $execute->fetch_assoc();
$username = $row['username'];
$password = $row['password'];


$usernameErr = $passwordErr = "";
$check = true;

if (isset($_POST['submit'])) {

    if (!empty($_POST['username'])) {
        if (strlen($_POST['username']) <= 20) {
            $newUsername = $_POST['username'];
        } else {
            $username = "";
            $usernameErr = "Username must be shorter than 20 chars";
            $check = false;
        }
    } else {
        $username = "";
        $usernameErr = "Username is empty";
        $check = false;
    }
    if (!empty($_POST['password'])) {
        if (strlen($_POST['password']) <= 20) {
            $newPassword = $_POST['password'];
        } else {
            $password = "";
            $passwordErr = "Password must be shorter than 20 chars";
            $check = false;
        }
    } else {
        $password = "";
        $passwordErr = "Password is empty";
        $check = false;
    }

    if ($check) {
        $sql = "UPDATE user SET username=?, password=? WHERE id=?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ssi", $newUsername, $newPassword, $_SESSION['id']);
        $stmt->execute();
        $stmt->close();
        $_SESSION["username"] = $newUsername;
        header('Location:shop.php');
    }
}

?>
<html>

<head>
    
    <link rel="stylesheet" type="text/css" href="styles.css" />
    <title>Settings</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">

    <script>
        function checkUsername() {
            let name = document.getElementById("username").value;
            if (name.length > 20) {
                document.getElementById("usernameErr").innerText = "Username must be below 20 chars";
            } else {
                document.getElementById("usernameErr").innerText = "";
            }
        }

        function checkPassword() {
            let price = document.getElementById("password").value;
            if (price.length > 20) {
                document.getElementById("passwordErr").innerText = "Password must be below 20 chars";
            } else {
                document.getElementById("passwordErr").innerText = "";
            }
        }

        function passChange() {
            if (document.getElementById("password").type == "password") {
                document.getElementById("password").type = "text";
            } else {
                document.getElementById("password").type = "password";
            }
        }
    </script>
</head>

<body>
    <div class="header">
        <div class="box">
            <h1>Settings</h1>
            <p>Update your account details</p>
            <a href="shop.php"><input type="button" value="Shop"></a>
        </div>
    </div>

    <div class="form">
        <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
            <span>Username</span> <input onkeypress="checkUsername()" value="<?php echo $username ?>" type="text" id="username" name="username">
            <span class="error" id="usernameErr"><?php echo $usernameErr ?></span><br>

            <span>Password</span> <input onkeypress="checkPassword()" value="<?php echo $password ?>" type="password" id="password" name="password">
            <button type="button" onclick="passChange()" style="background-color: #1e90ff; border-radius:5px;">
                <img src="https://img.icons8.com/ios-filled/50/private-lock.png" alt="show-password" height="40px" width="40px">

            </button>
            <span class="error" id="passwordErr"><?php echo $passwordErr ?></span><br>
            <input type="submit" name="submit" value="Update">
        </form>
    </div>

</body>

</html>