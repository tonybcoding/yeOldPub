<?php
    session_start();
    require 'library/constants.php';
    require 'library/htmlfunctions.php';
    require 'library/mySQLfunctions.php';
    require 'library/generalfunctions.php';
    require 'library/parsefunctions.php';

    // print opening HTML
    printOpenHTML("Content Management");

    // If not valid session, exit.
    dieIfNotValidSession();

    // determine if user is admin or not
    $admin = userHasPermission(ADMINISTRATOR, $_SESSION['gv_userTypeNum']);

    // openDB connection
    openDB($connect, $selected);

    // set initial menu options.  May prepend based on "back to criteria"
    $priv = "contrib";
    if ($admin) $priv = "admin";
    $menuoptions = array( array ("addmodcontent.php?priv=".$priv, "Add Content"),
                          array ("mainmenu.php", "Main Menu"),
                          array ("logout.php","Log Out"));

    // if "from" is part of the URL
    if (isset($from)) unset($from);
    if (isset($_GET['from'])) {
        $from = $_GET['from'];
        switch ($from) {
            case "same" :
                $change = $_GET['change'];
                $id = $_GET['id'];
                if($change=="cat") {
                    if ($id==-1) {
                        $viewmode = "All Categories";
                    }
                    else {
                        getCategoryInfo($id, $catname, $caturl, $catstatus, $cattype, $catdesc);
                        $viewmode = "Category " . $catname;
                    }
                }
                else { // user changed
                    if ($id==-1) {
                        $viewmode = "All Users";
                    }
                    else {
                        getUserInfo($id, $name, $type, $email, $status);
                        $viewmode = "User " . $name;
                    }
                }
                break;
            
            case "moduser" :
                $id = $_GET['id'];
                $change = "user";
                getUserInfo($id, $name, $type, $email, $status);
                $viewmode = "User " . $name;
                array_unshift($menuoptions, array(("modifyuser.php?id=".$id), ("Back to Modify ".$name)));
                break;
            
            case "modcat" :
                $id = $_GET['id'];
                $change = "cat";
                getCategoryInfo($id, $catname, $caturl, $catstatus, $catdesc);
                $viewmode = "Category " . $catname;
                array_unshift($menuoptions, array(("modifycategory.php?id=".$id), ("Back to Modify ".$catname)));
                break;
        } // end of switch to determine caller
    }
    else { // set to default; user based on self
        $change = "user";
        $id = $_SESSION['gv_userID'];
        $viewmode = ("User " . $_SESSION['gv_user']);
    }

    $contentarray = getContent($change, $id);

    // produce user navigation-specific links
    menuLinks($menuoptions);
    printBreak(1);

    // create drop down list array for users and categories
    // add -1 to indicate "ALL users"
    $users = getUsers();
    $categories = getCategories();
    if (count($users)>0) array_unshift($users, array(-1, 'ALL', NULL, NULL, NULL));
    if (count($categories)>0) array_unshift($categories, array(-1, 'ALL', NULL, NULL, NULL));

    ///////////////////
    // view contents //
    ///////////////////
    printCenter("Currently viewing contents based on: <b>".$viewmode."</b>");

    if($admin) {
        createTable(SMALLTBLSIZE,1,5);
            rowstart();
            createForm("selectuserform","post","reloadcontentmgt.php?caller=user");
                openPar("center");
                createDropDownMenu("viewuser", "Users", $users);
                closePar();
                submitbutton("center", "submituser", "Change View");
            closeForm();

            nextcell();
            createForm("selectcategoryform","post","reloadcontentmgt.php?caller=cat");
                openPar("center");
                createDropDownMenu("viewcategory","Categories",$categories);
                closePar();
                submitbutton("center", "submitcategory", "Change View");
            closeForm();
            rowend();
        closeTable();
    }

    // if returning from a successful add or mod, state so
    if (isset($_SESSION['gv_addcontentSuccess'])) {
        if($_SESSION['gv_addcontentSuccess']) {
            printBreak(1);
            printCenter("<h3>Add/Modify operation was successful</h3>");
            unset($_SESSION['gv_addcontentSuccess']);
        }
        else {
            printBreak(1);
            printCenter("<h4>Add/Mod Failure: ".$_SESSION['gv_failureReason']."</h4>");
            unset($_SESSION['gv_addcontentSuccess']);
            unset($_SESSION['gv_failureReason']);
        }
    }

    // Create horizontal line
    createTable(MAINTBLSIZE,0,0);
        printBreak(1);
        printf("<hr>");
    closeTable();

    // table to hold content-specific information
    createTable(LARGETBLSIZE,1,5);
        rowstart();
        printCenter("<b>Heading</b>");
        nextcell();
        printCenter("<b>Content</b>");
        nextcell();
        printCenter("<b>Owner</b>");
        nextcell();
        printCenter("<b>Category Association</b>");
        nextcell();
        printCenter("<b>Status</b>");
        nextcell();
        printCenter("<b>Created</b>");
        nextcell();
        printCenter("<b>Last Modified</b>");
        rowend();
        for ($i=0; $i<count($contentarray); $i++) {
            rowstart();
            printf("<a href=\"addmodcontent.php?id=".$contentarray[$i][0]."&priv=".$priv."\">".$contentarray[$i][4]."</a>");
            nextcell();
            printf(fullparse($contentarray[$i][5]));
            nextcell();
            if ($contentarray[$i][1] == -1) {
                printCenter("<h4>Orphaned</h4>");
            }
            else {
                for($x=0; $x<count($users); $x++) {
                    if ($users[$x][0] == $contentarray[$i][1]) {
                        printf($users[$x][1]);
                        $x = count($users); // escape early
                    }
                }
            }
            nextcell();

            // break apart category string
            if($contentarray[$i][9] == NULL) {
                printCenter("<h3>None</h3>");
            }
            else {
                $cats = explode(",",$contentarray[$i][9]);
                for ($x=0; $x<count($cats); $x++) {
                    for ($index=0; $index<count($categories); $index++) {
                        if ($cats[$x] == $categories[$index][0]) {
                            printCenter($categories[$index][1]);
                            $index = count($categories);
                        }
                    }
                }
            }
            nextcell();
            printf($contentarray[$i][7]);
            nextcell();
            printf($contentarray[$i][2]);
            nextcell();
            if($contentarray[$i][3]==NULL) {
                printf("Not modified");
            }
            else {
                printf($contentarray[$i][3]);
            }
            rowend();
        }
    closeTable();
    printBreak(4);
    
    // closeDB connection
    closeDB($connect);

    // close HTML statements
    printCloseHTML();

?>
