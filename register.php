<?php
//reCaptcha handling----------------------------------


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
    if ($_SESSION['logged_in'] == true) {
        redirectSuc();
    }
}
//------------------------------------------------------

function redirectSuc()
{
    header("Location: https://gunterhans.cloudapp.net/success.php");
    die();
}

function printForm()
{
    ?>
<form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Username">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password"
                   placeholder="Password">
        </div>
        <button type="button" class="btn btn-danger" onClick="location.href='index.php'">Return</button>
        <button type="submit" name="submit" class="btn btn-default pull-right">Submit</button>
    </form><?php
}

function printReturnButton()
{
    ?><br/><br/>
    <button type="button" class="btn btn-success" onClick="location.href='index.php'">Log In</button><?php
}

?>

<!DOCTYPE html>
<head>
    <!--Bootstrap -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap/css/bootstrap-theme.min.css">
    <script src="bootstrap/js/bootstrap.min.js"></script>

    <link rel="stylesheet" href="style.css">

    <script src='https://www.google.com/recaptcha/api.js'></script>

    <title>Lab 8: Philip Gilbreth</title>
</head>
<body>
<div class="container container-table">
    <div class="row vertical-center-row">
        <div class="col-md-4 col-md-offset-4">
            <div class="text-center">
                <h3>Register</h3>
            </div>
            <?php
            if (isset($_POST["submit"])) {
                include("../secure/db.php");
                $link = mysqli_connect(HOST, USERNAME, PASSWORD, DBNAME);
                $sql = "INSERT INTO user(username,salt,hashed_password) VALUES (?,?,?)";
                if ($stmt = mysqli_prepare($link, $sql)) {
                    $user = $_POST["username"];
                    $salt = mt_rand();
                    $hpass = password_hash($salt . $_POST["password"], PASSWORD_BCRYPT) or die("bind param");
                    mysqli_stmt_bind_param($stmt, "sss", $user, $salt, $hpass) or die("bind param");
                    if (mysqli_stmt_execute($stmt)) {
                        echo "Hi " . $user . ", thanks for registering! You can now log in at the log in page.";
                        printReturnButton();
                    } else {
                        echo "Sorry, this username is taken.";
                        printForm();
                    }
                } else {
                    die ("Prepare failed: " . $stmt->error);
                }
            } else {
                printForm();
            } ?>
        </div>
    </div>
</div>
</body>