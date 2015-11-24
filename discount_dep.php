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
    <title>DEPARTMENT AND CATEGORIES</title>
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
        <h1>DEPARTMENT AND CATEGORIES</h1>
    </div>

    <div class="row">
        <?php
        $table_template = '
                	<table class="table table-hover">
                        <tbody>
                            <tr>
                                <th>Department Name</th>
                                <th>Total Discount</th>
                                <th>Total Price</th>
                                <th>Percentage</th>
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
		            </tr>';
        $trs = '';
        echo '<div>
						<h3 class="text-center">Department Discount</h3>';

        $link = mysqli_connect($host, $user, $password, $db) or die ("Connection Error " . mysqli_error($link));
        $sql = "SELECT department_id, department_name FROM department";

        if ($stmt = mysqli_prepare($link, $sql)) {
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                $total_price = 0;
                $total_discount = 0;

                while ($row = mysqli_fetch_row($result)) {
                    $sql = "SELECT price, discount FROM food WHERE department_id=?";

                    if ($stmt = mysqli_prepare($link, $sql)) {
                        mysqli_stmt_bind_param($stmt, "d", $row[0]) or die("bind param");

                        if (mysqli_stmt_execute($stmt)) {
                            $res = mysqli_stmt_get_result($stmt);

                            while ($r = mysqli_fetch_row($res)) {
                                $total_price += $r[0];
                                $total_discount += $r[1];
                            }
                            $percentage = round($total_discount / $total_price, 3) * 100;

                            $trs .= sprintf($tr_template, $row[1], $total_discount, $total_price, $percentage . '%');
                        }
                    }
                }


                echo sprintf($table_template, $trs);
                echo '</div>';
            }
        }

        $table_template = '
                	<table class="table table-hover">
                        <tbody>
                            <tr>
                                <th>Category Name</th>
                                <th>Total Discount</th>
                                <th>Total Price</th>
                                <th>Percentage</th>
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
		            </tr>';
        $trs = '';
        echo '<div>
						<h3 class="text-center">Category Discount</h3>';

        $link = mysqli_connect($host, $user, $password, $db) or die ("Connection Error " . mysqli_error($link));
        $sql = "SELECT category_id, category_name FROM category";

        if ($stmt = mysqli_prepare($link, $sql)) {
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                $total_price = 0;
                $total_discount = 0;

                while ($row = mysqli_fetch_row($result)) {
                    $sql = "SELECT price, discount FROM food WHERE category_id=?";

                    if ($stmt = mysqli_prepare($link, $sql)) {
                        mysqli_stmt_bind_param($stmt, "d", $row[0]) or die("bind param");

                        if (mysqli_stmt_execute($stmt)) {
                            $res = mysqli_stmt_get_result($stmt);

                            while ($r = mysqli_fetch_row($res)) {
                                $total_price += $r[0];
                                $total_discount += $r[1];
                            }
                            $percentage = round($total_discount / $total_price, 3) * 100;

                            $trs .= sprintf($tr_template, $row[1], $total_discount, $total_price, $percentage . '%');
                        }
                    }
                }


                echo sprintf($table_template, $trs);
                echo '</div>';
            }
        }
        ?>
        <div class="row">
            <a class="btn btn-default" href="discount_given_email.php" role="button" target="_black">Discounts Given
                Email</a>
        </div>
    </div>
</div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</html>