<?php
    session_start();
    require 'constants.php';
    require 'mySQLfunctions.php';
    require 'generalfunctions.php';

    // Open DB session
    openDB($connect, $selected);

    // retrieve the POST information from the calling page
    $varusername = $_POST["username"];
    $varpassword = $_POST["password"];

    // Maintain location user is currently visiting.  This is appended to redirects below.
    $append = NULL;
    if ($_SESSION['loc'] != -1) {
        $append="?loc=".$_SESSION['loc'];
    }
    // if no user name entered bypass checks and return
    if ($varusername != NULL) {

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
            $_SESSION['gv_userName']    = $varusername;
            $_SESSION['gv_userIP']      = $_SERVER['REMOTE_ADDR'];
            $_SESSION['gv_userID']      = $row['id'];
            $_SESSION['gv_userType']    = $row['type'];
            $_SESSION['gv_userTypeNum'] = getUserTypeLevel($_SESSION['gv_userType']);
            $_SESSION['gv_userEmail']   = $row['email'];
            $_SESSION['gv_loggedIn']    = true;

            // add user login activity
            postUserActivity ($_SESSION['gv_userID'],'Log In');

            // free resources prior to redirecting
            mysql_free_result ($result);
            closeDB($connect);
            javaRedirect("../welcometoyeoldpub.php".$append);
        }
        else {
            if ((!$userfound) || (!$validpassword)) {
               $_SESSION['gv_loginstatus']="Login error. Please try again.";
            }
            else {
               $_SESSION['gv_loginstatus']="User account is Disabled.";
            }

           // free resources prior to redirecting
           mysql_free_result ($result);
           closeDB($connect);
           javaRedirect("../welcometoyeoldpub.php".$append);
        }
    }
    else {
        closeDB($connect);
        javaRedirect("../welcometoyeoldpub.php".$append);
    }
?>