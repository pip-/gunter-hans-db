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
    <title>ADVANCED REPORT</title>
    <!--  I USE BOOTSTRAP BECAUSE IT MAKES FORMATTING/LIFE EASIER -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css"><!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css"><!-- Optional theme -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">
</head>
<body>
<?php
$isAdvanced = 'active';
include('nav.php.inc');
?>
<div class="container">
    <div class="page-header">
        <h1>ADVANCED REPORT</h1>
    </div>

    <div class="row">
        <form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
            <div class="col-md-12 text-center">
                <div class='col-md-4'>
                    <div class="form-group">
                        <div class='input-group date' id='datestartpicker'>
                            <span class="input-group-addon">Start Date</span>
                            <input type='text' class="form-control" name="datestartinput"/>
				                <span class="input-group-addon">
				                    <span class="glyphicon glyphicon-calendar"></span>
				                </span>
                        </div>
                    </div>
                </div>
                <div class='col-md-4'>
                    <div class="form-group">
                        <div class='input-group date' id='dateendpicker'>
                            <span class="input-group-addon">End Date</span>
                            <input type='text' class="form-control" name="dateendinput"/>
				                <span class="input-group-addon">
				                    <span class="glyphicon glyphicon-calendar"></span>
				                </span>
                        </div>
                    </div>
                </div>
                <div class='col-md-4'>
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
            </div>
            <div class="col-md-12 text-center">
                <div class='col-md-4'>
                    <div class="form-group">
                        <div class='input-group date' id='timestartpicker'>
                            <span class="input-group-addon">Start Time</span>
                            <input type='text' class="form-control" name="timestartinput"/>
				                <span class="input-group-addon">
				                    <span class="glyphicon glyphicon-calendar"></span>
				                </span>
                        </div>
                    </div>
                </div>
                <div class='col-md-4'>
                    <div class="form-group">
                        <div class='input-group date' id='timeendpicker'>
                            <span class="input-group-addon">End Time</span>
                            <input type='text' class="form-control" name="timeendinput"/>
				                <span class="input-group-addon">
				                    <span class="glyphicon glyphicon-calendar"></span>
				                </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
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
                                <th>Employee Name</th>
                                <th>Sales</th>
                                <th>Returns</th>
                            </tr>
                            %s
                        </tbody>
                   </table>';
    $tr_template='
                	<tr>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                    </tr>';
    $trs = '';

    if(isset($_POST['submit'])) {
        $link = mysqli_connect(HOST, USERNAME, PASSWORD, DBNAME) or die ("Connection Error " . mysqli_error($link));
        $sql = "SELECT employee_name, sum(tendered_amount), sum(returns), employee_id FROM transaction NATURAL JOIN employee WHERE DATEDIFF(STR_TO_DATE(?, '%d/%m/%Y'), date(time))<=0 AND DATEDIFF(STR_TO_DATE(?, '%d/%m/%Y'), date(time))>=0 AND TIMEDIFF(STR_TO_DATE(?, '%H:%i'), time(time))<=0 AND TIMEDIFF(STR_TO_DATE(?, '%H:%i'), time(time))>=0";
        if ($_POST['days'] != 0) {
            $sql .= ' AND DAYOFWEEK(time)=?';
        }
        $sql .= " GROUP BY employee_id";

        if ($stmt = mysqli_prepare($link, $sql)) {
            $datestart = ($_POST['datestartinput'] === '') ? '01/01/1900' : $_POST['datestartinput'];
            $dateend = ($_POST['dateendinput'] === '') ? '31/12/2107' : $_POST['dateendinput'];
            $timestart = ($_POST['timestartinput'] === '') ? '00:00' : $_POST['timestartinput'];
            $timeend = ($_POST['timeendinput'] === '') ? '23:59' : $_POST['timeendinput'];

            if ($_POST['days'] != 0) {
                mysqli_stmt_bind_param($stmt, "ssssd", $datestart, $dateend, $timestart, $timeend, $_POST['days']) or die("bind param");
            } else {
                mysqli_stmt_bind_param($stmt, "ssss", $datestart, $dateend, $timestart, $timeend) or die("bind param");
            }

            echo '
							<div>
								<h3 class="text-center">Advanced Report</h3>';
            if(mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);

                while ($row = mysqli_fetch_row($result)) {
                    $trs .= sprintf($tr_template, $row[0], $row[1], $row[2]);
                }

                echo sprintf($table_template, $trs);
                echo '</div>';
            }
        }
    }
    ?>
    <div class="row">
        <?php
        if (isset($_POST['submitemail'])) {
            $htmls = '<html>
                            <head>
                                <title>ADVANCED REPORT EMAIL</title>
                                <!--  I USE BOOTSTRAP BECAUSE IT MAKES FORMATTING/LIFE EASIER -->
                                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css"><!-- Latest compiled and minified CSS -->
                                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css"><!-- Optional theme -->
                                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">
                            </head>
                            <body>
                                <div class="container">
                                    <div class="page-header">
                                      <h1>DISCOUNTS GIVEN</h1>
                                    </div>
                                    <div class="row">
                                    %s
                                    </div>
                                </div>
                            </body>
                        </html>';
            $table_template = '
                	<table class="table table-hover">
                        <tbody>
                            <tr>
                                <th>Staff Name</th>
                                <th>Total Sales</th>
                                <th>Total Returns</th>
                                <th>Tips Received</th>
                                <th>Tips (%% of sales)</th>
                                <th>Discount given</th>
                                <th>Discounts given (%% of sales)</th>
                                <th>Discounts Compared</th>
                            </tr>
                            %s
                        </tbody>
                   </table>';
            $tr_template = '
                	<tr>
                		<td>%s</td>
                		<td>%s</td>
                		<td>%s</td>
                		<td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
		            </tr>';
            $trs = '';
            $weekshow = array("Sun.", "Mon.", "Tue.", "Wed.", "Thurs.", "Fri.", "Sat.");
            echo '<div>';

            $link = mysqli_connect(HOST, USERNAME, PASSWORD, DBNAME) or die ("Connection Error " . mysqli_error($link));
            $sql = "SELECT discount FROM transaction NATURAL JOIN transaction_detail NATURAL JOIN food";
            $total_discounts = 0;
            if ($stmt = mysqli_prepare($link, $sql)) {
                if(mysqli_stmt_execute($stmt)) {
                    $result = mysqli_stmt_get_result($stmt);

                    while ($row = mysqli_fetch_row($result)) {
                        $total_discounts += $row[0];
                    }

                    $average = $total_discounts/$result->num_rows;
                }
            }

            $sql = "SELECT employee_id, employee_name, sum(tips), sum(tendered_amount), sum(returns), sum(discount) FROM transaction NATURAL JOIN employee NATURAL JOIN transaction_detail NATURAL JOIN food GROUP BY employee_id";

            if ($stmt = mysqli_prepare($link, $sql)) {
                if(mysqli_stmt_execute($stmt)) {
                    $result = mysqli_stmt_get_result($stmt);

                    while ($row = mysqli_fetch_row($result)) {
                        $staff = $row[1];
                        $total_sales = $row[3] - $row[4];
                        $total_returns = $row[4];
                        $total_tips = $row[2];
                        $tips_of_sales = round($total_tips/$total_sales, 3)*100;
                        $total_discount = $row[5];
                        $discount_of_sales = round($total_discount/$total_sales, 3)*100;
                        if ($total_discount > $average) {
                            $status = '<span class="label label-danger">+</span>';
                        } else if ($total_discount < $average) {
                            $status = '<span class="label label-success">-</span>';
                        } else {
                            $status = '<span class="label label-warning">=</span>';
                        }

                        $trs .= sprintf($tr_template, $staff, $total_sales, $total_returns, $total_tips, $tips_of_sales.'%', $total_discount, $discount_of_sales.'%', $status);
                    }
                }
            }
            $contents = sprintf($htmls, sprintf($table_template, $trs));

            $email = $_POST['email'];
            $subject = 'Advanced Report';
            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            if (mail($email, $subject, $contents, $headers)) {
                echo "<script>alert('Mail Send Success!');</script>";
            } else {
                echo "<script>alert('Mail Send Fail!');</script>";
            }
        }
        ?>
        <form action="<?=$_SERVER['PHP_SELF']?>" method="POST" class="form-inline">
            <label for="emailbox">Mailto</label>
            <input type='email' id="emailbox" class="form-control" name="email" placeholder="exmaple@example.com"/>
            <button type="submit" name="submitemail" class="btn btn-default">Advanced Report Email</button>
        </form>
    </div>
