<?php
include('connection.php');

$id = $_POST['id'];

echo "<h1>Bought</h1>
    <table class='table'>
        <tr>
            <th>Name</th>
            <th>Cover Type</th>
            <th>Author</th>
            <th>Price (€)</th>
            <th>Seller</th>
            <th>Date Bought</th>
        </tr>";

$sql = "SELECT b.name AS name, c.name AS covertype, a.name AS author, u.username AS seller, b.price AS price, s.date_bought AS date_bought
        FROM books b, covertype c, user u, author a, sale s
        WHERE b.covertype = c.id AND b.author = a.id AND b.user = u.id AND s.books = b.id AND s.user = " . $id;

$execute = $con->query($sql);
while ($row = $execute->fetch_assoc()) {
    echo "<tr>";
    echo '<td>' . $row['name'] . '</td>';
    echo '<td>' . $row['covertype'] . '</td>';
    echo '<td>' . $row['author'] . '</td>';
    echo '<td>' . $row['price'] . '</td>';
    echo '<td>' . $row['seller'] . '</td>';
    echo '<td>' . $row['date_bought'] . '</td>';
    echo "</tr>";
}

echo "</table>
    <h1>Sold</h1>
    <table class='table'>
        <tr>
            <th>Name</th>
            <th>Cover Type</th>
            <th>Author</th>
            <th>Price (€)</th>
            <th>Buyer</th>
            <th>Date Sold</th>
        </tr>";

$sql = "SELECT b.name AS name, c.name AS covertype,a.name AS author, u2.username AS buyer,b.price AS price, s.date_bought AS date_bought
                FROM books b,covertype c, user u,author a,sale s,user u2
                WHERE b.covertype = c.id AND b.author = a.id AND b.user = u.id AND u2.id=s.user AND s.books=b.id AND b.user=" . $id;
$execute = $con->query($sql);
while ($row = $execute->fetch_assoc()) {
    echo "<tr>";
    echo '<td>' . $row['name'] . '</td>';
    echo '<td>' . $row['covertype'] . '</td>';
    echo '<td>' . $row['author'] . '</td>';
    echo '<td>' . $row['price'] . '</td>';
    echo '<td>' . $row['buyer'] . '</td>';
    echo '<td>' . $row['date_bought'] . '</td>';
    echo "</tr>";
}
echo "</table>";
