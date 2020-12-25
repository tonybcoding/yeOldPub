<?php


// Function: openDB
// Parameters: (1) dB connection string to open (by reference), (2) selected dB string (by reference)
//     sent by reference so that values may be used in calling code
// Author: Tony Burge, 12/10/2007
// Return: None
function openDB (&$connect, &$selected) {

    // Until I learn how to determine local vs. machine, we'll test for both
    $localusername = "root";
    $localpassword = "";
    $localhostname = "localhost";
    $serverusername = "admin";
    $serverpassword = "lovinit";
    $serverhostname = "mysql";

    // connect to mysql--try local first, then server second
    $connect = mysql_connect($localhostname, $localusername, $localpassword);
    if (!$connect) {
        $connect = mysql_connect($serverhostname, $serverusername, $serverpassword)
             or die("Unable to connect to MySQL" . mysql_error());
    }

    $selected = mysql_select_db("yeoldpub",$connect)
    	or die("Could not select yeoldpub");

} // end of openDB function


// Function: closeDB
// Parameters: (1) dB connection string to close
// Author: Tony Burge, 12/10/2007
// Return: None
function closeDB (&$connect) {

   mysql_close($connect);

}


// Function: postUserActivity
// Parameters: (1) dB id of user, (2) string text of action to post
// Author: Tony Burge, 12/10/2007
// Return: None
function postUserActivity ($id, $action){

   // assumes dB connection open
   $values = array ("NULL","\"".mysql_real_escape_string($id)."\"", CURRENT_TIMESTAMP, "\"".$action."\"");
   mySQLInsert("useractivity", $values);

}




// $id passed to get any user info
// 4 parameters passed by reference so that the updated values are recognized by calling procedure
function getUserInfo($id, &$name, &$type, &$email, &$status) {
    //assumes db open

    $query=sprintf("SELECT `name`, `type`, `email`, `status` FROM `users` WHERE `id`='%u'",
                    mysql_real_escape_string($id));
    $result = mysql_query($query);
    if(!$result) die;
    $row=mysql_fetch_assoc($result);
    mysql_free_result($result);
    $name   = $row['name'];
    $type   = $row['type'];
    $status = $row['status'];
    $email  = $row['email'];

} // end of getUserInfo function


function getCategoryInfo($id, &$name, &$url, &$status, &$type, &$desc) {
    //assumes db open

    $query=sprintf("SELECT * FROM categories WHERE id='%u'",
                    mysql_real_escape_string($id));
    $result = mysql_query($query);
    if(!$result) die;
    $row=mysql_fetch_assoc($result);
    mysql_free_result($result);
    $name   = $row['name'];
    $url    = $row['url'];
    $status = $row['status'];
    $type   = $row['type'];
    $desc   = $row['description'];

} // end of getCategoryInfo function




function mySQLInsert ($table, $values) {
    // assumes db connection open
    
    // Discover $table fields and populate array with them
    $fields = array ();
    $q = mysql_query("SHOW COLUMNS FROM $table");
    $i = 0;
    while($r=mysql_fetch_assoc($q)){
        $fields[$i]= $r['Field'];
        $i++;
    }

    if (count($fields) != count($values)) {
        printf("FATAL ERROR: Number of fields and values do not match.");
        die;
    }
    
    $fieldStr = " (";
    $valueStr = "VALUES (";
    for ($i = 0; ($i < count($fields)); $i++) {
        $fieldStr .= ("`".$fields[$i]."`");
        $valueStr .= ($values[$i]);
        if (($i+1) != count($fields)) {
            $fieldStr .= ", ";
            $valueStr .= ", ";
        }
    }
    $fieldStr .= ") ";
    $valueStr .= ") ";
    $query = ("INSERT INTO " . $table . $fieldStr . $valueStr);
    mysql_query($query) or die('Error, inserting query failed<br/>' . $query . "<br/>" . mysql_error());

} // end of mySQLInsert function





// Get category list
function getCategories() {
    // assumes db connectino is open
    $query = sprintf("SELECT * FROM `categories` ORDER BY `name`");
    $result = mysql_query($query);
    if (mysql_num_rows($result) != 0) {
        $categories = array();
        $i = 0;
        while ($row = mysql_fetch_assoc($result)) {
            $categories[$i][0] = $row['id'];
            $categories[$i][1] = $row['name'];
            $categories[$i][2] = $row['url'];
            $categories[$i][3] = $row['status'];
            $categories[$i][4] = $row['description'];
            $categories[$i][5] = $row['dateCreated'];
            $categories[$i][6] = $row['type'];
            $i++;
        }
    }
    mysql_free_result ($result);
    return $categories;
} // end of get categories function


