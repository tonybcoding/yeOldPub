<?php
    session_start();
    require 'library/constants.php';
    require 'library/htmlfunctions.php';
    require 'library/generalfunctions.php';
    require 'library/mySQLfunctions.php';

    // Create opening HTML
    printOpenHTML("Main");

    // check session status
    dieIfNotValidSession();

    // open DB session
    openDB($connect, $selected);

    // Menu items based on user type
    $options = array(
       array ('Content Management', 'contentmgt.php', CONTRIBUTOR),
       array ('Category Management', 'categorymgt.php', MODERATOR),
       array ('User Management', 'usermgt.php', ADMINISTRATOR),
       array ('Site Builder', 'sitebuilder.php', SUPERUSER),
       array ('Log out', 'logout.php', GUEST)
    );
    $optionStr = "| ";
    for ($i = 0; ($i < count($options)); $i++) {
        if (userHasPermission($options[$i][2], $_SESSION['gv_userTypeNum'])) {
            $optionStr .= ("<a href=\"".$options[$i][1]."\">".$options[$i][0]."</a> | ");
       }
    }
    printCenter($optionStr);
    printBreak(1);
    
    createTable(SMALLTBLSIZE,0,0);
        printf("Welcome, <b>%s</b>.", $_SESSION['gv_user']);
        printBreak(1);
        printf("Your account type is set to <b>%s</b>", $_SESSION['gv_userType']);
        printBreak(1);
        printf("Your registered email address is <b>%s</b>", $_SESSION['gv_userEmail']);
        printBreak(2);

        // show categories user is moderator of
        $categories = getCategories();
        $moderated = getModerated($_SESSION['gv_userID']);
        if (count($moderated) > 0) {
            printCenter("You are currently assigned as moderator of the following categories:");
            for($i=0; $i<count($categories); $i++) {
                for($x=0; $x<count($moderated); $x++) {
                    if ($moderated[$x] == $categories[$i][0]) {  // if id in catID Moderated = id in List
                        printCenter("<b>" . $categories[$i][1] . "</b>");
                        $x = count($moderated); // escape early if found
                    }
                }
            }
        }
        else {
            printCenter("You are not currently assigned as moderator of any category.");
        }
        printBreak(1);
        if (!userHasPermission(ADMINISTRATOR, $_SESSION['gv_userTypeNum'])) {
            printCenter("If you would like to change your moderator settings, <a href=\"mailto:mic@yeoldpub.com\">please email me.</a>");
        }
        
    closeTable();
    printBreak(1);

    createTable(MAINTBLSIZE, 0, 0);
        printf("<hr>");
    closeTable();
    printBreak(1);
    
    createTable(SUBTBLSIZE,1,5);
        rowstart();
        createForm("form", "post", "applyuserchanges.php?id=mainpass");
            printCenter("Change Password");
            if (isset($_SESSION['passerror'])) {
                printCenter("<h4>".$_SESSION['passerror']."</h4>");
                unset($_SESSION['passerror']);
            }
            if (isset($_SESSION['passsuccess'])) {
                printCenter("<b>Password successfully changed</b>");
                unset($_SESSION['passsuccess']);
            }
            createTable(250,0,20);
                printBreak(1);
                openpar("right");
                    printf("New Password ");
                    textfield("password", "new", "", 20);
                    printBreak(1);
                    printf("Verify Password ");
                    textfield("password", "verify", "", 20);
                closepar();
                submitbutton("center", "submit", "Apply");
            closeTable();
        closeForm();
        nextcell();
        createForm("form2", "post", "applyuserchanges.php?id=mainemail");
            printCenter("Change Email Address");

            if (isset($_SESSION['emailerror'])) {
                printCenter("<h4>".$_SESSION['emailerror']."</h4>");
                unset($_SESSION['emailerror']);
            }
            if (isset($_SESSION['emailsuccess'])) {
                printCenter("<b>Email successfully changed</b>");
                unset($_SESSION['emailsuccess']);
            }
            openpar("center"); textfield("text", "newemail",$email, 30); closepar();
            submitbutton("center", "submit", "Apply");

        closeForm();
        rowend();
    closeTable();

    // close DB session
    closeDB($connect);

    // Create closing HTML
    printCloseHTML();
 ?>
