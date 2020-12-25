<?php
    session_start();
    require 'library/mySQLfunctions.php';
    require 'library/generalfunctions.php';
    
    // if not valid session die
    dieIfNotValidSession();
    
    // open a new DB session
    openDB($connect, $selected);
    
    // determine if this is an add or a modify
    $action = $_GET['action'];
    
    // set session variable to determine if adding category was successful
    if (isset($_SESSION['gv_addcontentSuccess'])) unset($_SESSION['gv_addcontentSuccess']);
    if (isset($_SESSION['gv_failureReason'])) unset($_SESSION['gv_failureReason']);
    $_SESSION['gv_addcontentSuccess'] = true;

    // ensure nothing is empty
    if (($_SESSION['heading']==NULL) || ($_SESSION['content']==NULL)) {
        $_SESSION['gv_addcontentSuccess'] = false;
        $_SESSION['gv_failureReason'] .= "Empty fields are not permitted for Heading or Content.<br/>";
    }

    //check if heading already exists
    $query = sprintf("SELECT id, heading FROM content");
    $result = mysql_query($query);
    if (!$result) die;
    $i = 0;
    while ($row = mysql_fetch_assoc($result)) {
        if ($row['heading'] == stripslashes($_SESSION['heading'])) {
            // if modify and header has changed (indicated by a different id with the same header
            // of if this is an add, then set the alarm
            if((($action=="modify") &&  ($row['id'] != $_SESSION['contid'])) || ($action=="add")) {
                $_SESSION['gv_addcontentSuccess'] = false;
                $_SESSION['gv_failureReason'] = "Heading already exists.<br/>";
            }
        }
    }
    mysql_free_result($result);

    /////////////////////////////////////////////////////
    // If all checks have passed, add or update record //
    /////////////////////////////////////////////////////
    if ($_SESSION['gv_addcontentSuccess']) {  // if all checks passed

        $categories = $_SESSION['selectedcategories'];
        switch($action) {
            // if action is to add
            case "add" :
                // insert content record
                $values = array("NULL",
                                "\"".mysql_real_escape_string($_SESSION['gv_userID'])."\"",
                                CURRENT_TIMESTAMP,
                                "NULL",
                                "\"".mysql_real_escape_string(stripslashes($_SESSION['heading']))."\"",
                                "\"".mysql_real_escape_string(stripslashes($_SESSION['content']))."\"",
                                "\"".mysql_real_escape_string($_SESSION['column'])."\"",
                                "\"".mysql_real_escape_string($_SESSION['status'])."\"",
                                "\"".mysql_real_escape_string($_SESSION['sticky'])."\"");
                mySQLInsert("content", $values);

                // need to find contentID
                $query = sprintf("SELECT `id` FROM `content` WHERE heading='%s'",
                                         mysql_real_escape_string(stripslashes($_SESSION['heading'])));
                $result = mysql_query($query);
                $row = mysql_fetch_assoc($result);
                $contid = $row['id'];
                mysql_free_result($result);

                // add content to category information
                for ($i=0; $i<count($categories); $i++) {
                    $values = array("NULL",
                                    "\"".mysql_real_escape_string($contid)."\"",
                                    "\"".mysql_real_escape_string($categories[$i])."\"");
                    mySQLInsert("contenttocategory", $values);
                }
                postUserActivity($_SESSION['gv_userID'], "Added contend with Heading: ".mysql_real_escape_string(stripslashes($_SESSION['heading'])));

            // end switch for add
            break;

            // if action is to modify
            case "modify" :

                // update record
                $query = sprintf("UPDATE `content` SET
                                 `lastmodified` = CURRENT_TIMESTAMP,
                                 `heading` = '%s',
                                 `content` = '%s',
                                 `column`  = '%s',
                                 `status`  = '%s',
                                 `sticky`  = '%s' WHERE `id`='%u'",
                                 mysql_real_escape_string(stripslashes($_SESSION['heading'])),
                                 mysql_real_escape_string(stripslashes($_SESSION['content'])),
                                 mysql_real_escape_string($_SESSION['column']),
                                 mysql_real_escape_string($_SESSION['status']),
                                 mysql_real_escape_string($_SESSION['sticky']),
                                 mysql_real_escape_string($_SESSION['contid']));
                mysql_query($query) or die('Error, updating content information failed<br/>' . $query);
                postUserActivity ($_SESSION['gv_userID'], "Modifed content for heading: ".mysql_real_escape_string(stripslashes($_SESSION['heading'])));

                // delete all contenttocategory with this contentid
                $query = sprintf("DELETE FROM `contenttocategory` WHERE `contentID`='%u'",
                                         mysql_real_escape_string($_SESSION['contid']));
                $result=mysql_query($query);
                if (!$result) die;

                // add contenttocategory records back based on selections
                for ($i=0; $i<count($categories); $i++){
                    $values = array("NULL",
                                 "\"".mysql_real_escape_string($_SESSION['contid'])."\"",
                                 "\"".mysql_real_escape_string($categories[$i])."\"");
                    mySQLInsert("contenttocategory", $values);
                }

            // end switch fo modify
            break;
            
        } // end of switch determining if this is an add or modify operation
        
    } // end if checking if all test passed

    // unset session variables that were set by previewcontent.php
    unset($_SESSION['contid']);
    unset($_SESSION['heading']);
    unset($_SESSION['content']);
    unset($_SESSION['column']);
    unset($_SESSION['status']);
    unset($_SESSION['sticky']);
    unset($_SESSION['selectedcategories']);

    // free resources and return
    closeDB($connect);
    javaRedirect("contentmgt.php");

?>
