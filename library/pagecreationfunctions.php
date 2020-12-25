<?php
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
/*
FUNCTION NOTES:
         - all functions assume openDB connection from caller, where required
         - all functions assume calling routine have established sessions
         - all functions assume calling routine have included other required
           functions, such as contsants.php
*/
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

function openPage($title, $css, $secondarycss, $class) {
    printf("<html>".LB);
    printf("<head>".LB);
    printf("<title>%s</title>".LB, $title);
    printf("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\"/>".LB);
	printf("<meta name=\"description\" content=\"Advice, Idea, and Story Sharing for Multiple Topics\" />".LB);
	printf("<meta name=\"keywords\" content=\"busy adult, professionals, insight, advice,legal,relationship,relationships,parenting,auto,automotive,home repair,decorating, science, math, academia, simple, simpler, current, relax, relaxing\" />".LB);
	printf("<meta name=\"author\" content=\"ye old Pub.com, LLC\" />".LB.LB);
    printf("<style type=\"text/css\" media=\"all\">@import \"css/".$css."\";</style>".LB);
    printf("<style type=\"text/css\" media=\"all\">@import \"css/".$secondarycss."\";</style>".LB);
    include('jsjumpmenuheader.html');
    printf("</head>".LB);
    printf("<body class = \"".$class."\">".LB.LB);
    openDiv(NULL, "pagecontainer", NULL);
        openDiv(NULL, "adsection", NULL);
            include("library/topsinglelinead.html");
        closeDiv("adsection");
}

function closePage($class) {
        openDiv(NULL, "footer", NULL);
            printHTML("Copyright © 2007, ye old Pub.com, LLC");
        closeDiv("footer");
    closeDiv("pagecontainer"); // end page container div
    printf(LB.LB."</body> <!-- close ".$class." BODY tag-->".LB);
    printf("</html>");
}

////////////////////////////////////////////////////////////////////////////////

function createMainNavSection() {
    openDiv(NULL, "mainnavigation", NULL);
        $links = NULL;
        $categories = getCategories();
        $index = 0;
        for ($i=0; $i<count($categories); $i++) {
            if(($categories[$i][6]=="Link") && ($categories[$i][3]!="Inactive")) {
                $links[$index][0] = $categories[$i][0];
                $links[$index][1] = $categories[$i][1];
                $index++;
            }
        }
        $str = "| ";

        // if this isn't called from main welcome, then add "home" link
        if ($_SESSION['loc'] != -1) {
            $str .= "<a href=\"welcometoyeoldpub.php\">Home</a> | ";
        }

        // if the user is a contributor or above, show the management link
        if (userHasPermission(CONTRIBUTOR, $_SESSION['gv_userTypeNum'])) {
            $str .= "<a href=\"welcometoyeoldpub.php?loc=-2\"><b>ezContent Manager</b></a> | ";
        }

        // create "Link" type category links
        for($i=0; $i<count($links); $i++) {
            $str .= "<a href=\"welcometoyeoldpub.php?loc=".$links[$i][0]."\">".$links[$i][1]."</a> | ";
        }

        // display appropriate end link based on if user is logged in or not
        if ($_SESSION['gv_loggedIn']) {
            $str .= "<a href=\"library/logout.php\">Log Out</a> | ";
        }
        else {
            $str .= "<a href=\"welcometoyeoldpub.php?loc=-3\"><b>Not a member? Join Today!</b></a> | ";
        }
        printf($str);
        
    closeDiv("mainnavigation");

}

////////////////////////////////////////////////////////////////////////////////

function createLeftColumnSection(){
    openDiv(NULL, "leftcolumn", NULL);
        openDiv("padding", "padding", NULL);
            // if viewing a "Table" instead of a link or a neg number (main, register, etc.) use textonly ad
            if ($_SESSION['loc'] > 0) {
                getCategoryInfo($_SESSION['loc'],$name, $url, $status, $type, $desc);
                ($type=="Table") ? $ad="library/leftvertadtextonly.html" : $ad="library/leftvertad.html";
            }
            else {
                $ad="library/leftvertad.html";
            }
            include($ad);
            printBreak(2);
        closeDiv("padding");
    closeDiv("leftcolumn");
}

////////////////////////////////////////////////////////////////////////////////

function createRightColumnSection() {
    openDiv(NULL, "rightcolumn", NULL);
        openDiv("jumpmenu", "jumpmenu", NULL);
            printf("<b>Please seat yourself!</b>");
            createTableJumpMenu("mainTableMenu");
        closeDiv("jumpmenu");
        openDiv("googlesearch", "googlesearch", NULL);
            include("library/googlesearch.html");
        closeDiv("googlesearch");
        openDiv("padding", "padding", NULL);
            //printCenter("<b>\"Top 10\" most active patrons</b>");
        closeDiv("padding");
    closeDiv("rightcolum");
}

////////////////////////////////////////////////////////////////////////////////

function createHeader($page) {
    openDiv(NULL, "header", NULL);
    // the header image is located in CSS file, so this div must be here to view it

        openDiv(NULL, "description", NULL);
            createTable("description", NULL, 0, 0);
                rowStart(NULL);
                if($_SESSION['loc'] < 0) {
                    printf("Welcome!  Pull up a chair to a table that interests you.  At ye old Pub.com you can feel at home on your terms.  Current events, insights and your opinions for busy adults.");
                }
                else {
                    $catinfo = getCategoryInfo($_SESSION['loc'],&$name, &$url, &$status, &$type, &$desc);
                    printf($desc);
                }
                rowEnd();
            closeTable("description");
        closeDiv(description);

        if (!isset($_SESSION['gv_userName'])) {
            openDiv(NULL, "login", NULL);
                createForm("login", "post", "library/validateuser.php");
                    createTable("Login", NULL,0,0);
                        if(isset($_SESSION['gv_loginstatus'])) {
                            rowStart(2);
                            printCenter($_SESSION['gv_loginstatus']);
                            rowEnd();
                            unset($_SESSION['gv_loginstatus']);
                        }
                        else {
                            rowStart(2);
                            printCenter("Please log in.");
                            rowEnd();
                        }
                        rowStart(NULL);
                        printf("User Name:");
                        nextCell();
                        textfield("text", "entrytext", "username",NULL,10);
                        rowEnd();
                        rowStart(NULL);
                        printf("Password:");
                        nextcell();
                        textfield("password","entrytext", "password",NULL,10);
                        rowEnd();
                        rowStart(2);
                        submitButton("center", "entrytextsubmit", "submit", "Log In");
                        rowEnd();
                    closeTable("Login");
                closeForm("login");
            closeDiv("login");
        }
        else {
            openDiv(NULL, "loginsuccess", NULL);
                createTable("LoginSuccess", NULL,0,0);
                    rowStart(NULL);
                    printCenter("<h3>Welcome back, ".$_SESSION['gv_userName']."!</h3>");
                    printCenter("Account type: <b>".$_SESSION['gv_userType']."</b>");
                    rowEnd();
                closeTable("LoginSuccess");
            closeDiv("loginsuccess");
        }
    closeDiv("header");
}

////////////////////////////////////////////////////////////////////////////////


?>