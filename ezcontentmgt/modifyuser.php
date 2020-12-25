<?php
    session_start();
    require 'library/constants.php';
    require 'library/htmlfunctions.php';
    require 'library/generalfunctions.php';
    require 'library/mySQLfunctions.php';
    require 'library/parsefunctions.php';

    // retreive ID of user to modify from URL
    $usertomod = $_GET["id"];

    // Create opening HTML
    printOpenHTML("Modify User");

    // If user is not proper level, do not let them proceed
    dieIfNotProperLevel(ADMINISTRATOR, $_SESSION['gv_userTypeNum']);

    // open database session
    openDB($connect, $selected);

    ////////////////////////////////////////////////////
    ////////////////////////////////////////////////////
    //                                                //
    // Retrieve and Display User Personal Information //
    //                                                //
    ////////////////////////////////////////////////////
    ////////////////////////////////////////////////////

    // Get row array and populate variables
    getUserInfo($usertomod, $name, $type, $email, $status);

    // potential for multiple forms, so initialize
    $formnum = 1;

    printCenter("<a href=\"usermgt.php\">Back to User Management</a> | <a href=\"contentmgt.php?from=moduser&id=".$usertomod."\">To ".$username." content</a> | <a href=\"logout.php\">Log Out</a>");
    printBreak(1);

    createTable(MAINTBLSIZE,1,0);
        rowstart();
        printBreak(1);
        printCenter("<h3>User \"" . $name. "\" Personal Information</h3>");

        // If this is set, there has been an attempt to modify user...check results
        if (isset($_SESSION['modsuccess'])) {
            if ($_SESSION['modsuccess'] != "true") {
                printCenter("<h4>Entry error: ". $_SESSION['modsuccess'] . "<h4/>");
            }
            else {
                printCenter("<b>Changes successfully applied.<b/>");
            }
            unset($_SESSION['modsuccess']);
        }
            
        createForm("form".$formnum, "post", ("applyuserchanges.php?id=".$usertomod."&name=".$name));
        $formnum++;
        // fields to post:  newstatus, newtype, newemail
            createTable(SUBTBLSIZE,0,5);
                rowstart();
                printf("<p align=\"right\"><b>Account Status</b></p>");
                nextcell();
                printf($status);
                nextcell();
                createUserStatusDropDown("newstatus", NULL, $status);

                nextrow();
                printf("<p align=\"right\"><b>Account Type</b></p>");
                nextcell();
                printf($type);
                nextcell();
                createUserTypeDropDown("newtype", NULL, $type);

                nextrow();
                printf("<p align=\"right\"><b>Email Address</b></p>");
                nextcell();
                printf($email);
                nextcell();
                printf("<input type=\"text\" name=\"newemail\" value=\"".$email."\" size=\"20\">");

                rowend();
            closeTable();
            submitbutton("center", "submit", "Apply User Personal Changes");
        closeForm();
        rowend();
    closeTable();

    ///////////////////////////////////////////////////////////////////
    // Retrieve and display categories assigned as moderator as      //
    // checkboxes so admin can change here                           //
    ///////////////////////////////////////////////////////////////////

    printBreak(1);
    $categories = getCategories();
    $moderated = getModerated($usertomod);
    $checked = array();
    $list = array();

    for($i=0; $i<count($categories); $i++) {
        $checked[$i] = false;
        $list[$i][0] = $categories[$i][0];
        $list[$i][1] = $categories[$i][1];


        for($x=0; $x<count($moderated); $x++) {
            if ($moderated[$x] == $list[$i][0]) {  // if id in catID Moderated = id in List
                $checked[$i] = true;
                $x = count($moderated); // escape early if found
            }
        }
    }

    createTable(MAINTBLSIZE,1,0);
        printf("<hr>");
        printBreak(1);
        rowstart();
        printBreak(1);
        printCenter("<h3>User \"" . $name. "\" Moderated Categories</h3>");
        if (isset($_SESSION['modcatsucces'])) {
            printCenter("<b>Changes successfully applied.<b/>");
            unset($_SESSION['modcatsucces']);
        }
        printBreak(1);

        createTable(SUBTBLSIZE,0,0);
            createForm("form".$formnum, "post", ("applyusermoderatedcatchanges.php?id=".$usertomod));
            $formnum++;
                    rowstart();
                    // create form and table and test submitted results....
                    createCheckBoxes("moderated[]", $list, $checked, false);
                    nextrow();
                    printBreak(1);
                    submitbutton("center","modapply","Apply Moderator Changes");
                    rowend();
            closeForm();
        closeTable();
        rowend();
    closeTable();

    ////////////////////////////////////////////////////
    // Retrieve and Display User Activity Information //
    ////////////////////////////////////////////////////

    createTable(MAINTBLSIZE,0,0);
        printBreak(1);
        printf("<hr>");
        printBreak(1);
        printCenter("<h3>User Activity</h3>");
        printBreak(1);
        createTable(SMALLTBLSIZE,1,2);
            $query = sprintf("SELECT * FROM `useractivity` WHERE `userID` = '%u' ORDER BY `dateTime` DESC",
                                     mysql_real_escape_string($usertomod));
            $result = mysql_query($query);
            while ($row = mysql_fetch_assoc($result)) {
                rowstart();
                printf($row['dateTime']);
                nextcell();
                printf($row['activity']);
                rowend();
            }
            mysql_free_result ($result);
        closeTable();
    closeTable();
    printBreak(3);

    // close database session
    closeDB($connect);
    
    // Create closing HTML
    printCloseHTML();
?>
