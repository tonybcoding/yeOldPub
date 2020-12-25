<?php
    session_start();
    require 'library/constants.php';
    require 'library/htmlfunctions.php';
    require 'library/mySQLfunctions.php';
    require 'library/generalfunctions.php';

    // print opening HTML
    printOpenHTML("Site Builder--Preview Site");

    // If user is not proper level, do not let them proceed
    dieIfNotProperLevel(SUPERUSER, $_SESSION['gv_userTypeNum']);
    
    $categories = $_POST['selected'];
    printCenter(count($selected));

    // openDB connection
    openDB($connect, $selected);





    // closeDB connection
    closeDB($connect);

    // close HTML statements
    printCloseHTML();

?>
