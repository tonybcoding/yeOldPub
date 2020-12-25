<?php
    session_start();
    require 'library/constants.php';
    require 'library/mySQLfunctions.php';
    require 'library/generalfunctions.php';

    // get fields from URL ($_GET) and submit ($_POST)
    $id            = $_GET["id"];
    $checked       = $_POST['catmoderated'];  //  array

    openDB($connect, $selected);

    // delete user entry from moderatortocategory table where userID = $id
    $query = sprintf("DELETE FROM moderatortocategory WHERE categoryID='%u'",
                             mysql_real_escape_string($id));
    $result=mysql_query($query);
    if (!$result) die;

    // insert new moderatortocategory rows
    for ($i=0; $i<count($checked); $i++){
        $values = array("NULL",
                     "\"".mysql_real_escape_string($checked[$i])."\"",
                     "\"".mysql_real_escape_string($id)."\"");
        mySQLInsert("moderatortocategory", $values);
    }
    postUserActivity ($_SESSION['gv_userID'], "Modifed moderator settings for Category ID: " . mysql_real_escape_string($id));
    if (isset($_SESSION['modcatsucces'])) unset($_SESSION['modcatsucces']);
    $_SESSION['modcatsucces'] = true;
    
    // close db connection
    closeDB($connect);

    javaRedirect(("modifycategory.php?id=".$id));
?>