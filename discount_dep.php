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
    <title>DEPARTMENT AND CATEGORIES</title>
    <!--  I USE BOOTSTRAP BECAUSE IT MAKES FORMATTING/LIFE EASIER -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css"><!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css"><!-- Optional theme -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">
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
            if(mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                $total_price = 0;
                $total_discount = 0;

                while ($row = mysqli_fetch_row($result)) {
                    $sql = "SELECT price, discount FROM food WHERE department_id=?";

                    if ($stmt = mysqli_prepare($link, $sql)) {
                        mysqli_stmt_bind_param($stmt, "d", $row[0]) or die("bind param");

                        if(mysqli_stmt_execute($stmt)) {
                            $res = mysqli_stmt_get_result($stmt);

                            while ($r = mysqli_fetch_row($res)) {
                                $total_price += $r[0];
                                $total_discount += $r[1];
                            }
                            $percentage = round($total_discount/$total_price, 3)*100;

                            $trs .= sprintf($tr_template, $row[1], $total_discount, $total_price, $percentage.'%');
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
            if(mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                $total_price = 0;
                $total_discount = 0;

                while ($row = mysqli_fetch_row($result)) {
                    $sql = "SELECT price, discount FROM food WHERE category_id=?";

                    if ($stmt = mysqli_prepare($link, $sql)) {
                        mysqli_stmt_bind_param($stmt, "d", $row[0]) or die("bind param");

                        if(mysqli_stmt_execute($stmt)) {
                            $res = mysqli_stmt_get_result($stmt);

                            while ($r = mysqli_fetch_row($res)) {
                                $total_price += $r[0];
                                $total_discount += $r[1];
                            }
                            $percentage = round($total_discount/$total_price, 3)*100;

                            $trs .= sprintf($tr_template, $row[1], $total_discount, $total_price, $percentage.'%');
                        }
                    }
                }


                echo sprintf($table_template, $trs);
                echo '</div>';
            }
        }
        ?>
        <div class="row">
            <?php
            if (isset($_POST['submitemail'])) {
                $htmls = '<html>
                            <head>
                                <title>DISCOUNTS GIVEN EMAIL</title>
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
                $table_template = '<table class="table table-hover">
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
                $tr_template = '<tr>
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

                $link = mysqli_connect($host, $user, $password, $db) or die ("Connection Error " . mysqli_error($link));
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
                $contents = sprintf($htmls, sprintf($table_template, $trs));

                $email = $_POST['email'];
                $subject = 'Discount Given';
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
                <button type="submit" name="submitemail" class="btn btn-default">Discounts Given Email</button>
            </form>
        </div>
    </div>
</div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</html>