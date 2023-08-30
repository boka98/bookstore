<?php
session_start();
include('connection.php');
if (!isset($_SESSION["username"])) {
    header('Location:index.php');
}
if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

if (isset($_POST['submit'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM books WHERE id=" . $id;
    $execute = $con->query($sql);
    header('Location:shop.php');
}

$sql = "SELECT name FROM books WHERE id=" . $id;
$execute = $con->query($sql);
$row = $execute->fetch_assoc();
$name = $row['name'];
?>
<html>

<head>
    <link rel="stylesheet" type="text/css" href="styles.css" />
    <title>Delete book</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
</head>

<body>
    <div class="header">
        <div class="box">
            <h1>Delete Book</h1>
            <p>Are you sure you want to delete <?php echo $name ?>?</p>
        </div>
    </div>
    <div class="form">
        <form method="post">
            <input type="hidden" value="<?php echo $id ?>" name="id">
            <input type="submit" name="submit" value="Delete" style="background-color: #af2b24;">
            <a href="shop.php"><input type="button" value="Cancel"></a>
        </form>
    </div>
</body>

</html>