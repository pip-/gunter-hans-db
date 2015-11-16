<?php
    session_start();
    if(session_destroy()){
        header("Location: https://cs3380-pg3f4.cloudapp.net/lab8/");
        die();
    } else {
        echo "<p style='color:red'>Could not destroy session</p>";
    }
?>