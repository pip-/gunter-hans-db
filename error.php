<?php
    //Logged-In redirect------------------------------------
session_start();
    if(isset($_SESSION['logged_in'])){
        if($_SESSION['logged_in'] == true){
            redirectSuc();
        }
    }
    //------------------------------------------------------

    function redirectSuc(){
        header("Location: https://cs3380-pg3f4.cloudapp.net/gunterhans/success.php");
        die();
    }
?>

<!DOCTYPE html>
<head>
    <!--Bootstrap -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap/css/bootstrap-theme.min.css">
    <script src="bootstrap/js/bootstrap.min.js"></script>

    <link rel="stylesheet" href="style.css">

    <title>Error</title>
</head>
<body>
<div class="container container-table">
    <div class="row vertical-center-row">
        <div class="col col-md-4 col-md-offset-4">
            <h4>Protected Content. Please log in to access.</h4>
            <button type="button" class="btn btn-success" onClick="location.href='index.php'">Log In</button>
        </div>
    </div>
</div>
</body>