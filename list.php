<?php
session_start();
include('connection.php');
if (!isset($_SESSION["username"])) {
    header('Location:index.php');
}
$id = $_SESSION['id'];

?>
<html>

<head>
    <link rel="stylesheet" type="text/css" href="styles.css" />
    <title>Sale History</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script>
        function fetch_data() {
            $.ajax({
                type: 'post',
                url: 'listAjax.php',
                data: {
                    id: <?php echo $id ?>,

                },
                success: function(response) {
                    document.getElementById("table").innerHTML = response;
                }
            });
        }
    </script>
</head>

<body onload="fetch_data()">
    <div class="header">
        <div class="box">
            <h1>Sale History</h1>
            <p>See all of your purchases and sales</p>
            <a href="shop.php"><input type="button" value="Shop"></a>
        </div>
    </div>
    <div class="show">
        <div id="table"></div>
    </div>
</body>

</html>