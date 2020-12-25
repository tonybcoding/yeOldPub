<?php
    session_start();
    require 'library/constants.php';
    require 'library/mySQLfunctions.php';
    require 'library/generalfunctions.php';

    // retrieve the POST information from the calling page
    $varusername = $_POST["username"];
    $varpassword = $_POST["password"];


    // if no user name entered bypass checks and return
    if ($varusername != NULL) {

        openDB($connect, $select);

        // Initialize user status variables
        $userfound     = false;
        $validpassword = false;
        $useractive    = false;

        // query database for varusername
        $query = sprintf("SELECT * FROM `users` WHERE `name` = '%s'",
                          mysql_real_escape_string($varusername));
        $result = mysql_query($query);
        if (!$result) die;

        $row = mysql_fetch_assoc($result);
        $userfound     = ($varusername == $row['name']);

        $hashedpassword = hashPassword($varpassword);
        $validpassword = ($hashedpassword == $row['password']);
        $userstatus    = $row['status'];

        if ($userfound && $validpassword && ($userstatus != "Disabled")) {

            // Set session variables, which will be used throughout log in
            $_SESSION['gv_user']        = $varusername;
            $_SESSION['gv_userID']      = $row['id'];
            $_SESSION['gv_userType']    = $row['type'];
            $_SESSION['gv_userTypeNum'] = getUserTypeLevel($_SESSION['gv_userType']);
            $_SESSION['gv_userEmail']   = $row['email'];
            $_SESSION['gv_sessionID']   = "nm59d7f8gs2";

            // add user login activity
            postUserActivity ($_SESSION['gv_userID'],'Log In');

            // free resources prior to redirecting
            mysql_free_result ($result);
            closeDB($connect);
            javaRedirect("mainmenu.php");
        }
        else {
            if ((!$userfound) || (!$validpassword)) {
               $_SESSION['gv_loginstatus']="User name or password error.  Please try again.";
            }
            else {
               $_SESSION['gv_loginstatus']="User account is Disabled.  Please contact webmaster.";
            }

           // free resources prior to redirecting
           mysql_free_result ($result);
           closeDB($connect);
           javaRedirect("login.php");
        }
    }
    else {
        javaRedirect("login.php");
    }
?>