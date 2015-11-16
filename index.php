<?php
    //HTTPS redirect---------------------------------------
    if (!isset($_SERVER['HTTPS']) || !$_SERVER['HTTPS']) { // if request is not secure, redirect to secure url
        $url = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        header('Location: ' . $url);
        //exit;
    }
    //------------------------------------------------------

    //Logged-In redirect------------------------------------
    session_start();
    if(isset($_SESSION['logged_in'])){
        if($_SESSION['logged_in'] == true){
            redirectSuc();
        }
    }
    //------------------------------------------------------

    function redirectSuc(){
        header("Location: https://cs3380-pg3f4.cloudapp.net/lab8/success.php");
        die();
    }

    function printForm(){ ?>
        <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Username" required="true">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required="true">
                        </div>
                        <button type="button" class="btn btn-default" onClick="location.href='register.php'">Register!</button>
                        <button type="submit" name="login" class="btn btn-success pull-right">Submit</button>
                    </form>
    <?php }
?>

<!DOCTYPE html>
<head>
    <!--Bootstrap -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap/css/bootstrap-theme.min.css">
    <script src="bootstrap/js/bootstrap.min.js"></script>

    <link rel="stylesheet" href="style.css">

    <title>Lab 8: Philip Gilbreth</title>
</head>
<body>
    <div class="container container-table">
        <div class="row vertical-center-row">
            <div class="col-md-4 col-md-offset-4">
                <div class="text-center">
                    <h3>Log In</h3>
                </div>
                <?php
                    if(isset($_POST["login"])) {
                        include("../secure/db.php");
                        $link = mysqli_connect(HOST, USERNAME, PASSWORD, DBNAME) or die ("Connection Error " . mysqli_error($link));
                        $sql = "SELECT username, salt, hashed_password FROM user WHERE username = ?";
                        if ($stmt = mysqli_prepare($link, $sql)){
                            mysqli_stmt_bind_param($stmt, "s", $_POST['username']) or die("Failed to bind param.");
                            mysqli_stmt_execute($stmt) or die("Could not execute statement");
                            $result = $stmt->get_result();
                            $row = mysqli_fetch_assoc($result);

                            if(password_verify(($row['salt'].$_POST['password']), $row['hashed_password'])){
                                if(!isset($_SESSION['user'])){
                                    $_SESSION['username'] = $_POST['username'];
                                }
                                if(!isset($_SESSION['logged_in'])){
                                    $_SESSION['logged_in'] = true;
                                }
                                redirectSuc();

                            } else {
                                echo "<p style='color:red'>Wrong Username/Password</font>";
                                printForm();
                            }
                        } else {die("Could not prepare statement.");}
                    } else {
                        printForm();
                    } ?>
            </div>
        </div>
    </div>
</body>