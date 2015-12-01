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
            <form action="parse_sales.php" method="post" enctype="multipart/form-data">
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
                            if ($num == 17) {
                                include("../secure/db.php");
                                $link = mysqli_connect(HOST, USERNAME, PASSWORD, DBNAME);

                                $sid = $data[0];
                                $time = $data[1];
                                $newTime = "2015/" . str_replace("/2015", "", $time);
                                //echo "THE NEWTIME IS " . $newTime;
                                $cid = $data[2];
                                $reg = $data[3];
                                $subtotal = $data[4];
                                $discount = $data[5];
                                $tax = $data[7];
                                $total = $data[8];
                                $tendered = $data[9];
                                $change = $data[10];
                                $pType = $data[11];
                                $cType = $data[12];
                                $custName = $data[13];
                                $cardDigits = $data[14];
                                $rNum = $data[15];

                                $sql = "INSERT INTO transaction(transaction_id,transactionDatetime,employee_name,operation_type_id,subtotal,tips,tax,tendered_amount) VALUES(?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE transaction_id = ?";
                                if ($stmt = mysqli_prepare($link, $sql)) {

                                    mysqli_stmt_bind_param($stmt, "issiddddi", $tid, $newTime, $cashier, $operation_type_id, $subtotal, $gratuity, $tax, $total, $tid) or die("Bind param");
                                    if (mysqli_stmt_execute($stmt)) {
                                        echo "Transaction " . $tid . " added successfully! <br/>";
                                    } else {
                                        echo "Transaction " . $tid . ": could not be added! (Transaction # already exists) <br/>";
                                    }
                                } else {
                                    die("Could not prepare statement!");
                                }
                            } else {
                                echo "Wrong file uploaded/Data not formatted correctly";
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

