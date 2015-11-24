<?php
session_start();
?>
<html>
<head>
	<title>Gunter Hans</title>
	<!--  I USE BOOTSTRAP BECAUSE IT MAKES FORMATTING/LIFE EASIER -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
	<!-- Optional theme -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	<!-- Latest compiled and minified JavaScript -->
</head>
<body>
<?php
include('nav.php.inc');
?>
<div class="container">
	<div class="row">
		<div class="col-md-4 col-sm-4 col-xs-3"></div>
		<div class="col-md-4 col-sm-4 col-xs-6">
			<?php
			if (isset($_SESSION['username'])) {
				$sess = $_SESSION['username'];
				$type = intval(substr($sess, -1));
				$name = substr($sess, 0, -1);
				if ($type == 0) {
					echo '<h2>Welcome Admin! You can use the full system!</h2>';
				} else if ($type == 1) {
					echo '<h2>Welcome ' . $name . '! But you cannot use the system!</h2>';
				}
			} else {
				echo '<h2>Login</h2>';
			}
			?>
			<form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
				<?php
				if (isset($_SESSION['username'])) {
					echo '<input class=" btn btn-info" type="submit" name="logout" value="Logout"/>';
				} else {
					echo '<div class="row form-group">
								<input class="form-control" type="text" name="username" placeholder="username">
						</div>
						<div class="row form-group">
								<input class="form-control" type="password" name="password" placeholder="password">
						</div>
						<div class="row form-group">
								<input class=" btn btn-info" type="submit" name="login" value="Login"/>
								<input class=" btn btn-warning" type="button" value="Register" onclick="window.location.href=\'register.php\'" />
						</div>';
				}
				?>
			</form>
		</div>
	</div>
	<?php
	//              $host = 'us-cdbr-azure-central-a.cloudapp.net';
	//              $user = 'bde136b978606c';
	//              $password = '7a298415';
	//              $db = 'cs3380-yxdh8';

	if (isset($_POST['login'])) {

		$link = mysqli_connect($host, $user, $password, $db) or die ("Connection Error " . mysqli_error($link));
		$sql = "SELECT salt, hashed_password, type FROM user WHERE username=?";
		if ($stmt = mysqli_prepare($link, $sql)) {
			$user = $_POST['username'];
			mysqli_stmt_bind_param($stmt, "s", $user) or die("bind param");
			if (mysqli_stmt_execute($stmt)) {
				$result = mysqli_stmt_get_result($stmt);
				$row = mysqli_fetch_row($result);

				$salt = $row[0];
				$hpass = $row[1];
				$type = $row[2];
				if (password_verify($salt . $_POST['password'], $hpass)) {
					$_SESSION['username'] = $user . $type;
					printf('
									<script>
									url="index.php";
									window.location.href=url;
			                        </script>');
				} else {
					echo "<h4>Error password</h4>";
				}
			} else {
				echo "<h4>Failed</h4>";
			}
		} else {
			die("prepare failed");
		}
	} else if (isset($_POST['logout']) || isset($_GET['logout'])) {
		unset($_SESSION['username']);
		printf('
						<script>
						url="index.php";
						window.location.href=url;
                        </script>');
	}
	?>
</div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</html>