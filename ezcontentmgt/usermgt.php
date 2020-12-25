<?php
    session_start();
    require 'library/constants.php';
    require 'library/htmlfunctions.php';
    require 'library/mySQLfunctions.php';
    require 'library/generalfunctions.php';

    // print opening HTML
    printOpenHTML("User Management");

    // If user is not proper level, do not let them proceed
    dieIfNotProperLevel(ADMINISTRATOR, $_SESSION['gv_userTypeNum']);

    // sub menu
    printCenter("Select one of the following to go to that section of this page, or log out");
    printBreak(1);
    $menuoptions = array( array ("mainmenu.php", "Back to Main"),
                          array ("logout.php","Log Out"));
    menuLinks($menuoptions);
    printBreak(1);
    $menuoptions = array( array("#view","View/Modify Users (this page)"),
                          array ("#add","Add User (this page)"));
    menuLinks($menuoptions);

    printBreak(1);

    ///////////////////////
    // view/modify users //
    ///////////////////////
    printf("<hr>");
    printf("<a name=\"view\"></a>");
    printBreak(1);
    printCenter("<h3>View/Modify Users</h3>");
    printBreak(1);
    $menuoptions = array( array("usermgt.php?showusers=all","All"),
                          array ("usermgt.php","Active (default)"),
                          array ("usermgt.php?showusers=inactive", "Disabled"));
    menuLinks($menuoptions);
    printBreak(1);
    printCenter("To modify a user, click on the UserID.  To email the user, click on the email address.");

    // Attempt to get URL varialbes to determine what "type" of users to display
    // determine whether to show all users, active users, or inactive users - default is "active"
    if (isset($_GET["showusers"])) {
      $_SESSION['gv_showusers'] = $_GET["showusers"];
    }
    else {
      // if you remove this variable, then you must check for isset before performing the switch below
      $_SESSION['gv_showusers'] = "active";   // if no variables on the URL, then set to active
    }

    $query=("SELECT * FROM `users` WHERE `status`='Active' ORDER BY `name`");
    switch ($_SESSION['gv_showusers']) {
      case "all":
         $query=("SELECT * FROM `users` ORDER BY `name`");
         break;
      case "inactive":
         $query=("SELECT * FROM `users` WHERE `status`='Disabled' ORDER BY `name`");
         break;
    }

    // Draw table with user contents
    createTable(SUBTBLSIZE,1,5);
        rowstart();
        printCenter("<b>User ID</b>");
        nextcell();
        printCenter("<b>Account Type</b>");
        nextcell();
        printCenter("<b>Account Status</b>");
        nextcell();
        printCenter("<b>Email</b>");
        nextcell();
        printCenter("<b>Date Added</b>");
        rowend();

        openDB($connect, $select);
        $result = mysql_query($query);
        if(!$result) die("Query Failed.");
        while($row = mysql_fetch_assoc($result)) {
          // Build User Table
          $id         = $row['id'];
          $username   = $row['name'];
          $email      = $row['email'];
          $dateAdded  = $row['dateAdded'];
          $userStatus = $row['status'];
          $userType   = $row['type'];

          printf(RS.
                 "<a href=\"modifyuser.php?id=".$id."\">".$username."</a>"
                 .NC. $userType.NC.$userStatus.NC.
                 "<a href=\"mailto:".$email."\">".$email."</a>"
                 .NC.$dateAdded.RE);
        }
    closeTable();

    // free resources
    mysql_free_result ($result);
    closeDB($connect);

    //////////////
    // Add user //
    //////////////
    createTable(MAINTBLSIZE,0,0);
        printBreak(2);
        printf("<hr>");
        printf("<a name=\"add\"></a>");
        printBreak(1);
        printCenter("<h3>Add User</h3>");

        // is this variable is set, then an add has been attempted
        if (isset($_SESSION['gv_addSuccess'])) {
            // if this is also set, then it was a failure
            if (isset($_SESSION['gv_failureReason'])) {
              printCenter("<h4>Entry Error. " . $_SESSION['gv_failureReason'] . "</h4>",0);
              unset($_SESSION['gv_failureReason']);
            }
            // it was a success
            else {
              printCenter("<b>Add successfull.</b>");
            }
            unset($_SESSION['gv_addSuccess']);
        }
    closeTable();

    createForm("form1", "post", "adduser.php");
        createTable(SUBTBLSIZE,0,5);
            // text fields
            rowstart();
            printCenter("User Name: <input type=\"text\" name=\"name\">");
            nextcell();
            printCenter("Email: <input type=\"text\" name=\"email\">");
            rowend();
        closeTable();

        createTable(EXSMALLTBLSIZE,0,5);
            // User type drop down list
            rowstart();
            createUserTypeDropDown("type", "Account Type", "Contributor");

            // User status drop down list
            nextcell();
            createUserStatusDropDown("status", "User Status", NULL);

            rowend();
        closeTable();

        // submit button
        submitbutton("center","submit","Add User");
        printCenter("(Note, password will be set to default until user changes it)");
    closeForm();
    printCloseHTML();
    
?>
