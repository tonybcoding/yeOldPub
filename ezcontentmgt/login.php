<?php
    session_start();
    require 'library/constants.php';
    require 'library/htmlfunctions.php';

    // Create opening HTML
    printOpenHTML("Log On");

    // Display prior attempt login message, if it is set
    if(isset($_SESSION['gv_loginstatus'])) {
        printCenter("<center><h4>".$_SESSION['gv_loginstatus']."</h4></center>");
        unset($_SESSION['gv_loginstatus']);
    }
    printCenter("You must be logged on to proceed. Please enter your user name and password.");

    // create form and entries
    createForm("form1", "post", "validateuser.php");
        createTable(NULL, 0, 1);
            rowstart();
            openpar("right"); printf("User Name"); closepar();
            nextcell();
            printf("<input type=\"text\" name=\"username\"/>");
            nextrow();
            openpar("right"); printf("Password"); closepar();
            nextcell();
            printf("<input type=\"password\" name=\"password\"/>");
            rowend();
        closeTable();
        printCenter("<input type=\"submit\" name=\"submit\" value=\"Log In\"/>");
    closeForm();

    printCenter("If you are having difficulty logging in, please <a href=\"mailto:webmaster@yeoldpub.com\">email webmaster</a>");

    // Create closing HTML
    printCloseHTML();

?>