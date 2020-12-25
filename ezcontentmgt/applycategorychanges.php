<?php
    session_start();
    require 'library/constants.php';
    require 'library/mySQLfunctions.php';
    require 'library/generalfunctions.php';

    // from url
    $id = $_GET["id"];

    // open db connection
    openDB($connect, $selected);

    // get entries from HTML post
    $name        = stripslashes($_POST["newname"]);
    $url         = $_POST["newurl"];
    $status      = $_POST["newstatus"];
    $type        = $_POST["newtype"];
    $desc        = stripslashes($_POST["newdesc"]);
    $categories  = getCategories();

    if (isset($_SESSION['modcatsuccess'])) unset($_SESSION['modcatsuccess']);
    if (isset($_SESSION['failurereason'])) unset($_SESSION['failurereason']);
    $_SESSION['modcatsuccess'] = true; // initially start as true

    // ensure nothing is empty
    if (($name==NULL) || ($url==NULL) || ($desc==NULL)) {
        $_SESSION['modcatsuccess'] = false;
        $_SESSION['failurereason'] .= "Empty field are not permitted for Category, URL, or Descirption.<br/>";
    }

    // ensure $desc is less than 180 characters
    if (strlen($desc) > 180) {
        $_SESSION['modcatsuccess'] = false;
        $_SESSION['failurereason'] .= "Description must be less than 180 characters.<br/>";
    }
    
    // if name as changed search for duplicate
    if ($_SESSION['catname'] != $name) {
        $same = false;
        for ($i=0; $i<count($categories); $i++) {
            if ($categories[$i][1] == $name) {
                $same = true;
                $i = count($categories);
            }
        }
        if ($same) {
            $_SESSION['modcatsuccess'] = false;
            $_SESSION['failurereason'] .= "Category already exists.<br/>";
        }
    }

    // if url as changed search for duplicate
    if ($_SESSION['caturl'] != $url) {
        $same = false;
        for ($i=0; $i<count($categories); $i++) {
            if ($categories[$i][2] == $url) {
                $same = true;
                $i = count($categories);
            }
        }
        if ($same) {
            $_SESSION['modcatsuccess'] = false;
            $_SESSION['failurereason'] .= "URL already exists.<br/>";
        }

    }

    // ensure url doesn't have spaces or special characters
    if (!ctype_alpha($url)) {
        $_SESSION['modcatsuccess'] = false;
        $_SESSION['failurereason'] .= "URL may only contain alphabetic characters.<br/>";
    }

    // if entries are valid
    if($_SESSION['modcatsuccess']) {
        $query = sprintf("UPDATE `categories` SET `name` = '%s', `url` = '%s', `status` = '%s', `type` = '%s', `description` = '%s' `themeID` = '%u' WHERE `id`='%u'",
                          mysql_real_escape_string($name),
                          mysql_real_escape_string($url),
                          mysql_real_escape_string($status),
                          mysql_real_escape_string($type),
                          mysql_real_escape_string($desc),
                          mysql_real_escape_string(0),        // 0 forces default in code / need to add selector
                          mysql_real_escape_string($id));
        mysql_query($query) or die('Error, updating category information query failed<br/>' . $query);
        postUserActivity ($_SESSION['gv_userID'], "Modified category info: " . mysql_real_escape_string($name));
    }

    closeDB($connect);
    javaRedirect("modifycategory.php?id=".$id);
?>