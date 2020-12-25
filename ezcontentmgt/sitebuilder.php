<?php
    session_start();
    require 'library/constants.php';
    require 'library/htmlfunctions.php';
    require 'library/mySQLfunctions.php';
    require 'library/generalfunctions.php';

    // print opening HTML
    printOpenHTML("Site Builder");

    // If user is not proper level, do not let them proceed
    dieIfNotProperLevel(SUPERUSER, $_SESSION['gv_userTypeNum']);

    // openDB connection
    openDB($connect, $selected);

    // sub menu
    $menuoptions = array( array ("mainmenu.php", "Back to Main"),
                          array ("logout.php","Log Out"),
                          array ("sb_buildit.php", "Just Build It"));
    menuLinks($menuoptions);
    printBreak(1);

    ////////////////////////////
    // view/modify categories //
    ////////////////////////////
    printf("<a name=\"view\"></a>");
    printCenter("<h3>Select Categories to include in build</h3>");
    printBreak(1);
    printCenter("To view category contents and modify their positions, click on the Category Name.");

    // Draw table with category information
    createForm("selectedcategories", "post", "sb_preview.php");
        createTable(LARGETBLSIZE,1,5);
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
            $num = 0;
            for($i=0; $i < count($categories); $i++) {
                rowstart();
                printf("<a href=\"sb_viewcategorycontents.php?id=".$categories[$i][0]."\">".$categories[$i][1]."</a>");
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
                $_SESSION['numOfEntries'] = $num;
                $num++;
            }
        closeTable();
        createTable(SUBTBLSIZE,0,0);
            printBreak(1);
            rowstart();
            submitButton("center", "previewbuild", "Preview");
            rowend();
        closeTable();
    closeForm();

    // closeDB connection
    closeDB($connect);

    // close HTML statements
    printCloseHTML();

?>
