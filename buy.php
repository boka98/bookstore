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
    $user = $_SESSION['id'];
    $date = date('Y-m-d');
    $sql = "INSERT INTO sale(id, user, books, date_bought) VALUES (NULL, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("iis", $user, $id, $date);

    $stmt->execute();
    $stmt->close();
    header('Location:shop.php');
}

$sql = "SELECT name, price FROM books WHERE id=" . $id;
$execute = $con->query($sql);
$row = $execute->fetch_assoc();
$name = $row['name'];
$price = $row['price'];
?>

<html>

<head>
    <link rel="stylesheet" type="text/css" href="styles.css" />
    <title>Buy book</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">

</head>

<body>
    <div class="header">
        <div class="box">
            <h1>Buy Book</h1>
            <p>Are you sure you want to purchase <?php echo $name ?> for <?php echo $price ?>€ ?</p>
        </div>
    </div>
    <div class="form">
        <form method="post">
            <input type="hidden" value="<?php echo $id ?>" name="id">
            <input type="submit" name="submit" value="Buy">
            <a href="shop.php"><input type="button" value="Cancel" style="background-color: #af2b24"></a>
        </form>
    </div>
</body>

</html>