function getContent($basedon, $id) {
    // assumes db connectino is open
    
    /*
    Design Logic
    what we want returned, is a list of content.  If all is selected then it doesn't matter
    if it is user or category that the call is based on.  The information returned would need to be the same
    We would want all content with a designation if no existing user is assigned or if it is not assigned to any category
    we also want a list of categories the content is assigned to
    If a specific userid or catid is specified, then we only want a subset of the above-mentioned information
    */
///
///  this function is ugly, but it works
///  i need to redo it...
///

    // get list of all usrs, which will help us determine if the content has an owner, which it should, but it may have been orphaned
    $users = getUsers();

    // run category query to build $concatarray with all entries from contenttocategory
    $catquery = sprintf("SELECT contentID, categoryID FROM contenttocategory");
    $result = mysql_query($catquery);
    $contcatarray = array();
    $i = 0;
    while ($row = mysql_fetch_assoc($result)) {
        $contcatarray[$i][0] = $row['contentID'];
        $contcatarray[$i][1] = $row['categoryID'];
        $i++;
    }
    mysql_free_result($result);

    // Create appropriate query string
    if(($id < 0) ||($basedon == "user")) {
        if ($id<0){
            $query = sprintf("SELECT * FROM content ORDER BY heading");
        }
        else{
            $query = sprintf("SELECT * FROM content WHERE userID='%u' ORDER BY heading",
                                     mysql_real_escape_string($id));
        }

        // run query
        $result = mysql_query($query);
        $content = array();
        $i = 0;
        while ($row = mysql_fetch_assoc($result)) {
            $content[$i][0] = $row['id'];
            // check if "orphaned" entry if ALL selected
            $orphaned = true;
            for($x=0; $x<count($users); $x++) {
                if ($users[$x][0] == $row['userID']) {
                    $orphaned = false;
                    $x = count($users); // escape early if found
                }
            }
            if ($orphaned) {
                $content[$i][1] = -1;
            }
            else {
                $content[$i][1] = $row['userID'];
            }
            $content[$i][2] = $row['dateEntered'];
            $content[$i][3] = $row['lastModified'];
            $content[$i][4] = $row['heading'];
            $content[$i][5] = $row['content'];
            $content[$i][6] = $row['column'];
            $content[$i][7] = $row['status'];
            $content[$i][8] = $row['sticky'];
            // determine which categories this content is assigned to
            $catstr = NULL; // using this to put in multiple entries in item 10
            for ($x=0; $x<count($contcatarray); $x++) {
                if($contcatarray[$x][0] == $content[$i][0]) {
                    $catstr .= ($contcatarray[$x][1].",");
                    $index++;
                }
            }
            $catstr = substr($catstr,0,strlen($catstr)-1); // remove the last comma
            $content[$i][9] = $catstr;
            // increment "while" index
            $i++;
        } // end of while
        mysql_free_result($result);
    }

    else {
        // only want comments that are part of the specified category
        // so we only want results from contenttocategory where categoryID = $id
        $catquery = sprintf("SELECT contentID FROM contenttocategory WHERE categoryID='%u'",
                                            mysql_real_escape_string($id));
        $result = mysql_query($catquery);
        $contarray = array();
        $i = 0;
        while ($row = mysql_fetch_assoc($result)) {
            $contarray[$i][0] = $row['contentID'];
            $contarray[$i][1] = $id;
            $i++;
        }
        mysql_free_result($result);

        // go through each contcatarray, run a query to retrive results, apply to $content array
        $content = NULL;
        for($i = 0; $i < count($contarray); $i++) {
            $query = sprintf("SELECT * FROM content WHERE id='%u' ORDER BY heading",
                                     mysql_real_escape_string($contarray[$i][0]));
            $result = mysql_query($query);
            $row = mysql_fetch_assoc($result);

            $content[$i][0] = $row['id'];
            // check if "orphaned" entry if ALL selected
            $orphaned = true;
            for($x=0; $x<count($users); $x++) {
                if ($users[$x][0] == $row['userID']) {
                    $orphaned = false;
                    $x = count($users); // escape early if found
                }
            }
            if ($orphaned) {
                $content[$i][1] = -1;
            }
            else {
                $content[$i][1] = $row['userID'];
            }
            $content[$i][2] = $row['dateEntered'];
            $content[$i][3] = $row['lastModified'];
            $content[$i][4] = $row['heading'];
            $content[$i][5] = $row['content'];
            $content[$i][6] = $row['column'];
            $content[$i][7] = $row['status'];
            $content[$i][8] = $row['sticky'];
            // determine which categories this content is assigned to
            $catstr = NULL; // using this to put in multiple entries in item 10
            for ($x=0; $x<count($contcatarray); $x++) {
                if($contcatarray[$x][0] == $content[$i][0]) {
                    $catstr .= ($contcatarray[$x][1].",");
                    $index++;
                }
            }
            $catstr = substr($catstr,0,strlen($catstr)-1); // remove the last comma
            $content[$i][9] = $catstr;
        }
    }
    return $content;
}



