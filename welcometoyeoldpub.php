<?php
    session_start();
    require 'library/constants.php';
    require 'library/htmlfunctions.php';
    require 'library/generalfunctions.php';
    require 'library/mySQLfunctions.php';
    require 'library/parsefunctions.php';
    require 'library/pagecreationfunctions.php';

////////////////////////////////////////////////////////////////////////////////
/*
     DB & Page initialization, determine location, Open appropriate Header,
     ensure session is valid
*/
////////////////////////////////////////////////////////////////////////////////

    // open DB connectoin
    openDB($connect, $selected);

    // Could be coming to this page from a table jump request or link click
    $_SESSION['loc'] = -1;
    if (isset($_GET['loc'])) $_SESSION['loc'] = $_GET['loc'];

    // get intended location user desires to view
    // don't retrieve category information if 'loc' is -1 (main) or -2 (ezContent Manager)
    if ($_SESSION['loc'] > -1) {
        $query = sprintf("SELECT `name`, `url`, `themeID` FROM `categories` WHERE `id` = '%u'",
                          mysql_real_escape_string($_SESSION['loc']));
        $result = mysql_query($query);
        if (!$result) die;
        $row = mysql_fetch_assoc($result);
        $locname = $row['name'];
        $url     = $row['url'];
        $themeid = $row['themeID'];
        mysql_free_result($result);
        $themearray = getTheme($themeid);
        $secondarycss = $themearray[1];
    }
    else {
        $secondarycss = "green.css";
    }

    // ensure valid session
    if (!isset($_SESSION['gv_loggedIn'])) {
        if($_SESSION['gv_loggedIn']) {
            dieIfNotValidSession();
        }
    }

    // DON'T CHANGE: open HTML, open page container, display top line ad
    switch($_SESSION['loc']) {
        case -1 :
            openPage("Welcome to ye old Pub.com!", "yeoldpub.css", $secondarycss, "welcome");
            createHeader("welcome");
        break;
        
        case -2 :
            openPage("ye old Pub.com - ezContent Manager", "yeoldpub.css", $secondarycss, "ezContentMgt");
            createHeader("ezContentMgt");
        break;

        case -3 :
            openPage("ye old Pub.com - Become a registered patron!", "yeoldpub.css", $secondarycss, "register");
            createHeader("register");
        break;

        default:
            openPage("ye old Pub.com - ".$locname, "yeoldpub.css", $secondarycss, $url);
            createHeader($url);
        break;
    }
    // DON'T CHANGE: open DB Conncetion, HTML, open page container, display top line ad





////////////////////////////////////////////////////////////////////////////////
/*
     Create main navigation (which is based on log in status and permissions
     and selectively show left column
*/
////////////////////////////////////////////////////////////////////////////////

    // DON'T CHANGE: create main navigation links and left column ad space
    createMainNavSection();
    if($_SESSION['loc'] <> -2) createLeftColumnSection();  // if not in ezContent Manager, add left column
    // DON'T CHANGE: create main navigation links and left column ad space





////////////////////////////////////////////////////////////////////////////////
/*
     Open appropriate main size/style and populate according to action identified
     in desired location indicator
*/
////////////////////////////////////////////////////////////////////////////////


    // Open main div/style/size based on 'loc'.  If ezContentMgt (-2), then use full screen
    if($_SESSION['loc'] <> -2) {
        openDiv(NULL, "main", NULL);
    }
    else {
        openDiv(NULL, "mainmgt", NULL);
    }
    openDiv("padding", "padding", NULL);
    
        // this is why it is essential to use negative numbers for exceptions.  Positive
        // numbers represent a valid table or link represented by "default"
        switch ($_SESSION['loc']) {
            case -1 :   // home / main
                break;

            case -2 :   // ezContent Management
                break;

            case -3 :   // register new user
                require ('registeruser.php');
                break;

            default :  // if a category table or link, the do this
                require ('tablelinkmaincontentincluded.php');
                break;
        } // end of switch statement



    // close main or mainmgt div based on 'loc'.  (-2) means ezContentMgt
    closeDiv("padding");
    if($_SESSION['loc'] <> -2) {
        closeDiv("main");
    }
    else {
        closeDiv("mainmgt");
    }





////////////////////////////////////////////////////////////////////////////////
/*
     Selectively show right column, close page and close DB connection
*/
////////////////////////////////////////////////////////////////////////////////

    // DON'T CHANGE: display footer, close page container, close HTML, close DB Connection
    if($_SESSION['loc'] <> -2) createRightColumnSection();  // if not in ezContent Manager, add right column
    closePage($class);
    closeDB($connect);
    // DON'T CHANGE: display footer, close page container, close HTML, close DB Connection

?>