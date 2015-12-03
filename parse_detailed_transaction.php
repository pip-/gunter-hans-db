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
            <form action="parse_detailed_transaction.php" method="post" enctype="multipart/form-data">
                Select file to upload (Must be a detailed transaction CSV):
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
                        if ($row > 1) {
                            if ($num == 19) { //skip first one because first one is just info
                                include("../secure/db.php");
                                $link = mysqli_connect(HOST, USERNAME, PASSWORD, DBNAME);

                                $tid = $data[0];
                                $ttype = $data[1];
                                $storeCode = $data[2];
                                $itemDescription = $data[3];
                                $category = $data[4];
                                $department = $data[5];
                                $supplier = $data[6];
                                $cost = $data[8];
                                $price = $data[9];
                                $quantity = $data[10];
                                $subtotal = $data[12];
                                $tax = $data[13];
                                $discount = $data[14];
                                $total = $data[15];
                                $cashier = $data[16];
                                $time = $data[17];
                                $operation_type = 1;
                                $newTime = "2015/" . str_replace("/2015", "", $time);

                                $sql = "INSERT INTO department(department_name) VALUES(?) ON DUPLICATE KEY UPDATE department_name = ?";
                                if ($stmt = mysqli_prepare($link, $sql)) {
                                    mysqli_stmt_bind_param($stmt, "ss", $department, $department) or die("Bind param for department on row: " . $row);
                                    if (mysqli_stmt_execute($stmt)) {
                                        $sql = "INSERT INTO category(category_name) VALUES(?) ON DUPLICATE KEY UPDATE category_name = ?";
                                        if ($stmt = mysqli_prepare($link, $sql)) {
                                            mysqli_stmt_bind_param($stmt, "ss", $category, $category) or die("Bind param for category on row: " . $row);
                                            if (mysqli_stmt_execute($stmt)) {
                                                $sql = "INSERT INTO employee(employee_name) VALUES(?) ON DUPLICATE KEY UPDATE employee_name = ?";
                                                if ($stmt = mysqli_prepare($link, $sql)) {
                                                    mysqli_stmt_bind_param($stmt, "ss", $cashier, $cashier) or die("Bind param for employee on row: " . $row);
                                                    if (mysqli_stmt_execute($stmt)) {
                                                        $sql2 = "SELECT category_id FROM category WHERE category_name = ?";
                                                        if ($stmt2 = mysqli_prepare($link, $sql2)) {
                                                            mysqli_stmt_bind_param($stmt2, "s", $category) or die ("Can't bind category search on row " . $row . "\n");
                                                            mysqli_stmt_execute($stmt2);
                                                            if ($res = $stmt2->get_result()) {
                                                                if ($res->num_rows == 1) {
                                                                    $arr = $res->fetch_assoc();
                                                                    $category_id = $arr[category_id];
                                                                    $sql2 = "SELECT department_id FROM department WHERE department_name = ?";
                                                                    if ($stmt2 = mysqli_prepare($link, $sql2)) {
                                                                        mysqli_stmt_bind_param($stmt2, "s", $department) or die ("Can't bind department search on row " . $row . "\n");
                                                                        mysqli_stmt_execute($stmt2);
                                                                        if ($res = $stmt2->get_result()) {
                                                                            if ($res->num_rows == 1) {
                                                                                $arr = $res->fetch_assoc();
                                                                                $department_id = $arr[department_id];
                                                                                $sql = "INSERT INTO food(category_id,department_id,food_name,price,discount) VALUES(?,?,?,?,?)";
                                                                                if ($stmt = mysqli_prepare($link, $sql)) {
                                                                                    mysqli_stmt_bind_param($stmt, "iisdd", $category_id, $department_id, $itemDescription, $price, $discount) or die("Bind param for food on row: " . $row);
                                                                                    if (mysqli_stmt_execute($stmt)) {
                                                                                        $sql2 = "SELECT employee_id FROM employee WHERE employee_name = ?";
                                                                                        if ($stmt2 = mysqli_prepare($link, $sql2)) {
                                                                                            mysqli_stmt_bind_param($stmt2, "s", $cashier) or die ("Can't bind employee search on row " . $row . "\n");
                                                                                            mysqli_stmt_execute($stmt2);
                                                                                            if ($res = $stmt2->get_result()) {
                                                                                                if ($res->num_rows == 1) {
                                                                                                    $arr = $res->fetch_assoc();
                                                                                                    $cashier_id = $arr[employee_id];
                                                                                                    $sql = "INSERT INTO transaction(transaction_id,time,employee_id,operation_type_id,tendered_amount) VALUES(?,?,?,?,?) ON DUPLICATE KEY UPDATE transaction_id = ?";
                                                                                                    if ($stmt = mysqli_prepare($link, $sql)) {
                                                                                                        mysqli_stmt_bind_param($stmt, "isiidi", $tid, $newTime, $cashier_id, $operation_type, $total, $tid) or die("Bind param for transaction on row: " . $row);
                                                                                                        if (mysqli_stmt_execute($stmt)) {
                                                                                                            $sql2 = "SELECT food_id FROM food WHERE food_name = ?";
                                                                                                            if ($stmt2 = mysqli_prepare($link, $sql2)) {
                                                                                                                mysqli_stmt_bind_param($stmt2, "s", $itemDescription) or die ("Can't bind food search on row " . $row . "\n");
                                                                                                                mysqli_stmt_execute($stmt2);
                                                                                                                if ($res = $stmt2->get_result()) {
                                                                                                                    if ($res->num_rows == 1) {
                                                                                                                        $arr = $res->fetch_assoc();
                                                                                                                        $food_id = $arr[food_id];
                                                                                                                        $sql = "INSERT INTO transaction_detail(transaction_id,food_id,quantity) VALUES(?,?,?)";
                                                                                                                        if ($stmt = mysqli_prepare($link, $sql)) {
                                                                                                                            mysqli_stmt_bind_param($stmt, "iii", $tid, $food_id, $quantity) or die("Bind param for transaction_detail on row: " . $row);
                                                                                                                            if (mysqli_stmt_execute($stmt)) {
                                                                                                                                echo "Transaction: " . $tid . " added successfully!<br/>";
                                                                                                                            } else {
                                                                                                                                echo("Unable to add transaction detail on row: " . $row . " (Maybe already exists?)<br/>");
                                                                                                                            }
                                                                                                                        } else {
                                                                                                                            die("Unable to prepare transaction detail on row: " . $row);
                                                                                                                        }
                                                                                                                    } else {
                                                                                                                        echo "Multiple entries exist for " . $food . "</br>";
                                                                                                                    }
                                                                                                                } else {
                                                                                                                    echo "Error searching for food: " . $food . "</br>";
                                                                                                                }
                                                                                                            } else {
                                                                                                                die("Unable to prepare food selection statement");
                                                                                                            }
                                                                                                        } else {
                                                                                                            die("Unable to add transaction on row: " . $row);
                                                                                                        }
                                                                                                    } else {
                                                                                                        die("Could not prepare transaction statement on row: " . $row);
                                                                                                    }
                                                                                                } else {
                                                                                                    echo "Multiple entries exist for " . $cashier . "</br>";
                                                                                                }
                                                                                            } else {
                                                                                                echo "Error searching for employee: " . $cashier . "</br>";
                                                                                            }
                                                                                        } else {
                                                                                            echo "Unable to prepare food selection statement";
                                                                                        }
                                                                                    } else {
                                                                                        die("Could not add food on row: " . $row);
                                                                                    }
                                                                                } else {
                                                                                    die("Could not prepare food statement on row: " . $row);
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    } else {
                                                        die("Could not add employee on row: " . $row);
                                                    }
                                                } else {
                                                    die("Could not prepare employee statement on row: " . $row);
                                                }
                                            } else {
                                                die("Could not add category on row: " . $row);
                                            }
                                        } else {
                                            die("Could not prepare category statement on row: " . $row);
                                        }
                                    } else {
                                        die("Error adding department for row: " . $row);
                                    }
                                } else {
                                    die("Could not prepare department statement on row: " . $row);
                                }
                            } else {
                                echo "Wrong file uploaded/Data not formatted correctly";
                            }
                        }
                    }
                    fclose($handle);
                } else {
                    echo "Could not open file";
                }

            }
            ?>
        </div>
    </div>
</div>