// Get content-specific assigned categories
function getContentAssignedtoCats($contentid) {
    // assumes db connectino is open

    // Get user-moderated categories
    $query = sprintf("SELECT categoryID FROM contenttocategory WHERE contentID = '%u'",
                             mysql_real_escape_string($contentid));
    $result = mysql_query($query);
    if (mysql_num_rows($result) != 0) {
        $catsassignedto = array();
        $i = 0;
        while ($row = mysql_fetch_assoc($result)) {
            $catsassignedto[$i] = $row['categoryID'];
            $i++;
        }
    }
    mysql_free_result ($result);
    return $catsassignedto;
}



// Get user-specific moderated categories
function getModerated($userid) {
    // assumes db connectino is open

    // Get user-moderated categories
    $query = sprintf("SELECT categoryID FROM moderatortocategory WHERE userID = '%u'",
                             mysql_real_escape_string($userid));
    $result = mysql_query($query);
    if (mysql_num_rows($result) != 0) {
        $modarray = array();
        $i = 0;
        while ($row = mysql_fetch_assoc($result)) {
            $modarray[$i] = $row['categoryID'];
            $i++;
        }
    }
    mysql_free_result ($result);
    return $modarray;
}



// Get category-specific moderating users
function getCatModerated($catid) {
    // assumes db connectino is open

    // Get user-moderated categories
    $query = sprintf("SELECT userID FROM moderatortocategory WHERE categoryID = '%u'",
                             mysql_real_escape_string($catid));
    $result = mysql_query($query);
    if (mysql_num_rows($result) != 0) {
        $modarray = array();
        $i = 0;
        while ($row = mysql_fetch_assoc($result)) {
            $modarray[$i] = $row['userID'];
            $i++;
        }
    }
    mysql_free_result ($result);
    return $modarray;
}




// Get list of users
function getUsers() {
    // assumes db connectino is open
    $query = sprintf("SELECT * FROM `users` ORDER BY `name`");
    $result = mysql_query($query);
    if (mysql_num_rows($result) != 0) {
        $users = array();
        $i = 0;
        while ($row = mysql_fetch_assoc($result)) {
            $users[$i][0] = $row['id'];
            $users[$i][1] = $row['name'];
            $users[$i][2] = $row['type'];
            $users[$i][3] = $row['status'];
            $users[$i][4] = $row['email'];
            $users[$i][5] = $row['dateAdded'];
            $i++;
        }
    }
    mysql_free_result ($result);
    return $users;
} // end of get list of users function




function getModeratorsandAbove() {
    // "Active" Moderators List is returned
    $query = sprintf("SELECT * FROM `users` ORDER BY `name`");
    $result = mysql_query($query);
    if (mysql_num_rows($result) != 0) {
        $users = array();
        $i = 0;
        while ($row = mysql_fetch_assoc($result)) {
            if ((getUserTypeLevel($row['type']) >= MODERATOR) && ($row['status'] != "Disabled")) {
                $users[$i][0] = $row['id'];
                $users[$i][1] = $row['name'];
                $users[$i][2] = $row['type'];
                $users[$i][3] = $row['status'];
                $users[$i][4] = $row['email'];
                $users[$i][5] = $row['dateAdded'];
            $i++;
            }
        }
    }
    mysql_free_result ($result);
    return $users;
}




?>
