<?php
session_start();
include('connection.php');
if (!isset($_SESSION["username"])) {
    header('Location:index.php');
}

$name = $price = $author = $covertypeErr = $nameErr = $priceErr = $authorErr = "";
$check = true;

if (isset($_POST['submit'])) {
    if (!empty($_POST['name'])) {
        if (strlen($_POST['name']) <= 50) {
            $name = $_POST['name'];
        } else {
            $nameErr = "Name must be below 50 chars";
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

    if ($_POST['covertype'] != 'null') {
        $covertype = $_POST['covertype'];
    } else {
        $covertypeErr = "You have to choose the cover type";
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
        $dateAdded = date('Y-m-d');
        $sql = "INSERT INTO books (id, name, price, covertype, author, user, date_added) VALUES (NULL, ?, ?, ?, ?, ?, ?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("siiiis", $name, $price, $covertype, $author, $_POST['id'], $dateAdded);
        $stmt->execute();
        $stmt->close();
        header('Location:shop.php');
    }
}
?>

<html>

<head>
    <link rel="stylesheet" type="text/css" href="styles.css" />
    <title>Sell book</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">

    <script>
        function checkName() {
            let name = document.getElementById("name").value;
            if (name.length > 50) {
                document.getElementById("nameErr").innerText = "Name must be below 50 chars";
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
            <h1>Sell Book</h1>
            <p>Add a book to the marketplace</p>
            <a href="shop.php"><input type="button" value="Shop"></a>
        </div>
    </div>
    <div>
        <form class="form" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
            <input type="hidden" name="id" value="<?php echo $_SESSION['id'] ?>">
            <span>Name</span>

            <input type="text" id="name" name="name" placeholder="Enter name..." onkeyup="checkName()" value="<?php echo $name ?>">
            <span class="error" id="nameErr"> <?php echo $nameErr ?></span>
            <br>

            <span>Price</span>
            <input type="text" id="price" name="price" placeholder="Enter price..." onkeyup="checkPrice()" value="<?php echo $price ?>">
            <span class="error" id="priceErr"> <?php echo $priceErr ?></span>
            <br>

            <span>Cover Type</span>
            <select class="combo" name="covertype">
                <option value="null">Select cover type...</option>
                <?php
                $sql = "SELECT id, name FROM covertype";
                $execute = $con->query($sql);
                while ($row = $execute->fetch_assoc()) {
                    $selected = '';
                    if ($_POST['covertype'] == $row['id']) {
                        $selected = 'selected';
                    }
                    echo "<option value='" . $row['id'] . "' " . $selected . ">" . $row['name'] . "</option>";
                }
                ?>
            </select><span class="error"> <?php echo $covertypeErr ?></span>
            <br>

            <span>Author</span>
            <input type="text" id="author" name="author" placeholder="Enter author..." onkeyup="checkAuthor()" value="<?php echo $author ?>">
            <span class="error" id="authorErr"> <?php echo $authorErr ?></span>
            <br>
            <input name="submit" type="submit" value="Add">
        </form>
    </div>
</body>

</html>