</div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript">
    $(function () {
        $('#datestartpicker').datetimepicker({
            format: "DD/MM/YYYY",
            sideBySide: true,
            showTodayButton: true,
            showClear: true
        });
        $('#dateendpicker').datetimepicker({
            useCurrent: false,
            format: "DD/MM/YYYY",
            sideBySide: true,
            showTodayButton: true,
            showClear: true
        });
        $('#timestartpicker').datetimepicker({
            format: "HH:mm",
            sideBySide: true,
            showTodayButton: true,
            showClear: true
        });
        $('#timeendpicker').datetimepicker({
            useCurrent: false,
            format: "HH:mm",
            sideBySide: true,
            showTodayButton: true,
            showClear: true
        });
        $("#timestartpicker").on("dp.change", function (e) {
            $('#timeendpicker').data("DateTimePicker").minDate(e.date);
        });
        $("#timeendpicker").on("dp.change", function (e) {
            $('#timestartpicker').data("DateTimePicker").maxDate(e.date);
        });
        $("#datestartpicker").on("dp.change", function (e) {
            $('#dateendpicker').data("DateTimePicker").minDate(e.date);
        });
        $("#timeendpicker").on("dp.change", function (e) {
            $('#datestartpicker').data("DateTimePicker").maxDate(e.date);
        });
    });
</script>
</html>