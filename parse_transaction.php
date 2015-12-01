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

$isUpload = 'active';
include('nav.php.inc');
?>
<html>
<head>
    <title>Basic Transaction Upload</title>
    <!--  I USE BOOTSTRAP BECAUSE IT MAKES FORMATTING/LIFE EASIER -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
    <!-- Optional theme -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css">
</head>
<div class="container">
    <div class="row">
        <div class="col-md-4 col-sm-4 col-xs-3"></div>
        <div class="col-md-4 col-sm-4 col-xs-6">
            <form action="parse_transaction.php" method="post" enctype="multipart/form-data">
                Select file to upload (Must be a basic transaction CSV):
                <input type="file" name="fileToUpload" id="fileToUpload">
                <input type="submit" value="Upload File" name="submit">
            </form>
            <?php
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
            $uploadOk = 1;
            $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
// Check if image file is a actual image or fake image
            if (isset($_POST["submit"])) {
                if (($handle = fopen($_FILES["fileToUpload"]["tmp_name"], "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $num = count($data);
                        $row++;
                        if ($row > 1) { //skip first one because first one is just info
                            if ($num == 14) {
                                include("../secure/db.php");
                                $link = mysqli_connect(HOST, USERNAME, PASSWORD, DBNAME);
                                $sql = "INSERT INTO employee(employee_name) VALUES(?)";
                                if ($stmt = mysqli_prepare($link, $sql)) {
                                    $tid = $data[0];
                                    $time = $data[1];
                                    $cashier = $data[3];
                                    $customerName = $data[5];
                                    $subtotal = $data[7];
                                    $discount = $data[9];
                                    $tax = $data[10];
                                    $total = $data[11];
                                    $gratuity = $data[12];
                                    $rNum = $data[13];
                                    $operation_type_id = 1;
                                    $newTime = "2015/" . str_replace("/2015", "", $time);
                                    mysqli_stmt_bind_param($stmt, "s", $cashier) or die ("Can't bind param employee on row " . $row . "\n");
                                    mysqli_stmt_execute($stmt);
                                    $sql = "INSERT INTO transaction(transaction_id,time,employee_id,operation_type_id,tips,tendered_amount) VALUES(?,?,?,?,?,?) ON DUPLICATE KEY UPDATE transaction_id = ?";
                                    if ($stmt = mysqli_prepare($link, $sql)) {
                                        $sql2 = "SELECT employee_id FROM employee WHERE employee_name = ?";
                                        if ($stmt2 = mysqli_prepare($link, $sql2)) {
                                            mysqli_stmt_bind_param($stmt2, "s", $cashier) or die ("Can't bind employee search on row " . $row . "\n");
                                            mysqli_stmt_execute($stmt2);
                                            if ($res = $stmt2->get_result()) {
                                                if ($res->num_rows == 1) {
                                                    $arr = $res->fetch_assoc();
                                                    $cashier_id = $arr[employee_id];

                                                    mysqli_stmt_bind_param($stmt, "isiiddi", $tid, $newTime, $cashier_id, $operation_type_id, $gratuity, $total, $tid) or die("Bind param");
                                                    if (mysqli_stmt_execute($stmt)) {
                                                        echo "Transaction " . $tid . " added successfully! <br/>";
                                                    } else {
                                                        echo "Transaction " . $tid . ": could not be added! (Transaction # already exists) OR ERROR: " . $stmt->error . " <br/>";
                                                    }
                                                } else {
                                                    die("Could not prepare statement!");
                                                }
                                            } else {
                                                echo "Wrong file uploaded/Data not formatted correctly";
                                            }
                                        }
                                    } else {
                                        echo "could not prepare statement INSERT INTO...Error: " . $stmt->error . "...";
                                    }
                                }
                            }
                        }
                }
            }
                    fclose($handle);
                } else {
                    echo "Could not open file";
        }
?>
        </div>
    </div>
</div>

