<?php
session_start();
if (!isset($_SESSION['username']) ||
    intval(substr($_SESSION['username'], -1)) != 0
) {
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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
    <!-- Optional theme -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">
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
        <div class="col-md-6">
            <h3 class="text-center">Daily Forecasting</h3>
            <hr/>
            <?php
            $template = '
				<div class="panel col-md-4 text-center">
					<div class="panel-heading"><h4>%s</h4></div>
					<div class="panel-body"><h2>%s</h2></div>
				</div>';
            $daily_forecasting = '';

            $link = mysqli_connect(HOST, USERNAME, PASSWORD, DBNAME) or die ("Connection Error " . mysqli_error($link));
            $sql = "SELECT tendered_amount, returns FROM transaction WHERE DATEDIFF(STR_TO_DATE(?, '%d/%m/%Y'), date(time))=0";

            $week = array(date('Y-m-d', strtotime('-1 week')), date('Y-m-d', strtotime('-2 week')), date('Y-m-d', strtotime('-3 week')));
            $week_sale = array(0, 0, 0);

            for ($i = 0; $i < 3; $i++) {
                if ($stmt = mysqli_prepare($link, $sql)) {
                    mysqli_stmt_bind_param($stmt, "s", $week[$i]) or die("bind param");

                    if (mysqli_stmt_execute($stmt)) {
                        $result = mysqli_stmt_get_result($stmt);
                        while ($row = mysqli_fetch_row($result)) {
                            $week_sale[$i] = $row[0] - $row[1];
                        }
                    }
                }
                $daily_forecasting .= sprintf($template, $week[$i], $week_sale[$i]);
            }
            echo $daily_forecasting;

            $today = $week_sale[2] + ($week_sale[2] - $week_sale[0]) / 2;
            if ($today < 0) {
                $today = 0;
            }

            echo '<div class="text-center"><h4>Daily sale: ' . $today . '</h4></div>';
            ?>
        </div>
        <div class="col-md-6">
            <h3 class="text-center">Monthly Forecasting</h3>
            <hr/>
            <?php
            $template = '
				<div class="panel col-md-3 text-center">
					<div class="panel-heading"><h4>%s</h4></div>
					<div class="panel-body"><h2>%s</h2></div>
				</div>';
            $month_forecasting = '';

            $sql = "SELECT sum(tendered_amount), sum(returns) FROM transaction WHERE DATEDIFF(STR_TO_DATE(?, '%Y-%m-%d'), date(time))<=0 AND DATEDIFF(STR_TO_DATE(?, '%Y-%m-%d'), date(time))>=0";
            $last_year = date('Y-m', strtotime('-1 year'));
            $last_year_start = $last_year . '-1';
            $last_year_end = $last_year . '-31';
            $last_year_sale = 0;

            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "ss", $last_year_start, $last_year_end) or die("bind param");

                if (mysqli_stmt_execute($stmt)) {
                    $result = mysqli_stmt_get_result($stmt);
                    while ($row = mysqli_fetch_row($result)) {
                        $last_year_sale = $row[0] - $row[1];
                    }
                }
            }
            $month_forecasting .= sprintf($template, $last_year, $last_year_sale);

            $sql = "SELECT sum(tendered_amount), sum(returns) FROM transaction WHERE DATEDIFF(STR_TO_DATE(?, '%Y-%m-%d'), date(time))<0 AND DATEDIFF(STR_TO_DATE(?, '%Y-%m-%d'), date(time))>=0";
            $past_month = array(date('Y-m', strtotime('-4 month')), date('Y-m', strtotime('-3 month')), date('Y-m', strtotime('-2 month')), date('Y-m', strtotime('-1 month')));
            $past_month_sale = array(0, 0, 0);

            for ($i = 0; $i < 3; $i++) {
                if ($stmt = mysqli_prepare($link, $sql)) {
                    $past_month_start = $past_month[$i] . '-31';
                    $past_month_end = $past_month[$i + 1] . '-31';
                    mysqli_stmt_bind_param($stmt, "ss", $past_month_start, $past_month_end) or die("bind param");

                    if (mysqli_stmt_execute($stmt)) {
                        $result = mysqli_stmt_get_result($stmt);
                        while ($row = mysqli_fetch_row($result)) {
                            $past_month_sale[$i] = $row[0] - $row[1];
                        }
                    }
                }
                $month_forecasting .= sprintf($template, $past_month[$i + 1], $past_month_sale[$i]);
            }
            echo $month_forecasting;

            $month = ($past_month_sale[2] + ($past_month_sale[2] - $past_month_sale[0]) / 2 + $last_year_sale) / 2;
            if ($month < 0) {
                $month = 0;
            }
            echo '<div class="text-center"><h4>Monthly sale: ' . $month . '</h4></div>';
            ?>
        </div>
    </div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</html>