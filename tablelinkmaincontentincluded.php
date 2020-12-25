<?

// NOT A STAND-ALONE SCRIPT
// included from welcometoyeoldpub.com

    printf("<h1>".$locname."</h1>");
    printBreak(1);

    //find top entry of each link or table category
    $cats = getCategories();
    for($i=0; $i<count($cats); $i++){
        if($cats[$i][0] == $_SESSION['loc']) {
            $contIDs = getContentAssignedtoCategory($cats[$i][0]);
            for ($x=0; $x<count($contIDs); $x++) {
                $query = sprintf("SELECT * FROM `content` WHERE `id`='%u'",
                                         mysql_real_escape_string($contIDs[$x]));
                $result = mysql_query($query) or die;
                $row = mysql_fetch_assoc($result);
                getUserInfo($row['userID'],$name, $type, $email, $status);
                $date = $row['dateEntered'];
                openDiv("entry", "entry", NULL);
                printHTML("<h3>".$row['heading']."</h3>");
                printHTML("<i>Posted by <b>".$name."</b> on <b>".$date."</b></i>");
                printBreak(2);
                $content = fullparse($row['content']);
                printHTML($content);
                closeDiv("entry");
                printBreak(1);
                mysql_free_result($result);
            }
        }
    }
?>