<?php
session_start();
include('connection.php');
if (!isset($_SESSION["username"])) {
    header('Location:index.php');
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
}


$nameErr = $priceErr = $authorErr = "";
$check = true;
if (isset($_POST['submit'])) {
    if (!empty($_POST['name'])) {
        if (strlen($_POST['name']) <= 50) {
            $newName = $_POST['name'];
        } else {
            $nameErr = "Name must be under 50 chars";
            $check = false;
        }
    } else {
        $nameErr = "Name is empty";
        $check = false;
    }
    if (!empty($_POST['price'])) {
        if (!is_numeric($_POST['price'])) {
            $priceErr = "Price must be a number";
            $check = false;
        } else {
            if (strlen($_POST['price']) <= 4) {
                $price = $_POST['price'];
            } else {
                $priceErr = "Price is too high";
                $check = false;
            }
        }
    } else {
        $priceErr = "Price is empty";
        $check = false;
    }

    if (!empty($_POST['author'])) {
        $author = $_POST['author'];
        if (strlen($author) > 30) {
            $authorErr = "Author name must be below 30 chars";
            $check = false;
        }
        $sql = "SELECT id FROM author WHERE name = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $author);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $author = $row['id'];
        } else {
            $stmt->close();
            $sql = "INSERT INTO author (id, name) VALUES (NULL, ?)";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("s", $author);
            $stmt->execute();
            $author = $stmt->insert_id;
        }
        $stmt->close();
    } else {
        $authorErr = "You have to enter an author";
        $check = false;
    }

    if ($check) {
        $id = $_POST['id'];
        $newCovertype = $_POST['covertype'];
        $newPrice = $_POST['price'];
        $sql = "UPDATE books SET name=?, price=?, covertype=?, author=? WHERE id=?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ssssi", $newName, $newPrice, $newCovertype, $author, $id);

        $stmt->execute();
        $stmt->close();
        header('Location:shop.php');
    }
}


$sql = "SELECT * FROM books WHERE id=" . $id;
$execute = $con->query($sql);
$row = $execute->fetch_assoc();
$name = $row['name'];
$authorId = $row['author'];
$covertype = $row['covertype'];
$seller = $row['user'];
$price = $row['price'];

$sql = "SELECT name FROM author where id =" . $authorId;
$execute = $con->query($sql);
$row = $execute->fetch_assoc();
$author = $row['name'];

?>

<html>

<head>

    <link rel="stylesheet" type="text/css" href="styles.css" />
    <title>Edit</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">

    <script>
        function checkName() {
            let name = document.getElementById("name").value;
            if (name.length > 30) {
                document.getElementById("nameErr").innerText = "Name must be under 30 chars";
            } else {
                document.getElementById("nameErr").innerText = "";
            }
        }

        function checkPrice() {
            let price = document.getElementById("price").value;
            if (isNaN(price)) {
                document.getElementById("priceErr").innerText = "Price must be a number";
            } else {
                document.getElementById("priceErr").innerText = "";
            }
        }

        function checkAuthor() {
            let author = document.getElementById("author").value;
            if (author.length > 30) {
                document.getElementById("authorErr").innerText = "Author name must be below 30 chars";
            } else {
                document.getElementById("authorErr").innerText = "";
            }
        }
    </script>
</head>

<body>
    <div class="header">
        <div class="box">
            <h1>Edit Book</h1>
            <p>Update book details</p>
            <a href="shop.php"><input type="button" value="Shop"></a>
        </div>
    </div>
    <div class="form">
        <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
            <input type="hidden" name="id" value="<?php echo $id ?>">
            <span>Name</span> <input onkeypress="checkName()" value="<?php echo $name ?>" type="text" id="name" name="name"><span class="error" id="nameErr"><?php echo $nameErr ?></span><br>
            <span>Covertype</span>
            <select class="combo" name="covertype">
                <?php
                $sql = "SELECT id, name FROM covertype";
                $result = $con->query($sql);

                while ($row = $result->fetch_assoc()) {
                    $selected = "";
                    if ($row['id'] == $covertype) {
                        $selected = "selected";
                    }
                    echo "<option value='" . $row['id'] . "' " . $selected . ">" . $row['name'] . "</option>";
                }
                ?>
            </select>
            <br>
            <?php
            $sql = "SELECT username FROM user WHERE id=" . $seller;
            $execute = $con->query($sql);
            $row = $execute->fetch_assoc();
            $sellerName = $row['username'];
            ?>
            <span>Author</span>
            <input type="text" id="author" name="author" placeholder="Enter author..." onkeyup="checkAuthor()" value="<?php echo $author ?>">
            <span class="error" id="authorErr"> <?php echo $authorErr ?></span><br>
            <span>Seller</span> <input value="<?php echo $sellerName ?>" type="text" disabled><br>
            <span>Price</span> <input id="price" onkeyup="checkPrice()" value="<?php echo $price ?>" type="text" name="price">
            <span class="error" id="priceErr"><?php echo $priceErr ?></span><br>
            <input type="submit" name="submit" value="Update Details">
        </form>
    </div>
</body>

</html>