<?

// NOT A STAND-ALONE SCRIPT
// included from welcometoyeoldpub.com

    printf("<h1>User Registration</h1>");
    printBreak(1);

    // do session check on whether user has already attempted an add with
    // either a success or failure
    $attempted = false;
    if (isset($_SESSION['gv_addSuccess'])) {
        $attempted = true;
        $success = $_SESSION['gv_addSuccess'];
        unset ($_SESSION['gv_addSuccess']);
    }
    if (isset($_SESSION['gv_failureReason'])) {
        $failurereason = $_SESSION['gv_failureReason'];
        unset ($_SESSION['gv_failureReason']);
    }


    //find top entry of each link or table category
    openDiv("entry", "entry", NULL);
        if (!$attempted || ($attempted && !$success)) {
            $str = "Signing up is easy and rewarding!  As a registered user, you will ";
            $str .= "be able to comment on existing entries and be entered for various ";
            $str .= "drawings that we plan to hold periodically in recognition of your ";
            $str .= "\"patronage.\"  We also plan to have live events that you may ";
            $str .= "choose to be notified of.\r\n\r\n";
            $str .= "Once registered, you have the opportunity, based on activity and ";
            $str .= "other merits to [b]become a full member, contributor, and even a ";
            $str .= "waiter/waitress (okay, technically a Table moderator).[b]\r\n\r\n";
            $str .= "As always, if you have any questions, [e]drop me a quick email.[l]mic@yeoldpub.com[e]";
            printf(fullParse($str));

            createForm("registeruser", "post", "library/registernewuser.php");
                openDiv("register", "register", NULL);
                printCenter("<h4>".$failurereason."</h4>");
                    createTable("registertable", NULL, 0, 0);
                        rowStart(NULL);
                        printf("Desired User Name:");
                        nextCell();
                        textfield("text", "entrytext", "name",NULL,15);
                        rowEnd();
                        rowStart(NULL);
                        printf("Valid Email Address:");
                        nextCell();
                        textfield("text", "entrytext", "email",NULL,15);
                        rowEnd();
                        rowStart(NULL);
                        printf("Password:");
                        nextcell();
                        textfield("password","entrytext", "password",NULL,15);
                        rowEnd();
                        rowStart(NULL);
                        printf("Verify Password:");
                        nextcell();
                        textfield("password","entrytext", "verifypassword",NULL,15);
                        rowEnd();
                        rowStart(2);
                        printf("<input type=\"checkbox\" name=\"optinfornotification\" value=\"yes\" checked/>Please notify me of ye old Pub.com events and changes.");
                        rowEnd();
                        rowStart(2);
                        submitButton("center", "entrytextsubmit", "submit", "Register");
                        rowEnd();
                    closeTable("registertable");
                closeDiv("register");
            closeForm("registeruser");
        } // either not attempted or attempted and failed

        else { // successful add
            printCenter("You have successfully been registered!");
            printBreak(1);
            printCenter("<b>You must now log in using the log in form at the top right of this site.</b>");
            printBreak(1);
            printCenter("Visit often and don't forget to tell your friends about ye old Pub.com!");
            $_SESSION['loc'] = -1; // send user back to main after he/she logs in
        }
    
    closeDiv("entry");
    
    // not a standalone script...must return to routine that called it...
?>