<?php
    session_start();
    require 'library/constants.php';
    require 'library/htmlfunctions.php';
    require 'library/mySQLfunctions.php';
    require 'library/generalfunctions.php';
    require 'library/parsefunctions.php';

    // print opening HTML
    printOpenHTML("Preview Content");
    
    // open a new DB session
    openDB($connect, $selected);

    // If not valid session, exit.
    dieIfNotValidSession();
    
    // get URL variables
    $id     = $_GET['id'];              // -1 if new add
    $action = $_GET['action'];          // modify or add
    $priv   = $_GET['priv'];            // only needed to return to addmodcontent.php
    
    // get post data
    $heading            = $_POST['heading'];
    $content            = $_POST['content'];
    $column             = $_POST['position'];
    $status             = $_POST['status'];
    $sticky             = $_POST['sticky'];
    $selectedcategories = $_POST['categories'];  // array
    $categories         = getCategories();       // get list to compare $selectedcategories with
    
    // and set session variables for returning to addmodifycontent.php
    $_SESSION['contid']              = $id;
    $_SESSION['heading']             = $heading;
    $_SESSION['content']             = $content;
    $_SESSION['column']              = $column;
    $_SESSION['status']              = $status;
    $_SESSION['sticky']              = $sticky;
    $_SESSION['selectedcategories']  = $selectedcategories;

    //display information
    // need to know if this is modifying existing record or adding a new
    
    createTable(MAINTBLSIZE,0,0);
        rowstart();
        createTable(SUBTBLSIZE,1,10);
            rowstart();
            printf("<h3>".stripslashes($heading)."</h3>");
            nextrow();
            // ensure even number of formatting characters (except link start)
            $parsestring = fullparse(stripslashes($content));
            if (isset($_SESSION['parsemessage'])) {
                printf("<h4>".($_SESSION['parsemessage'])."</h4>");
                unset($_SESSION['parsemessage']);
            }
            printf($parsestring);
            printBreak(2);
            rowend();
        closeTable();
        nextrow();
        printf("<hr>");
        rowend();
    closeTable();
    
    printCenter("<h3>This content will have the following settings applied:</h3>");
    printBreak(1);

    createTable(SUBTBLSIZE,0,0);
        rowstart();
        printCenter("Position: <b>".$column."</b>");
        nextcell();
        printCenter("Status: <b>".$status."</b>");
        if ($priv == "admin") {
            nextcell();
            printCenter("Sticky: <b>".$sticky."</b>");
        }
        rowend();
    closeTable();

    if ($priv == "admin") {
        createTable(MAINTBLSIZE,0,0);
            rowstart();
            printf("<hr>");
            nextrow();
                if($selectedcategories != NULL) {
                    printCenter("<h3>This content will be assigned to the following categories:</h3>");
                    for($i = 0; $i < count($selectedcategories); $i++) {
                        for($x = 0; $x < count($categories); $x++) {
                            if($selectedcategories[$i] == $categories[$x][0]) {
                                printCenter($categories[$x][1]);
                                $x = count($categories); // escape early
                            }
                        }
                    }
                }
                else {
                    printCenter("<h3>No categories where selected.</h3>");
                }
            nextrow();
            printf("<hr>");
        closeTable();
    }
    createTable(EXSMALLTBLSIZE,0,0);
        rowstart();
        createForm("submitform", "post", "submitcontent.php?action=".$action);
        submitbutton("center", "submit", "Submit");
        closeForm();
        nextcell();
        createForm("modify", "post", "addmodcontent.php?from=preview&priv=".$priv."&action=".$action."&id=".$id);
        submitbutton("center", "backtomod", "Return to Modify");
        closeForm();
        rowend();
    closeTable();


    // close HTML statements and DB session
    printCloseHTML();
    closeDB($connect);

?>
