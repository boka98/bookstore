<?php
session_start();
include('connection.php');
if (!isset($_SESSION["username"]) || $_SESSION["username"] != "admin") {
    header('Location:index.php');
}
$covertypeAdded = $authorAdded = $covertypeErr = $authorErr = "";

if (isset($_POST['delete'])) {
    $user = $_POST['user'];
    if ($user != "null") {

        $sql = "SET FOREIGN_KEY_CHECKS=0";
        $execute = $con->query($sql);
        $sql = "DELETE from user where id = '$user'";
        $execute = $con->query($sql);
        $sql = "SET FOREIGN_KEY_CHECKS=1";
        $execute = $con->query($sql);
        header('Location:admin.php');
    }
}

if (isset($_POST['moderator'])) {
    $user = $_POST['user'];
    if ($user != "null") {
        $sql = "UPDATE user SET type = 1 WHERE id = '$user'";
        $execute = $con->query($sql);
        header('Location:admin.php');
    }
}

if (isset($_POST['download'])) {

    header('Content-Disposition: attachment; filename="books.txt"');
    $sql = "SELECT b.name AS name,u.username AS seller,b.price AS price
                FROM books b,user u,sale s 
                WHERE b.user = u.id AND s.books=b.id";
    $execute = $con->query($sql);
    $count = 0;
    echo "List of sold books:";
    echo "\n";
    while ($row = $execute->fetch_assoc()) {
        $count++;
        echo $count . '. ' . $row['name'] . ': ' . $row['price'] . '€ sold by ' . $row['seller'];
        echo "\n";
    }
    return;
}

if (isset($_POST['submitCovertype'])) {
    if (!empty($_POST['covertype'])) {
        if (strlen($_POST['covertype']) <= 30) {

            $covertype = $_POST['covertype'];
            $checkCovertype = true;

            $sql = "SELECT id, name FROM covertype";
            $execute = $con->query($sql);
            while ($row = $execute->fetch_assoc()) {
                if (strtolower($covertype) == strtolower($row['name'])) {
                    $checkCovertype = false;
                    $covertypeErr = "That covertype already exists";
                    break;
                }
            }
            if ($checkCovertype) {
                $covertypeAdded = "Added new covertype";
                $sql = "INSERT INTO covertype (id, name) VALUES (NULL, ?)";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("s", $covertype);

                $stmt->execute();
                $stmt->close();
            }
        } else {
            $covertypeErr = "Name must be shorter than 30 chars";
        }
    } else {
        $covertype = "Covertype can't be empty";
    }
}

if (isset($_POST['submitAuthor'])) {
    if (!empty($_POST['author'])) {
        if (strlen($_POST['author']) <= 30) {
            $author = $_POST['author'];
            $checkAuthor = true;

            $sql = "SELECT id, name FROM author";
            $execute = $con->query($sql);
            while ($row = $execute->fetch_assoc()) {
                if (strtolower($author) == strtolower($row['name'])) {
                    $checkAuthor = false;
                    $authorErr = "That author already exists";
                    break;
                }
            }
            if ($checkAuthor) {
                $authorAdded = "Added author";
                $sql = "INSERT INTO author (id, name) VALUES (NULL, ?)";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("s", $author);

                $stmt->execute();
                $stmt->close();
            }
        } else {
            $authorErr = "Name must be shorter than 30 chars";
        }
    } else {
        $authorErr = "Author name can't be empty";
    }
}
?>
<html>

<head>
    <link rel="stylesheet" type="text/css" href="styles.css" />
    <title>Admin Settings</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">

    <script>
        function checkCovertype() {
            let name = document.getElementById("covertype").value;
            if (name.length > 30) {
                document.getElementById("covertypeErr").innerText = "Name must be shorter than 30 chars";
            } else {
                document.getElementById("covertypeErr").innerText = "";
            }
        }

        function checkAuthor() {
            let name = document.getElementById("author").value;
            if (name.length > 30) {
                document.getElementById("authorErr").innerText = "Name must be shorter than 30 chars";
            } else {
                document.getElementById("authorErr").innerText = "";
            }
        }
    </script>
</head>

<body>
    <div class="header">
        <div class="box">
            <h1>Admin settings</h1>
            <p>Add new authors and covertypes</p>
            <a href="shop.php"><input type="button" value="Shop"></a>
        </div>

    </div>
    <div>
        <form class="form" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
            <br><span>Delete a user: </span>
            <select class="combo" name="user">
                <option value="null">Select...</option>
                <?php
                $sql = "SELECT id, username FROM user WHERE type=2";
                $execute = $con->query($sql);
                while ($row = $execute->fetch_assoc()) {
                    echo "<option value='" . $row['id'] . "'>" . $row['username'] . "</option>";
                }
                ?>
            </select>
            <input style="background-color: #af2b24" type="submit" value="Delete" name="delete" class="delete">
            <br>
            <br><span>Make user a moderator: </span>
            <select class="combo" name="user">
                <option value="null">Select...</option>
                <?php
                $sql = "SELECT id, username FROM user WHERE type=2";
                $execute = $con->query($sql);
                while ($row = $execute->fetch_assoc()) {
                    echo "<option value='" . $row['id'] . "'>" . $row['username'] . "</option>";
                }
                ?>
            </select>
            <input style="background-color: #2AAA8A" type="submit" value="Add" name="moderator">
            <br>
            <span>Sold items report: </span><input type="submit" value="Download" name="download"><br><br><br>
            <span>Add cover type</span>
            <input type="text" id="covertype" name="covertype" placeholder="Add covertype..." onkeypress="checkCovertype()">
            <input type="submit" value="Add" name="submitCovertype"><span class="error" id="covertypeErr"><?php echo $covertypeErr ?></span><span class="added"><?php echo $covertypeAdded ?></span><br>
            <span>Add author</span>
            <input type="text" id="author" name="author" placeholder="Add author..." onkeypress="checkAuthor()">
            <input type="submit" value="Add" name="submitAuthor"><span class="error" id="authorErr"><?php echo $authorErr ?></span><span class="added"><?php echo $authorAdded ?></span>
            <br>
        </form>
        <form>

        </form>
    </div>
</body>

</html>