<?php
    session_start();
    if (!isset($_SESSION['username']) ||
            intval(substr($_SESSION['username'], -1))!=0) {
        echo '<script>
            url="index.php";
            window.location.href=url;
            </script>';
    }
?>
<html>
	<head>
		<title>TIPS PER EMPLOYEE</title>
		<!--  I USE BOOTSTRAP BECAUSE IT MAKES FORMATTING/LIFE EASIER -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css"><!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css"><!-- Optional theme -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">
	</head>
	<body>
		<?php
			$isTips = 'active';
			include('nav.php.inc');
		?>
		<div class="container">
			<div class="page-header">
			  <h1>TIPS PER EMPLOYEE</h1>
			</div>

			<div class="row">
			<?php
                $table_template = '
                	<table class="table table-hover">
                        <tbody>
                            <tr>
                                <th>Employee</th>
                                <th>Tips</th>
                                <th>Sales</th>
                                <th>Percentage</th>
                            </tr>
                            %s
                        </tbody>
                   </table>';
                $tr_template='
                	<tr>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                    </tr>';
                $trs = '';

        $link = mysqli_connect(HOST, USERNAME, PASSWORD, DBNAME) or die ("Connection Error " . mysqli_error($link));
				$sql = "SELECT * FROM employee";
				if ($stmt = mysqli_prepare($link, $sql)) {
					if(mysqli_stmt_execute($stmt)) {
						$result = mysqli_stmt_get_result($stmt);

						while ($employee = mysqli_fetch_row($result)) {
							$sql = "SELECT tips, tendered_amount, returns FROM transaction WHERE employee_id=?";

							if($stmt = mysqli_prepare($link, $sql)) {
								mysqli_stmt_bind_param($stmt, "d", $employee[0]) or die("bind param");

								if(mysqli_stmt_execute($stmt)) {
									$res = mysqli_stmt_get_result($stmt);
									$total_tips = 0;
									$total_sales = 0;

									while ($sales = mysqli_fetch_row($res)) {
										$total_tips += $sales[0];
										$total_sales += $sales[1] - $sales[2];
									}
									$percentage = round($total_tips/$total_sales, 3)*100;

									$trs .= sprintf($tr_template, $employee[1], $total_tips, $total_sales, $percentage.'%');
								}
							}
						}
					}
				}

				echo '
					<div>
						<h3 class="text-center">Tips Report</h3>';

				echo sprintf($table_template, $trs);
				echo '</div>';
			?>
		</div>
	</body>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</html>