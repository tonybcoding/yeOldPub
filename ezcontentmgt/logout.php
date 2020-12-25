<?php
  session_start();
  require 'library/mySQLfunctions.php';
  require 'library/htmlfunctions.php';
  require 'library/constants.php';

  // Create opening HTML
  printOpenHTML("Log Out");

  // post event to user activity log
  openDB ($connect, $select);
  postUserActivity ($_SESSION['gv_userID'],'Log Out');
  closeDB($connect);
  
  // destroy session information
  session_destroy();
  
  // display log out information
  createTable(SMALLTBLSIZE,0,0);
      printBreak(1);
      printf("You are logged out.");
      printBreak(2);
      printf("<a href=\"login.php\">Click here to log back in</a>");
  closeTable();

  // Create closing HTML
  printCloseHTML();
?>
