<?php
    session_start();
    require 'library/constants.php';
    require 'library/mySQLfunctions.php';
    require 'library/generalfunctions.php';

    // from url
    $id = $_GET["id"];

    // determine where called from
    $calledfrommain = false;
    $calledfrommodifyuser = false;
    if (substr($id,0,4) == "main") {
        $calledfrommain = true;
        $caller = $id;
    }
    else {
        $calledfrommodifyuser = true;
        $name = $_GET["name"];
    }

    // Called from modifyuser.php
    if ($calledfrommodifyuser) {
        // get entries from HTML post
        $status      = $_POST["newstatus"];
        $type        = $_POST["newtype"];
        $email       = $_POST["newemail"];

        if (isset($_SESSION['modsuccess'])) unset($_SESSION['modsuccess']);
        $validemail = checkValidUserEmail($email, $_SESSION['modsuccess']);

        if($validemail) {
            // if an admin changes their own type, then they will not be allowed back through
            // the screens
            if ($_SESSION['gv_userID'] == $id) $_SESSION['gv_userType'] = $type;

            openDB($connect, $select);
            $query = sprintf("UPDATE `users` SET `type` = '%s', `status` = '%s', `email` = '%s' WHERE `id`='%u'",
                              mysql_real_escape_string($type),
                              mysql_real_escape_string($status),
                              mysql_real_escape_string($email),
                              mysql_real_escape_string($id));
            mysql_query($query) or die('Error, updating user information query failed<br/>' . $query);
            postUserActivity ($_SESSION['gv_userID'], "Modified user info for " . mysql_real_escape_string($name));
            closeDB($connect);
            $_SESSION['modsuccess']="true";
        }
        javaRedirect(("modifyuser.php?id=".$id));
    }

    // Called from main.php
    if ($calledfrommain) {

        // called from mainpass form
        if ($caller == "mainpass") {
            $new = $_POST["new"];
            $ver = $_POST["verify"];
            if (isset($_SESSION['passerror'])) unset($_SESSION['passerror']);
            if (isset($_SESSION['passsuccess'])) unset($_SESSION['passsuccess']);
            $validpass = checkValidPassword($new, $ver);

            // if password is valid proceed
            if($validpass) {
                $new = hashPassword($new);
                openDB($connect, $select);
                $query = sprintf("UPDATE `users` SET `password` = '%s' WHERE `id`='%u'",
                                  mysql_real_escape_string($new),
                                  mysql_real_escape_string($_SESSION['gv_userID']));
                mysql_query($query) or die('Error, updating user information query failed<br/>' . $query);
                postUserActivity ($_SESSION['gv_userID'], "Changed password.");
                closeDB($connect);
                $_SESSION['passsuccess']=true;
            }
        } // end of called from mainpass form
        
        // called from mainemail form
        else {
            $email = $_POST["newemail"];
            if (isset($_SESSION['emailerror'])) unset($_SESSION['emailerror']);
            if (isset($_SESSION['emailsuccess'])) unset($_SESSION['emailsuccess']);
            $validemail = checkValidUserEmail ($email);

            if ($validemail) {
                openDB($connect, $select);
                $query = sprintf("UPDATE `users` SET `email` = '%s' WHERE `id`='%u'",
                                  mysql_real_escape_string($email),
                                  mysql_real_escape_string($_SESSION['gv_userID']));
                mysql_query($query) or die('Error, updating user information query failed<br/>' . $query);
                $_SESSION['gv_userEmail'] = $email; // must change session variable for logged in user
                postUserActivity ($_SESSION['gv_userID'], "Changed email address.");
                closeDB($connect);
                $_SESSION['emailsuccess']=true;
            }
        }

        javaRedirect("mainmenu.php");

    }

?>