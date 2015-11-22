<?php
    session_start();
    if(session_destroy()){
        header("Location: https://gunterhans.cloudapp.net");
        die();
    } else {
        echo "<p style='color:red'>Could not destroy session</p>";
    }
?>