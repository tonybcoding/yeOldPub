<?php
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
/*
FUNCTION NOTES:
         - all functions assume openDB connection from caller, where required
         - all functions assume calling routing have established sessions
*/
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////



function dieIfNotProperLevel ($requiredlevel, $userlevel) {

    if (!userHasPermission($requiredlevel, $userlevel)) {
        printf("Your User Account Type does not authorize you to view this page.");
        die;
    }
} // end dieIfNotAdmin function

function dieIfNotValidSession () {
    if ($_SESSION['gv_sessionID'] != "nm59d7f8gs2") {
       printf("Your session has expired.  <a href = \"index.htm\">Please log in again.</a>");
       die;
    }
} // end dieIfNotValidSession



//////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////

function userHasPermission($requiredlevel, $userlevel) {
    // required level (from constants) and userlevel must be numeric
    $haspermission = false;
    if ($userlevel >= $requiredlevel) {
        $haspermission = true;
    }
    return $haspermission;
}
//        if ($_SESSION['gv_userTypeNum'] >= $options[$i][2]) {


function getUserTypeList() {

    // really need to get this list from SQL
    $userTypes = array ( array("Super User",      6),
                         array("Administrator",   5),
                         array("Moderator",       4),
                         array("Contributor",     3),
                         array("Member",          2),
                         array("Registered",      1),
                         array("Guest",           0));
    return $userTypes;
}

function getUserTypeLevel($type) {
     // really need to determine this from ENUM order number
     $userTypes = getUserTypeList();
     $userLevel = NULL;
     for ($i=0; $i<count($userTypes); $i++) {
         if ($type==$userTypes[$i][0]) {
             $userLevel = $userTypes[$i][1];
             $i = count($userTypes); // escape early if found
         }
     }
     return $userLevel;
}
//////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////


function javaRedirect ($path) {
    echo "<script type=\"text/javascript\">\n";
    echo "location.replace(\"".$path."\");\n";
    echo "</script>";
} // end of javaRedirect


function checkValidUserEmail($email){
    $valid = true;
    
    // Check if empty
    if($email=="") {
        $valid = false;
        $_SESSION['emailerror'] .= "Email must not be blank.<br/>";
    }

    return $valid;
} // end of checkValidUserEmail



function checkValidPassword($password, $verpassword){
    $valid = true;
    $passwordResponse = NULL;

    // Check if empty
    if((strlen($password)<6) || (strlen($password)>20)) {
        $valid = false;
        $_SESSION['passerror'] .= "Password must be between 6 and 20 characters<br/>";
    }
    
    // Ensure only alpha-numeric characters are used
    if(!ctype_alnum($password)) {
        $valid = false;
        $_SESSION['passerror'] .= "Password must only contain alpha-numeric characters.<br/>";
    }

    // if new and verify don't match, error
    if ($password != $verpassword) {
        $validpass = false;
        $_SESSION['passerror'] .= "Two entries did not match.<br/>";
    }
    return $valid;
} // end of checkValidPassword



function hashPassword($password) {

    $hashed = md5($password);
    return $hashed;
    
}


function checkAddUserName($user, &$nameResponse){
    $valid = true;
    
    // Check if length is correct
    if ((strlen($user) < 5) || (strlen($user) >20)) {
        $valid = false;
        $nameResponse .= "User ID must be between 5 and 20 characters.<br/>";
    }
    
    // Ensure only alpha-numeric characters are used
    if(!ctype_alnum($user)) {
        $valid = false;
        $nameResponse .= "User ID must only contain alpha-numeric characters.<br/>";
    }
    
    // if it passes, check for unique user ID
    if ($valid) {
        $query = sprintf("SELECT `name` FROM `users` WHERE `name`='%s'",
                          mysql_real_escape_string($user));
        $result = mysql_query($query);
        if (mysql_num_rows($result) > 0) {
            $valid = false;
            $nameResponse .= "User ID is already in use.<br/>";
        }
    }
    return $valid;
} // end of checkAddUserName


?>