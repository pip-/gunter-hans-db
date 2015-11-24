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
    <title>TIPS SEARCH</title>
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
$isTips = 'active';
include('nav.php.inc');
?>
<div class="container">
    <div class="page-header">
        <h1>TIPS SEARCH</h1>
    </div>

    <div class="row">
        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
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
                                <th>Transaction ID</th>
                                <th>Time</th>
                                <th>Tips</th>
                            </tr>
                            %s
                        </tbody>
                   </table>';
    $tr_template = '
                	<tr>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                    </tr>';
    $trs = '';

    if (isset($_POST['submit'])) {
        $link = mysqli_connect($host, $user, $password, $db) or die ("Connection Error " . mysqli_error($link));
        $sql = "SELECT transaction_id, time, tips FROM transaction WHERE DATEDIFF(STR_TO_DATE(?, '%d/%m/%Y'), date(time))<=0 AND DATEDIFF(STR_TO_DATE(?, '%d/%m/%Y'), date(time))>=0 AND TIMEDIFF(STR_TO_DATE(?, '%H:%i'), time(time))<=0 AND TIMEDIFF(STR_TO_DATE(?, '%H:%i'), time(time))>=0";
        if ($_POST['days'] != 0) {
            $sql .= ' AND DAYOFWEEK(time)=?';
        }

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
								<h3 class="text-center">Tips Report</h3>';
            if (mysqli_stmt_execute($stmt)) {
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