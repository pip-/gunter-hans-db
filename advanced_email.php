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
    <title>ADVANCED REPORT EMAIL</title>
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
$host = 'us-cdbr-azure-central-a.cloudapp.net';
$user = 'bde136b978606c';
$password = '7a298415';
$db = 'cs3380-yxdh8';
?>
<div class="container">
    <div class="page-header">
        <h1>ADVANCED REPORT EMAIL</h1>
    </div>

    <div class="row">
        <?php
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

        $link = mysqli_connect($host, $user, $password, $db) or die ("Connection Error " . mysqli_error($link));
        $sql = "SELECT discount FROM transaction NATURAL JOIN transaction_detail NATURAL JOIN food";
        $total_discounts = 0;
        if ($stmt = mysqli_prepare($link, $sql)) {
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);

                while ($row = mysqli_fetch_row($result)) {
                    $total_discounts += $row[0];
                }

                $average = $total_discounts / $result->num_rows;
            }
        }

        $sql = "SELECT employee_id, employee_name, sum(tips), sum(tendered_amount), sum(returns), sum(discount) FROM transaction NATURAL JOIN employee NATURAL JOIN transaction_detail NATURAL JOIN food GROUP BY employee_id";

        if ($stmt = mysqli_prepare($link, $sql)) {
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);

                while ($row = mysqli_fetch_row($result)) {
                    $staff = $row[1];
                    $total_sales = $row[3] - $row[4];
                    $total_returns = $row[4];
                    $total_tips = $row[2];
                    $tips_of_sales = round($total_tips / $total_sales, 3) * 100;
                    $total_discount = $row[5];
                    $discount_of_sales = round($total_discount / $total_sales, 3) * 100;
                    if ($total_discount > $average) {
                        $status = '<span class="label label-danger">+</span>';
                    } else if ($total_discount < $average) {
                        $status = '<span class="label label-success">-</span>';
                    } else {
                        $status = '<span class="label label-warning">=</span>';
                    }

                    $trs .= sprintf($tr_template, $staff, $total_sales, $total_returns, $total_tips, $tips_of_sales . '%', $total_discount, $discount_of_sales . '%', $status);
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