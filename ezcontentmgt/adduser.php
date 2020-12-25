<?php
    session_start();
    require 'library/constants.php';
    require 'library/mySQLfunctions.php';
    require 'library/generalfunctions.php';

    // get entries from HTML post
    $name        = $_POST["name"];
    $email       = $_POST["email"];
    $type        = $_POST["type"];
    $status      = $_POST["status"];

    // Open database session
    openDB($connect, $select);
    
    // set session variable to determine if adding user was successful
    $_SESSION['gv_failureReason'] = NULL;
    $validname = checkAddUserName($name, $_SESSION['gv_failureReason']);
    $validemail = checkValidUserEmail($email, $_SESSION['gv_failureReason']);
    
    $_SESSION['gv_addSuccess'] = false;
    if ($validname && $validemail) $_SESSION['gv_addSuccess'] = true;
    
    // add to database if valid entry
    $password = hashPassword("password");
    if($_SESSION['gv_addSuccess']) {
    $values = array("NULL",
                    "\"".mysql_real_escape_string($name)."\"",
                    "\"".mysql_real_escape_string($password)."\"",
                    "\"".mysql_real_escape_string($type)."\"",
                    "\"".mysql_real_escape_string($status)."\"",
                    "\"".mysql_real_escape_string($email)."\"",
                    CURRENT_TIMESTAMP);
    mySQLInsert("users", $values);
    postUserActivity ($_SESSION['gv_userID'], "Added user: " . mysql_real_escape_string($name));
    }

    // Close resources
    closeDB($connect);
    javaRedirect("usermgt.php#add");

?>