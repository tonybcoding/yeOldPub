<?php
    session_start();
    require 'constants.php';
    require 'mySQLfunctions.php';
    require 'generalfunctions.php';

    // get entries from HTML post
    $name        = $_POST["name"];
    $email       = $_POST["email"];
    $pass        = $_POST["password"];
    $verpass     = $_POST["verifypassword"];
    $optin       = $_POST["optinfornotification"];

    // Open database session
    openDB($connect, $select);
    
    // set session variable to determine if adding user was successful
    $_SESSION['gv_failureReason'] = NULL;
    $validname = checkAddUserName($name, $_SESSION['gv_failureReason']);
    $validemail = checkValidUserEmail($email, $_SESSION['gv_failureReason']);
    $validpassword = checkValidPassword($pass, $verpass, $_SESSION['gv_failureReason']);
    
    $_SESSION['gv_addSuccess'] = false;
    if ($validname && $validemail && $validpassword) $_SESSION['gv_addSuccess'] = true;
    
    // add to database if valid entry
    $password = hashPassword($pass);
    ($optin == "yes") ? $opt = "Yes" : $opt = "No";
    if($_SESSION['gv_addSuccess']) {
    $type = "Registered";
    $status = "Active";
    $values = array("NULL",
                    "\"".mysql_real_escape_string($name)."\"",
                    "\"".mysql_real_escape_string($password)."\"",
                    "\"".mysql_real_escape_string($type)."\"",
                    "\"".mysql_real_escape_string($status)."\"",
                    "\"".mysql_real_escape_string($email)."\"",
                    "\"".mysql_real_escape_string($opt)."\"",
                    CURRENT_TIMESTAMP);
    mySQLInsert("users", $values);
    }

    // Close resources
    closeDB($connect);
    javaRedirect("../welcometoyeoldpub.php?loc=".$_SESSION['loc']); // return to registration page with message

?>