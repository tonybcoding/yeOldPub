<?php
    session_start();
    require 'library/constants.php';
    require 'library/mySQLfunctions.php';
    require 'library/generalfunctions.php';

    // get entries from HTML post
    $category    = stripslashes($_POST['category']);
    $url         = $_POST['url'];
    $desc        = stripslashes($_POST['description']);
    $status      = $_POST['status'];
    $moderators  = $_POST['moderators'];
    $type        = $_POST['type'];

    // Open db connection
    openDB($connect, $selected);

    $categories  = getCategories();

    // set session variable to determine if adding category was successful
    if (isset($_SESSION['gv_addmodSuccess'])) unset($_SESSION['gv_addmodSuccess']);
    if (isset($_SESSION['gv_failureReason'])) unset($_SESSION['gv_failureReason']);
    $_SESSION['gv_addmodSuccess'] = true;

    // ensure nothing is empty
    if (($category==NULL) || ($url==NULL) || ($desc==NULL)) {
        $_SESSION['gv_addmodSuccess'] = false;
        $_SESSION['gv_failureReason'] .= "Empty field are not permitted for Category, URL, or Descirption.<br/>";
    }

    // ensure desc is no greather than 180 characters
    if(strlen($desc) > 180) {
        $_SESSION['gv_addmodSuccess'] = false;
        $_SESSION['gv_failureReason'] .= "Description may not exceed 180 characters.<br/>";
    }
    //check if name or url already exists
    $same = false;
    for ($i=0; $i<count($categories); $i++) {
        if (($categories[$i][1] == $category) || $categories[$i][2] == $url) {
            $same = true;
            $i = count($categories);
        }
    }
    if ($same) {
        $_SESSION['gv_addmodSuccess'] = false;
        $_SESSION['gv_failureReason'] .= "Category or URL already exists.<br/>";
    }

    // ensure url doesn't have spaces or special characters
    if (!ctype_alpha($url)) {
        $_SESSION['gv_addmodSuccess'] = false;
        $_SESSION['gv_failureReason'] .= "URL may only contain alphabetic characters.<br/>";
    }

    // ALL CHECKS HAVE BEEN MADE.  NOW ADD CATEGORY if passed.
    if ($_SESSION['gv_addmodSuccess']) {
        $values = array("NULL",
                        "\"".mysql_real_escape_string($category)."\"",
                        "\"".mysql_real_escape_string($url)."\"",
                        "\"".mysql_real_escape_string($status)."\"",
                        "\"".mysql_real_escape_string($type)."\"",
                        "\"".mysql_real_escape_string($desc)."\"",
                        "0",                            // this forces "default" theme in code, need to add selector
                        CURRENT_TIMESTAMP);
        mySQLInsert("categories", $values);
        postUserActivity ($_SESSION['gv_userID'], "Added category: " . mysql_real_escape_string($category));

        // get category id
        $query = sprintf("SELECT `id` FROM `categories` WHERE `name` = '%s'", mysql_real_escape_string($category));
        $result = mysql_query($query);
        $row = mysql_fetch_assoc($result);
        $catid = $row['id'];

        // Add moderator to category information
        for ($i=0; $i<count($moderators); $i++){
            $values = array("NULL",
                         "\"".mysql_real_escape_string($moderators[$i])."\"",
                         "\"".mysql_real_escape_string($catid)."\"");
            mySQLInsert("moderatortocategory", $values);
        }
    }

    closeDB($connect);
    javaRedirect("categorymgt.php#add");
?>