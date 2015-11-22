<?php
//HTTPS redirect---------------------------------------
if (!isset($_SERVER['HTTPS']) || !$_SERVER['HTTPS']) { // if request is not secure, redirect to secure url
    $url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('Location: ' . $url);
    //exit;
}
//------------------------------------------------------

//Logged-In redirect------------------------------------
session_start();
if (isset($_SESSION['logged_in'])) {
    if (!($_SESSION['logged_in'] == true)) {
        redirectErr();
    }
} else {
    redirectErr();
}
//------------------------------------------------------

function redirectErr()
{
    header("Location: https://cs3380-pg3f4.cloudapp.net/gunterhans/error.php");
    die();
}


?>

<!DOCTYPE html>
<head>
    <!--Bootstrap -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap/css/bootstrap-theme.min.css">
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>

    <link rel="stylesheet" href="style.css">

    <title>LSuccess!</title>
</head>
<body>
<div class="container container-table">
    <div class="row vertical-center-row">
        <div class="col-md-4 col-md-offset-4 text-center">
            <h1>Success!</h1>
            <?php
                if(isset($_SESSION['username'])){
                    echo "<p>Hello <em>". $_SESSION['username']. "</em>! You are logged in.</p>";
                }
            ?>
            <button type="button" class="btn btn-danger" onclick="window.location.replace('logout.php');" class="btn btn-default">Log Out</button>
        </div>
    </div>
</div>
</body>