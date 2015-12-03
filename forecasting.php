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
		<title>FORECASTING</title>
		<!--  I USE BOOTSTRAP BECAUSE IT MAKES FORMATTING/LIFE EASIER -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css"><!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css"><!-- Optional theme -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
	</head>
	<body>
		<?php
			$isForcasting = 'active';
			include('nav.php.inc');
		?>
		<div class="container">
			<div class="page-header">
			  <h1>FORECASTING</h1>
			</div>

			<div class="row">
				<div class="col-md-12">
				<h3 class="text-center">Daily Forecasting</h3>
				<hr/>
             <?php
				$template = '
				<div class="panel col-md-3 text-center">
					<div class="panel-heading"><h4>%s(%s)</h4></div>
					<div class="panel-body"><h2>%s</h2></div>
				</div>';
				$daily_forecasting  = '';

				$link = mysqli_connect(HOST, USER, PASSWORD, DBNAME) or die ("Connection Error " . mysqli_error($link));
				$sql = "SELECT sum(tendered_amount), sum(returns), date_format(time, '%Y-%m-%d') FROM transaction WHERE weekday(time)=weekday(?) GROUP BY date_format(time, '%Y-%m-%d')";

				$week = array(date('Y-m-d', strtotime('+1 day')), date('Y-m-d', strtotime('+2 day')), date('Y-m-d', strtotime('+3 day')), date('Y-m-d', strtotime('+4 day')), date('Y-m-d', strtotime('+5 day')), date('Y-m-d', strtotime('+6 day')), date('Y-m-d', strtotime('+7 day')));
				$week_sale = array(0, 0, 0, 0, 0, 0, 0);
                $week_sale_count = array(0, 0, 0, 0, 0, 0, 0);

				for ($i=0; $i<7; $i++) {
					if ($stmt = mysqli_prepare($link, $sql)) {
						mysqli_stmt_bind_param($stmt, "s", $week[$i]) or die("bind param");
						
						if(mysqli_stmt_execute($stmt)) {
							$result = mysqli_stmt_get_result($stmt);
							while ($row = mysqli_fetch_row($result)) {
							    $week_sale[$i] += $row[0]-$row[1];
                                $week_sale_count[$i]++;
							}
						}
					}
                    if ($week_sale_count[$i] != 0) {
                        $week_sale[$i] = round($week_sale[$i]/$week_sale_count[$i], 2);
                    }
					$daily_forecasting .= sprintf($template, $week[$i], date('D', strtotime($week[$i])) . '.', $week_sale[$i]);
				}
				echo $daily_forecasting;
			    echo '<div class="text-center col-md-12"><canvas id="dailychart" height="300" width="800"></canvas></div>';
			?>
			     </div>
			     <div class="col-md-12">
				<h3 class="text-center">Monthly Forecasting</h3>
				<hr/>
				<?php
				$template = '
				<div class="panel col-md-3 text-center">
					<div class="panel-heading"><h4>%s</h4></div>
					<div class="panel-body"><h2>%s</h2></div>
				</div>';
				$month_forecasting  = '';

				$sql = "SELECT sum(tendered_amount), sum(returns), DATE_FORMAT(time, '%Y') FROM transaction WHERE MONTH(time)=MONTH(?) GROUP BY DATE_FORMAT(time, '%Y')";
				$past_month = array(date('Y-m-d', strtotime('+1 month')), date('Y-m-d', strtotime('+2 month')), date('Y-m-d', strtotime('+3 month')), date('Y-m-d', strtotime('+4 month')), date('Y-m-d', strtotime('+5 month')), date('Y-m-d', strtotime('+6 month')), date('Y-m-d', strtotime('+7 month')), date('Y-m-d', strtotime('+8 month')), date('Y-m-d', strtotime('+9 month')), date('Y-m-d', strtotime('+10 month')), date('Y-m-d', strtotime('+11 month')), date('Y-m-d', strtotime('+12 month')));
				$past_month_sale = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
                $past_month_sale_count = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

				for ($i=0; $i<12; $i++) {
					if ($stmt = mysqli_prepare($link, $sql)) {
						mysqli_stmt_bind_param($stmt, "s", $past_month[$i]) or die("bind param");
						
						if(mysqli_stmt_execute($stmt)) {
							$result = mysqli_stmt_get_result($stmt);
							while ($row = mysqli_fetch_row($result)) {
                                echo $row[0];
							    $past_month_sale[$i] += $row[0]-$row[1];
                                $past_month_sale_count[$i]++;
							}
						}
					}
                    if ($past_month_sale_count[$i] != 0) {
                        $past_month_sale[$i] = round($past_month_sale[$i]/$past_month_sale_count[$i], 2);
                    }
                    $past_month[$i] = date('Y-m', strtotime($past_month[$i]));
					$month_forecasting .= sprintf($template, $past_month[$i], $past_month_sale[$i]);
				}
				echo $month_forecasting;
                echo '<div class="text-center col-md-12"><canvas id="monthchart" height="300" width="800"></canvas></div>';
				?>
			</div>
		</div>
	</body>
    <script type="text/javascript">
    <?php
    $dailydata_template = 'var dailydata = {
        labels: ["%s", "%s", "%s", "%s", "%s", "%s", "%s"],
        datasets: [
            {
                fillColor: "rgba(220,220,220,0.2)",
                strokeColor: "rgba(220,220,220,1)",
                pointColor: "rgba(220,220,220,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(220,220,220,1)",
                data: [%s, %s, %s, %s, %s, %s, %s]
            }
        ]
    };';
    $dailydata = sprintf($dailydata_template, $week[0], $week[1], $week[2], $week[3], $week[4], $week[5], $week[6],
        $week_sale[0], $week_sale[1], $week_sale[2], $week_sale[3], $week_sale[4], $week_sale[5], $week_sale[6]);
    echo $dailydata;
    echo 'var dailyctx = document.getElementById("dailychart").getContext("2d");
    var dailyChart = new Chart(dailyctx).Line(dailydata);';

    $monthdata_template = 'var monthdata = {
        labels: ["%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s"],
        datasets: [
            {
                fillColor: "rgba(220,220,220,0.2)",
                strokeColor: "rgba(220,220,220,1)",
                pointColor: "rgba(220,220,220,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(220,220,220,1)",
                data: [%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s]
            }
        ]
    };';
    $monthdata = sprintf($monthdata_template, $past_month[0], $past_month[1], $past_month[2], $past_month[3], $past_month[4], $past_month[5], $past_month[6], $past_month[7], $past_month[8], $past_month[9], $past_month[10], $past_month[11], $past_month_sale[0], $past_month_sale[1], $past_month_sale[2], $past_month_sale[3], $past_month_sale[4], $past_month_sale[5], $past_month_sale[6], $past_month_sale[7], $past_month_sale[8], $past_month_sale[9], $past_month_sale[10], $past_month_sale[11]);
    echo $monthdata;
    echo 'var monthctx = document.getElementById("monthchart").getContext("2d");
    var monthChart = new Chart(monthctx).Line(monthdata);';
    ?>
    </script>
</html>