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
            if (($row > 1) && ($num == 14)) { //skip first one because first one is just info
                include("../secure/db.php");
                $link = mysqli_connect(HOST, USERNAME, PASSWORD, DBNAME);
                $sql = "INSERT INTO transaction(transactionID,transactionTimestamp,subTotal,totalPrice,totalTax,gratuity,receiptNumber,registerNumber,cashier,customer,totalDiscount) VALUES(?,?,?,?,?,?,?,?,?,?,?)";
                if ($stmt = mysqli_prepare($link, $sql)) {
                    $tid = $data[0];
                    $time = $data[1];
                    $newTime = "2015/" . str_replace("/2015", "", $time);
                    //echo "THE NEWTIME IS " . $newTime;
                    $cashier = $data[3];
                    $customerName = $data[5];
                    $subtotal = $data[7];
                    $discount = $data[9];
                    $tax = $data[10];
                    $total = $data[11];
                    $gratuity = $data[12];
                    $rNum = $data[13];

                    mysqli_stmt_bind_param($stmt, "isddddsissd", $tid, $newTime, $subtotal, $total, $tax, $gratuity, $rNum, $register, $cashier, $customerName, $discount) or die("Bind param");
                    if (mysqli_stmt_execute($stmt)) {
                        echo "Transaction " . $tid . " added successfully! <br/>";
                    } else {
                        echo "Transaction " . $tid . ": could not be added! (Transaction # already exists) <br/>";
                    }
                } else {
                    die("Could not prepare statement!");
                }
            }
        }
        fclose($handle);
    } else {
        echo "Could not open file";
    }
}
?>