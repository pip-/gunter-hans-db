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
    <title>DISCOUNT VS SALES</title>
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
$isDiscount = 'active';
include('nav.php.inc');
?>
<div class="container">
    <div class="page-header">
        <h1>DISCOUNT VS SALES</h1>
    </div>

    <div class="row">
        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
            <div class="col-md-12 text-center">
                <div class='col-md-5'>
                    <div class="form-group">
                        <div class='input-group date' id='datestartpicker'>
                            <span class="input-group-addon">Date</span>
                            <input type='text' class="form-control" name="datestartinput"/>
				                <span class="input-group-addon">
				                    <span class="glyphicon glyphicon-calendar"></span>
				                </span>
                        </div>
                    </div>
                </div>
                <div class='col-md-5'>
                    <div class="form-group">
                        <select class="form-control" name="days">
                            <option value=0>All</option>
                            <option value=1>Sun.</option>
                            <option value=2>Mon.</option>
                            <option value=3>Tue.</option>
                            <option value=4>Wed.</option>
                            <option value=5>Thurs.</option>
                            <option value=6>Fri.</option>
                            <option value=7>Sat.</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="submit" class="btn btn-default">Search</button>
                </div>
            </div>
        </form>
    </div>
    <?php
    $table_template = '
                	<table class="table table-hover">
                        <tbody>
                            <tr>
                                <th>Time</th>
                                <th>Total Discount</th>
                                <th>Total Price</th>
                                <th>Percentage</th>
                            </tr>
                            <tr>
		                        <td>%s</td>
		                        <td>%s</td>
		                        <td>%s</td>
		                        <td>%s</td>
		                    </tr>
                        </tbody>
                   </table>';

    $weekshow = array("All", "Sun.", "Mon.", "Tue.", "Wed.", "Thurs.", "Fri.", "Sat.");
    if (isset($_POST['submit'])) {
        $link = mysqli_connect($host, $user, $password, $db) or die ("Connection Error " . mysqli_error($link));
        if ($_POST['days'] != 0 && $_POST['datestartinput'] !== '') {
            $sql = "SELECT transaction_id, time FROM transaction WHERE DATEDIFF(STR_TO_DATE(?, '%d/%m/%Y'), date(time))=0 AND DAYOFWEEK(time)=?";
        } else if ($_POST['days'] != 0) {
            $sql = "SELECT transaction_id, time FROM transaction WHERE DAYOFWEEK(time)=?";
        } else if ($_POST['datestartinput'] !== '') {
            $sql = "SELECT transaction_id, time FROM transaction WHERE DATEDIFF(STR_TO_DATE(?, '%d/%m/%Y'), date(time))=0";
        } else {
            $sql = "SELECT transaction_id, time FROM transaction";
        }

        if ($stmt = mysqli_prepare($link, $sql)) {
            if ($_POST['days'] != 0 && $_POST['datestartinput'] !== '') {
                mysqli_stmt_bind_param($stmt, "sd", $_POST['datestartinput'], $_POST['days']) or die("bind param");
                $timeshow = $_POST['datestartinput'] . ' & ' . $weekshow[$_POST['days']];
            } else if ($_POST['days'] != 0) {
                mysqli_stmt_bind_param($stmt, "d", $_POST['days']) or die("bind param");
                $timeshow = $weekshow[$_POST['days']];
            } else if ($_POST['datestartinput'] !== '') {
                mysqli_stmt_bind_param($stmt, "s", $_POST['datestartinput']) or die("bind param");
                $timeshow = $_POST['datestartinput'];
            } else {
                $timeshow = $weekshow[$_POST['days']];
            }

            echo '
							<div>
								<h3 class="text-center">Discount vs Sales Report</h3>';
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                $discounts = 0;
                $total_count = 0;

                while ($row = mysqli_fetch_row($result)) {
                    $sql = "SELECT quantity, price, discount FROM transaction_detail NATURAL JOIN food WHERE transaction_id=?";

                    if ($stmt = mysqli_prepare($link, $sql)) {
                        mysqli_stmt_bind_param($stmt, "s", $row[0]) or die("bind param");

                        if (mysqli_stmt_execute($stmt)) {
                            $res = mysqli_stmt_get_result($stmt);

                            while ($r = mysqli_fetch_row($res)) {
                                $total_count += ($r[1] - $r[2]) * $r[0];
                                $discounts += $r[2] * $r[0];
                            }
                        }
                    }
                }
                $percentage = round($discounts / $total_count, 3) * 100;
                echo sprintf($table_template, $timeshow, $discounts, $total_count, $percentage . '%');

                echo '</div>';
            }
        }
    }
    ?>
</div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
<script
    src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript">
    $(function () {
        $('#datestartpicker').datetimepicker({
            format: "DD/MM/YYYY",
            sideBySide: true,
            showTodayButton: true,
            showClear: true
        });
    });
</script>
</html>