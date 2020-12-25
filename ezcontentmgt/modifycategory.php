<?php
    session_start();
    require 'library/constants.php';
    require 'library/htmlfunctions.php';
    require 'library/generalfunctions.php';
    require 'library/mySQLfunctions.php';
    require 'library/parsefunctions.php';

    // retreive ID of user to modify from URL
    $cattomod = $_GET["id"];

    // Create opening HTML
    printOpenHTML("Modify Category");

    // If not valid session, exit.
    dieIfNotValidSession();

    // determine if user is admin or not
    ($_SESSION['gv_userType']==0) ? $admin = true : $admin = false;

    // open database session
    openDB($connect, $selected);

    ///////////////////////////////////////////////
    ///////////////////////////////////////////////
    //                                           //
    // Retrieve and Display Category Information //
    //                                           //
    ///////////////////////////////////////////////
    ///////////////////////////////////////////////
    
    $query = sprintf("SELECT * FROM categories WHERE id = '%u'",
                     mysql_real_escape_string($cattomod));
    $result = mysql_query($query);
    if (!$result) die;

    // Get row array and populate variables
    $row = mysql_fetch_assoc($result);
    $catname    = $row['name'];
    $caturl     = $row['url'];
    $catstatus  = $row['status'];
    $cattype    = $row['type'];
    $catdesc    = $row['description'];
    $catcreated = $row['dateCreated'];
    mysql_free_result ($result);


    // potential for multiple forms, so initialize
    $formnum = 1;

    printCenter("<a href=\"categorymgt.php\">Back to Category Management</a> | <a href=\"contentmgt.php?from=modcat&id=".$cattomod."\">To ".$catname." content</a> | <a href=\"logout.php\">Log Out</a>");
    printBreak(1);

    createTable(MAINTBLSIZE,1,0);
        rowstart();
        printBreak(1);
        printCenter("<h3>Category \"" . $catname. "\" Information</h3>");

        // If this is set, then there has been an attempt to modify...check results
        if (isset($_SESSION['modcatsuccess'])) {
            if (!$_SESSION['modcatsuccess']) {
                printCenter("<h4>Entry error: ". $_SESSION['failurereason'] . "<h4/>");
            }
            else {
                printCenter("<b>Changes successfully applied.<b/>");
            }
            unset($_SESSION['modcatsuccess']);
            unset($_SESSION['failurereason']);
        }

        createForm("form".$formnum, "post", ("applycategorychanges.php?id=".$cattomod));
        $formnum++;

            createTable(SUBTBLSIZE,0,5);
                rowstart();
                printf("<p align=\"right\"><b>Name</b></p>");
                nextcell();
                printf($catname);
                if ($admin){
                    nextcell();
                    textfield("text", "newname", htmlspecialchars($catname), 20);
                    $_SESSION['catname'] = $catname;
                }


                nextrow();
                printf("<p align=\"right\"><b>URL</b></p>");
                nextcell();
                printf($caturl);
                if ($admin) {
                    nextcell();
                    textfield("text", "newurl", $caturl, 20);
                    $_SESSION['caturl'] = $caturl;
                }


                nextrow();
                printf("<p align=\"right\"><b>Status</b></p>");
                nextcell();
                printf($catstatus);
                if ($admin) {
                    nextcell();
                    // set menu options in line with current status
                    createCatStatusDropDown("newstatus", NULL, $catstatus);
                }

                nextrow();
                printf("<p align=\"right\"><b>Type</b></p>");
                nextcell();
                printf($cattype);
                if ($admin) {
                    nextcell();
                    createCatTypeDropDown("newtype", NULL, $cattype);
                }

                nextrow();
                printf("<p align=\"right\"><b>Description</b></p>");
                nextcell();
                printf($catdesc);
                if ($admin) {
                    nextcell();
                    textfield("text", "newdesc", htmlspecialchars($catdesc), 40);
                }

                rowend();
            closeTable();
            if($admin) submitbutton("center", "submit", "Apply Category Changes");
        closeForm();
        rowend();
    closeTable();


    ///////////////////////////////////////////////////////////////////
    // Retrieve and display moderators assigned to category as       //
    // checkboxes so admin can change here, IF ADMIN                 //
    ///////////////////////////////////////////////////////////////////
    if ($admin){
        printBreak(1);
        $users = getModeratorsandAbove();                              // full list of active moderators
        $catmoderated = getCatModerated($cattomod);                    // users moderators of this category

        $checked = array();
        $list = array();
        for($i=0; $i<count($users); $i++) {
            $checked[$i] = false;
            $list[$i][0] = $users[$i][0];
            $list[$i][1] = $users[$i][1];

            for($x=0; $x<count($catmoderated); $x++) {
                if ($catmoderated[$x] == $list[$i][0]) {
                    $checked[$i] = true;
                    $x = count($catmoderated); // escape early if found
                }
            }
        }

        createTable(MAINTBLSIZE,1,0);
            printf("<hr>");
            printBreak(1);
            rowstart();
            printBreak(1);
            printCenter("<h3>Category \"" . $catname. "\" Moderated by</h3>");

            if (isset($_SESSION['modcatsucces'])) {
                printCenter("<b>Changes successfully applied.<b/>");
                unset($_SESSION['modcatsucces']);
            }
            printBreak(1);

            createTable(SUBTBLSIZE,0,0);
                createForm("form".$formnum, "post", ("applycatsmoderatedbyuserschanges.php?id=".$cattomod));
                $formnum++;
                        rowstart();
                        // create form and table and test submitted results....
                        createCheckBoxes("catmoderated[]", $list, $checked, false);
                        nextrow();
                        printBreak(1);
                        submitbutton("center","catapply","Apply Moderator Changes");
                        rowend();
                closeForm();
            closeTable();
            rowend();
        closeTable();
        printBreak(2);
    } // END if admin show this

    // close database session
    closeDB($connect);
    
    // Create closing HTML
    printCloseHTML();
?>
