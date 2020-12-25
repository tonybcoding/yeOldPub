<?php
    session_start();
    require 'library/constants.php';
    require 'library/htmlfunctions.php';
    require 'library/mySQLfunctions.php';
    require 'library/generalfunctions.php';

    // print opening HTML
    printOpenHTML("Site Builder--Building Site");

    // If user is not proper level, do not let them proceed
    dieIfNotProperLevel(SUPERUSER, $_SESSION['gv_userTypeNum']);

    // openDB connection
    openDB($connect, $selected);

    // copy library and css files from main into our temp directory
    printf("Creating temporary directory...");


    //$maindir = "_tempyeoldpub".time();
    $maindir = "_tempyeoldpub"
    
    
    mkdir($maindir);
    chdir($maindir);
    printf("success");
    printBreak(1);
    printf("Creating library, css, and images directories...");
    $libdirname    = "_library";
    $cssdirname    = "_css";
    $imagesdirname = "_images";
    mkdir($libdirname);
    mkdir($cssdirname);
    mkdir($imagesdirname);
    printf("success");
    printBreak(1);
    printf("Copying library files from main into this structure...");
    chdir("../library");
    $libpath = "../".$maindir."/".$libdirname."/";
    $handle = opendir("../library");
    while (false !== ($file = readdir($handle))) {
        if($file != '.' && $file != '..') {
           copy($file, "$libpath/$file");
        }
    }
    printf("success");
    printBreak(1);
    printf("Copying css files from main into this structure...");
    chdir("../css");
    $libpath = "../".$maindir."/".$cssdirname."/";
    $handle = opendir("../css");
    while (false !== ($file = readdir($handle))) {
        if($file != '.' && $file != '..') {
           copy($file, "$libpath/$file");
        }
    }
    chdir("../".$maindir);  // return to our temp directory root
    printf("success");
    printBreak(1);

    // create a directory for each category based on its URL
    printf("Creating directories for each category...");
    $categories = getCategories();
    for($i=0; $i<count($categories); $i++) {
        mkdir($categories[$i][2]);
    }
    printf("success");
    printBreak(1);

    // build initial index.html to kick off main page
    printf("Creating index file...");
    $handle = fopen("index.htm", "w+");
    fwrite($handle, "<html>\r\n");
    fwrite($handle, "<head>\r\n");
    fwrite($handle, "<script language=\"JavaScript\">\r\n");
    fwrite($handle, "<!--\r\n");
    fwrite($handle, "var time = null\r\n");
    fwrite($handle, "function move() {\r\n");
    fwrite($handle, "window.location = 'welcome.php'\r\n");
    fwrite($handle, "}\r\n");
    fwrite($handle, "//-->\r\n");
    fwrite($handle, "</script>\r\n");
    fwrite($handle, "</head>\r\n");
    fwrite($handle, "<body onload=\"timer=setTimeout('move()',1)\">\r\n");
    fwrite($handle, "</body>\r\n");
    fclose($handle);
    printf("success");
    printBreak(1);

    // create welcome file with appropriate links and tables







    // closeDB connection
    closeDB($connect);

    // close HTML statements
    printCloseHTML();

?>
