<?php
session_start();
if (!isset($_SESSION['username']) ||
    intval(substr($_SESSION['username'], -1)) != 0
) {
    echo '<script>
            url="index.php";
            window.location.href=url;
            </script>';
    $isDiscount = 'active';
}
?>
<html>
<head>
    <title>DISCOUNTS GIVEN EMAIL</title>
    <!--  I USE BOOTSTRAP BECAUSE IT MAKES FORMATTING/LIFE EASIER -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
    <!-- Optional theme -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">
</head>
<body>
<div class="container">
    <div class="page-header">
        <h1>DISCOUNTS GIVEN</h1>
    </div>

    <div class="row">
        <?php
        $table_template = '
                	<table class="table table-hover">
                        <tbody>
                            <tr>
                                <th>Staff Name</th>
                                <th>Item</th>
                                <th>Department</th>
                                <th>Category</th>
                                <th>Discount Given %%</th>
                                <th>Discount given $</th>
                                <th>Day</th>
                                <th>Date</th>
                                <th>Time</th>
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
                        <td>%s</td>
		            </tr>';
        $trs = '';
        $weekshow = array("Sun.", "Mon.", "Tue.", "Wed.", "Thurs.", "Fri.", "Sat.");
        echo '<div>';

        include("../secure/db.php");
        $link = mysqli_connect(HOST, USERNAME, PASSWORD, DBNAME) or die ("Connection Error " . mysqli_error($link));
        $sql = "SELECT * FROM employee";

        if ($stmt = mysqli_prepare($link, $sql)) {
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);

                while ($employee = mysqli_fetch_row($result)) {
                    $sql = "SELECT transaction_id, time, date(time), time(time) FROM transaction WHERE employee_id=?";

                    if ($stmt = mysqli_prepare($link, $sql)) {
                        mysqli_stmt_bind_param($stmt, "d", $employee[0]) or die("bind param");
                        if (mysqli_stmt_execute($stmt)) {
                            $res = mysqli_stmt_get_result($stmt);

                            while ($transaction = mysqli_fetch_row($res)) {
                                $sql = "SELECT food_name, department_name, category_name, price, discount FROM transaction_detail NATURAL JOIN food NATURAL JOIN category NATURAL JOIN department WHERE transaction_id=? AND discount != 0";
                                if ($stmt = mysqli_prepare($link, $sql)) {
                                    mysqli_stmt_bind_param($stmt, "d", $transaction[0]) or die("bind param");
                                    if (mysqli_stmt_execute($stmt)) {
                                        $fres = mysqli_stmt_get_result($stmt);

                                        while ($food = mysqli_fetch_row($fres)) {
                                            $staff = $employee[1];
                                            $item = $food[0];
                                            $department = $food[1];
                                            $category = $food[2];
                                            $discount_percentage = round($food[4] / $food[3], 3) * 100;
                                            $discount_given = $food[3];
                                            $day = $weekshow[date('w', $transaction[1])];
                                            $date = $transaction[2];
                                            $time = $transaction[3];
                                            $trs .= sprintf($tr_template, $staff, $item, $department, $category, $discount_percentage . '%', $discount_given, $day, $date, $time);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        echo sprintf($table_template, $trs);
        echo '</div>';
        ?>
    </div>
</div>
</body>
</html>