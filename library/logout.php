<?php
  session_start();
  require 'mySQLfunctions.php';
  require 'generalfunctions.php';

  // post event to user activity log
  openDB ($connect, $select);
  postUserActivity ($_SESSION['gv_userID'],'Log Out');
  closeDB($connect);
  
  // destroy session information
  session_destroy();

  // back to main
  javaRedirect("../welcometoyeoldpub.php");
?>
