<?php
session_start();
include('connection.php');
if (!isset($_SESSION["username"])) {
    header('Location:index.php');
}
$searchName = "";
if (!empty($_POST['searchName'])) {
    $searchName = $_POST['searchName'];
}
if (isset($_POST['submitRefresh'])) {
    $searchName = "";
}
?>
<html>

<head>
    <title>Shop</title>
    <link rel="stylesheet" type="text/css" href="styles.css" />
    <link rel="icon" type="image/x-icon" href="favicon.ico">
</head>

<body>
    <div class="header">
        <div class="box">
            <h1>Shop</h1>
            <p style="height: 40px">Welcome <?php echo $_SESSION["username"] ?> <a href="logout.php"><input type="button" value="Logout" id="logout"></a></p>
            <?php
            if ($_SESSION['username'] == "admin") {
                echo "<a href='admin.php'><input type='button' id='admin' value='Admin settings'></a>";
                echo " ";
            }
            echo "<a href='settings.php'><input type='button' value='Settings'></a>";
            ?>
        </div>
    </div>
    <table class="table">
        <tr>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                <th><input type="text" name="searchName" placeholder="Book Title" value="<?php echo $searchName ?>"></th>
                <th><select class="combo" name="searchCover">
                        <option value="null">Cover Type</option>
                        <?php
                        $sql = "SELECT id, name FROM covertype";
                        $execute = $con->query($sql);
                        while ($row = $execute->fetch_assoc()) {
                            $selected = "";
                            if (!isset($_POST['submitRefresh'])) {
                                if ($_POST['searchCover'] == $row['id']) {
                                    $selected = "selected";
                                }
                            }
                            echo "<option value='" . $row['id'] . "' " . $selected . ">" . $row['name'] . "</option>";
                        }
                        ?>
                    </select></th>
                <th>
                    <select class="combo" name="searchAuthor">
                        <option value="null">Author</option>
                        <?php
                        $sql = "SELECT id, name FROM author";
                        $execute = $con->query($sql);
                        while ($row = $execute->fetch_assoc()) {
                            $selected = "";
                            if (!isset($_POST['submitRefresh'])) {
                                if ($_POST['searchAuthor'] == $row['id']) {
                                    $selected = "selected";
                                }
                            }
                            echo "<option value='" . $row['id'] . "' " . $selected . ">" . $row['name'] . "</option>";
                        }

                        ?>
                    </select>
                </th>
                <th><input id="filter" type="submit" name="submit" value="Filter"></th>
                <th><input id="filter" type="submit" name="submitRefresh" value="Refresh"></th>
                <th></th>
                <th></th>
            </form>
        </tr>
        <tr>
            <th>Book Title</th>
            <th>Cover Type</th>
            <th>Author</th>
            <th>Price (€)</th>
            <th>Seller</th>
            <th>Date Added</th>
            <th><a href="add.php"><input type="button" value="Sell a Book"></a>
                <a href="list.php"><input type="button" value="Sale History"></a>
            </th>
        </tr>
        <?php


        $sql = "SELECT b.name AS name, c.name AS covertype, a.name AS author, u.username AS seller, b.price AS price, b.date_added AS date_added, b.id AS id
        FROM books b, covertype c, author a, user u 
        WHERE b.covertype = c.id AND b.author = a.id AND b.user = u.id
        AND b.id NOT IN(SELECT books FROM sale)";

        if (isset($_POST['submit'])) {
            if (!empty($_POST['searchName'])) {
                $searchName = $_POST['searchName'];
                $sql .= " AND b.name LIKE '" . "%" . $searchName . "%'";
            }
            if ($_POST['searchCover'] != 'null') {
                $searchCover = $_POST['searchCover'];
                $sql .= " AND b.covertype=" . $searchCover;
            }
            if ($_POST['searchAuthor'] != 'null') {
                $searchAuthor = $_POST['searchAuthor'];
                $sql .= " AND b.author=" . $searchAuthor;
            }
        }
        $execute = $con->query($sql);
        while ($row = $execute->fetch_assoc()) {
            echo "<tr>";
            echo '<td>' . $row['name'] . '</td>';
            echo '<td>' . $row['covertype'] . '</td>';
            echo '<td>' . $row['author'] . '</td>';
            echo '<td>' . $row['price'] . '</td>';
            echo '<td>' . $row['seller'] . '</td>';
            echo '<td>' . $row['date_added'] . '</td>';
            echo "<td>";
            if ($_SESSION['type'] != 1 && $_SESSION["username"] != $row['seller']) {
                echo '<a href="buy.php?id=' . $row['id'] . '"><input type="image" width="50" height="50" src="https://img.icons8.com/fluency/48/buy.png" alt="buy"/></a> ';
                echo '<br>';
                echo '<a href="book.php?id=' . $row['id'] . '"><input type="button" value="View Comments"></a> ';
            }
            if ($_SESSION['username'] == $row['seller'] || $_SESSION['type'] == 1) {
                echo '<a href="edit.php?id=' . $row['id'] . '"><input type="button" value="Edit"></a> ';
                echo '<a href="book.php?id=' . $row['id'] . '"><input type="button" value="View Comments"></a> ';
                echo '<a href="delete.php?id=' . $row['id'] . '"><input type="button" value="Delete" style="background-color: #af2b24;" class="delete"></a>';
            }
            echo "</td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>

</html>