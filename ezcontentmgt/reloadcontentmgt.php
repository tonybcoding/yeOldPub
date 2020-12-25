<?php
// this file ONLY exists, because I can't get javascript jump menu to work correctly,
// so I need this to reload the contenttmgt.php page
    require 'library/generalfunctions.php';

    $loc = $_GET["caller"];
    switch ($loc) {
        case "user":
            $id = $_POST['viewuser'];
            $changed = "user";
            break;
        case "cat":
            $id = $_POST['viewcategory'];
            $changed = "cat";
            break;
    }

    javaRedirect("contentmgt.php?from=same&change=".$changed."&id=".$id);

?>
