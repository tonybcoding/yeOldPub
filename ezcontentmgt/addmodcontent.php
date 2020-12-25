<?php
    session_start();
    require 'library/constants.php';
    require 'library/htmlfunctions.php';
    require 'library/mySQLfunctions.php';
    require 'library/generalfunctions.php';

    // print opening HTML
    printOpenHTML("Add & Modify Content");
    
    // open a new DB session
    openDB($connect, $selected);

    // If not valid session, exit.
    dieIfNotValidSession();
    
    // check admin status
    ($_GET['priv'] == "admin") ? $admin=true : $admin=false;

    // initially assume an "Add"
    $action = "add";
    $id     = "-1";

    // is this modify?
    $modify = false;
    if (isset($_GET['id'])) {
        // user wants to modify content
        $modify = true;
        $conttomod = $_GET['id'];
        $query=sprintf("SELECT * FROM content WHERE id='%u'",
                        mysql_real_escape_string($conttomod));
        $result = mysql_query($query);
        $row = mysql_fetch_assoc($result);
        $c_header        = htmlspecialchars($row['heading']);            // any can change as long as it is unique
        $c_text          = htmlspecialchars($row['content']);              // any can change
        $c_column        = $row['column'];            // any can change
        $c_status        = $row['status'];            // any can change
        $c_sticky        = $row['sticky'];            // admin can change
        $action          = "modify";        // needed for previewcontent.php
        $id              = $conttomod;      // needed for previewcontent.php
        mysql_free_result($result);
        unset($_GET['id']);
    }

    // is user returning from preview?  If so, retrieve session variables
    if (isset($_GET['from'])) {
        $from = $_GET['from'];
        if($from="preview"){
            $conttomod      = $_SESSION['contid'];
            $c_header       = htmlspecialchars(stripslashes($_SESSION['heading']));
            $c_text         = htmlspecialchars(stripslashes($_SESSION['content']));
            $c_column       = $_SESSION['column'];
            $c_status       = $_SESSION['status'];
            $c_sticky       = $_SESSION['sticky'];
            $catsassignedto = $_SESSION['selectedcategories'];
            unset($_SESSION['contid']);
            unset($_SESSION['heading']);
            unset($_SESSION['content']);
            unset($_SESSION['column']);
            unset($_SESSION['status']);
            unset($_SESSION['sticky']);
            unset($_SESSION['selectedcategories']);
        }
    }

    // get list of categories, checked based on moderated
//    if (!$admin) $modcats = getModerated($_SESSION['gv_userID']);
    $categories = getCategories();
    if($modify) {  //if add, this is not needed; if returning from "preview" this has already been set
        $catsassignedto = getContentAssignedToCats($conttomod); // get list of categories this content is assigned to
    }
    $list = array();
    for($i=0; $i<count($categories); $i++) {
        // if not admin, then only add/show categories for which contributor is moderator of
//        if (!$admin) {
//            $assigned = false;
//            for ($x=0; $x<count($modcats); $x++) {  // if not admin, check each entry of categories contributor is assigned
//                if ($categories[$i][0] == $modcats[$x]) {
//                    $assigned = true;
//                    $x = count($modcats); // if found, escape early
//                }
//            }
//        }
        // if admin, or if a contrib assigned to this category, then add to list
        if ($admin || $assigned) {
            $checked[$i] = false;
            $list[$i][0] = $categories[$i][0];
            $list[$i][1] = $categories[$i][1];
            for($x=0; $x<count($catsassignedto); $x++) {
                if ($catsassignedto[$x] == $list[$i][0]) {
                    $checked[$i] = true;
                    $x = count($catsassignedto); // escape early if found
                }
            }
        }
    }
    printCenter("<a href=\"contentmgt.php\">Back to Content Management</a> | <a href=\"logout.php\">Log Out</a>");
    printBreak(1);

    ($admin) ? $priv="admin" : $priv="contrib";
    createForm("addcontentform", "post", "previewcontent.php?id=".$id."&action=".$action."&priv=".$priv);
        createTable(MAINTBLSIZE, 0, 5);
            rowstart();
                createTable(MAINTBLSIZE,0,0); // container for text box
                    rowstart();
                    printf("<font face=\"Courier New, Courier, mono\" size=\"2\">");
                    printf("Before/after bold: ".BOLD);
                    printBreak(1);
                    printf("Before/after italic: ".ITALIC);
                    printBreak(1);
                    printf("Before/after underline: ".UNDERLINE);
                    printBreak(1);
                    printf(EMAILLINK."Text to be underlined for email".EMAILSTART."email@email.com".EMAILLINK);
                    printBreak(1);
                    printf(HYPERLINK."Text to be underlined for hyperlink".LINKSTART."http://www.yeoldpub.com/".HYPERLINK);
                    printBreak(2);
                    printf("</font>");
                    nextrow();
                    if ($modify || $from=="preview") {
                        $heading = $c_header;
                    }
                    else {
                        $heading = NULL;
                    }
                    printf("<h3>Heading: </h3>");
                    textfield("text", "heading", $heading, 60);
                    nextrow();
                    printBreak(1);
                    nextrow();
                    openPar("center");
                    if ($modify || $from=="preview") {
                        $text = $c_text;
                    }
                    else {
                        $text = NULL;
                    }
                    printf("<h3>Body: </h3>");
                    printf("<textarea name=\"content\" cols=\"75\" rows=\"20\" wrap=\"VIRTUAL\">".$text."</textarea>");
                    closePar();
                    rowend();
                closeTable();
            rowend();

            rowstart();
                createTable(MAINTBLSIZE,0,3);
                    rowstart();
                    openPar("center");
                    createDropDownMenu("position", "Position", array(array("Center","Center", $c_column),
                                                                 array("Left","Left", $c_column),
                                                                 array("Right","Right", $c_column)));
                    closePar();
                    nextcell();
                    openPar("center");
                    createDropDownMenu("status", "Status", array(array("Pending","Pending", $c_status),
                                                             array("Approved","Approved", $c_status),
                                                             array("Not approved","Not Approved", $c_status),
                                                             array("Disabled", "Disabled", $c_status)));
                    if ($admin) { // only admin can apply sticky
                        closePar();
                        nextcell();
                        openPar("center");
                        createDropDownMenu("sticky", "Sticky", array(array("Not Sticky","Not Sticky", $c_sticky),
                                                                    array("Sticky","Sticky", $c_sticky)));
                    }
                    closePar();
                    rowend();
                closeTable();
            rowend();
            rowstart();
                createTable(MAINTBLSIZE,0,0);
                    rowstart();
                    createCheckBoxes("categories[]", $list, $checked, false);
                    nextrow();
                    printBreak(1);
                    submitbutton("center","submitcontent","Preview/Submit");
                    rowend();
                closeTable();
            rowend();
        closeTable();
    closeForm();
    printBreak(4);

    // close HTML statements and DB session
    printCloseHTML();
    closeDB($connect);

?>
