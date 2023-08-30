<?php
session_start();
include('connection.php');
if (!isset($_SESSION["username"])) {
    header('Location:index.php');
    exit;
}
if (isset($_GET['id'])) {
    $id = $_GET['id'];
}


if (isset($_POST['delete'])) {
    $commentId = $_POST['commentId'];
    $sql = "DELETE FROM comments WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $commentId);
    if ($stmt->execute()) {
        header('Location: book.php?id=' . $id);
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}


$sql = "SELECT name FROM books WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $bookName = $row['name'];
} else {
    header('Location: shop.php');
    exit;
}
?>

<html>

<head>
    <link rel="stylesheet" type="text/css" href="styles.css" />
    <title>Book details</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
</head>

<body>
    <div class="header">
        <div class="box">
            <h1><?php echo $bookName; ?></h1>
            <a href="shop.php"><input type="button" value="Shop"></a>
        </div>
    </div>

    <form method="POST" class="form">
        <h2>Leave a Comment</h2>
        <label for="comment">Comment:</label><br>
        <textarea name="comment" required cols="50" rows="8"></textarea><br>
        <input type="submit" value="Submit">
        <br>
        <br>
        <h2>Comments</h2>
    </form>
</body>

</html>

<?php
$sql = "SELECT * FROM comments WHERE book_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {
        echo "<div class='comment'>";
        echo "<p><strong>" . $row['username'] . "</strong>: " . $row['comment'] . "</p>";



        if ($_SESSION["type"] == 1 || $_SESSION["username"] == $row['username']) {
            echo "<form method='POST'>";
            echo "<input type='hidden' name='commentId' value='" . $row['id'] . "'>";
            echo "<span style='margin-left: 40px;'><input type='submit'style='background-color: #af2b24;' name='delete' value='Delete'></span>";
            echo "</form>";
        }
        echo "</div>";
        echo "</br>";
    }
} else {
    echo '<div style="display: flex; justify-content: center; align-items: center;">';
    echo "<p style = 'text-align: center;'>No comments yet.</p>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_SESSION['username'];
    $comment = $_POST['comment'];

    $sql = "INSERT INTO comments (book_id, username, comment, date_created) 
            VALUES (?, ?, ?, NOW())";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("iss", $id, $username, $comment);
    if ($stmt->execute()) {
        header('Location: book.php?id=' . $id);
        exit;
    } else {
        echo "<script>alert('Comment must be under 300 characters!'); window.location.href='book.php?id=" . $id . "';</script>";
        exit;
    }
}

?>