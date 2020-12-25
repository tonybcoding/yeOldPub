<?php
    session_start();
    require 'library/constants.php';
    require 'library/mySQLfunctions.php';
    require 'library/generalfunctions.php';

    // get fields from URL ($_GET) and submit ($_POST)
    $id            = $_GET["id"];
    $checked       = $_POST['moderated'];  //  array

    openDB($connect, $selected);

    // delete user entry from moderatortocategory table where userID = $id
    $query = sprintf("DELETE FROM moderatortocategory WHERE userID='%u'",
                             mysql_real_escape_string($id));
    $result=mysql_query($query);
    if (!$result) die;
    
    // insert new moderatortocategory rows
    for ($i=0; $i<count($checked); $i++){
        $values = array("NULL",
                     "\"".mysql_real_escape_string($id)."\"",
                     "\"".mysql_real_escape_string($checked[$i])."\"");
        mySQLInsert("moderatortocategory", $values);
    }
    getUserInfo($id, $username, $usertype, $useremail, $userstatus); // needed for posting activity
    postUserActivity ($_SESSION['gv_userID'], "Modifed moderator settings for: " . mysql_real_escape_string($username));
    if (isset($_SESSION['modcatsucces'])) unset($_SESSION['modcatsucces']);
    $_SESSION['modcatsucces'] = true;
    
    // close db connection
    closeDB($connect);

    javaRedirect(("modifyuser.php?id=".$id));
?>