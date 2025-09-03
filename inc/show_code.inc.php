<?php
    session_start();
    include_once "./check_code.inc.php";
    $_SESSION['check_code'] = check_code();
?>