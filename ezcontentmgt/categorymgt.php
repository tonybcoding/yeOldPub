<?php
    session_start();
    require 'library/constants.php';
    require 'library/htmlfunctions.php';
    require 'library/mySQLfunctions.php';
    require 'library/generalfunctions.php';

    // print opening HTML
    printOpenHTML("Category Management");

    // If user is not proper level, do not let them proceed
    dieIfNotProperLevel(ADMINISTRATOR, $_SESSION['gv_userTypeNum']);
/*  this is commented out until I determine if I ever want contribs here
    if not, then i need to remove all contributor checks in this file and
    "child" files
    // If not valid session, exit.
    dieIfNotValidSession();
*/
    // determine if user is admin or not
    ($_SESSION['gv_userType']==0) ? $admin = true : $admin = false;

    // openDB connection
    openDB($connect, $selected);

    // sub menu
    $menuoptions = array( array ("mainmenu.php", "Back to Main"),
                          array ("logout.php","Log Out"));
    menuLinks($menuoptions);
    printBreak(1);

    // admin only menu options
    if ($admin) {
        $menuoptions = array( array("#view","View/Modify Categories (this page)"),
                              array ("#add","Add Category (this page)"));
        menuLinks($menuoptions);
        printBreak(1);
        printf("<hr>");
    }
    
    ////////////////////////////
    // view/modify categories //
    ////////////////////////////
    printf("<a name=\"view\"></a>");
    printBreak(1);
    printCenter("<h3>View/Modify Categories</h3>");
    printBreak(1);
    printCenter("To modify a category, click on the category.");

    // Draw table with user contents
    createTable(MAINTBLSIZE,1,5);
        rowstart();
        printCenter("<b>Category</b>");
        nextcell();
        printCenter("<b>URL</b>");
        nextcell();
        printCenter("<b>Status</b>");
        nextcell();
        printCenter("<b>Type</b>");
        nextcell();
        printCenter("<b>Description</b>");
        nextcell();
        printCenter("<b>Date Created</b>");
        rowend();

        // get category information
        $categories = getCategories();
        if (!$admin) $moderated = getModerated($_SESSION['gv_userID']);
        for($i=0; $i < count($categories); $i++) {
            if (!$admin) {
                $show = false;
                for ($x=0; $x<count($moderated); $x++) {
                    if ($moderated[$x]==$categories[$i][0]) $show = true;
                }
            }
            else {
                $show = true;  // show all for admins
            }
            if ($show) {
                rowstart();
                printf("<a href=\"modifycategory.php?id=".$categories[$i][0]."\">".$categories[$i][1]."</a>");
                nextcell();
                printf($categories[$i][2]);
                nextcell();
                printf($categories[$i][3]);
                nextcell();
                printf($categories[$i][6]);
                nextcell();
                printf($categories[$i][4]);
                nextcell();
                printf($categories[$i][5]);
                rowend();
            }
        }
    closeTable();

    // only admins can add category
    if ($admin) {
        //////////////////
        // Add category //
        //////////////////
        createTable(MAINTBLSIZE,0,0);
            printBreak(2);
            printf("<hr>");
            printf("<a name=\"add\"></a>");
            printBreak(1);
            printCenter("<h3>Add Category</h3>");

            // is this variable is set, then an add has been attempted
            if (isset($_SESSION['gv_addmodSuccess'])) {
                // if this is also set, then it was a failure
                if (isset($_SESSION['gv_failureReason'])) {
                  printCenter("<h4>Entry Error.<br/>" . $_SESSION['gv_failureReason'] . "</h4>");
                  unset($_SESSION['gv_addmodSuccess']);
                }
                // it was a success
                else {
                  printCenter("<b>Add successfull.</b>");
                }
                unset($_SESSION['gv_addmodSuccess']);
                unset($_SESSION['gv_failureReason']);
            }
        closeTable();

        createForm("form1", "post", "addcategory.php");
            createTable(SUBTBLSIZE,0,5);
                // text fields
                rowstart();
                printf("Category: <input type=\"text\" name=\"category\">");
                nextcell();
                printf("URL: <input type=\"text\" name=\"url\">");
                nextcell();
                printf("Desciprtion: <input type=\"text\" name=\"description\">");
                nextcell();
                createCatStatusDropDown("status", "Status", NULL);
                nextcell();
                createCatTypeDropDown("type", "Type", NULL);
                rowend();
            closeTable();

            // choose initial moderators
            printBreak(1);
            printCenter("Select Moderators (hold CTRL to select multiple)");
            printBreak(1);
            $users = getModeratorsandAbove();  // only get active moderators
            openPar("center");
            createSelectMenu("moderators[]", 10, $users);
            closePar();

            // submit button
            submitbutton("center","submit","Add Category");
        closeForm();
    } // end of Add category
    
    // closeDB connection
    closeDB($connect);

    // close HTML statements
    printCloseHTML();

?>
