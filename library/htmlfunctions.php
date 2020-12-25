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

///////////////////////////////////////////////////////////////////////////////
/*               Beginning of table functions                                */
///////////////////////////////////////////////////////////////////////////////
function createTable ($name, $width, $bw, $cp){
    printf(LB.LB."<!-- Begin of table \"".$name."\" -->".LB);
    printf("<table width=\"".$width."\" border=\"".$bw."\" cellpadding = \"".$cp."\">".LB);
}
        // table "sub" functions
        function rowstart($colspan) {
            printf("<tr>".LB);
            if ($colspan == NULL) {printf("<td>".LB);}
            else {printf("<td colspan=\"".$colspan."\">".LB);}
        }
        function nextcell() {
            printf("</td>".LB);
            printf("<td>".LB);
        }
        function rowend() {
            printf("</td>".LB);
            printf("</tr>".LB);
        }
        // end of table "sub" functions
function closeTable ($name){
    printf(LB."</table>".LB);
    printf("<!-- End of table \"".$name."\" -->".LB.LB);
}
///////////////////////////////////////////////////////////////////////////////
/*               End of table functions                                      */
///////////////////////////////////////////////////////////////////////////////



///////////////////////////////////////////////////////////////////////////////
/*               Beginning of Div and Form functions                         */
///////////////////////////////////////////////////////////////////////////////
function openDiv ($class, $id, $title) {
    printf(LB.LB."<!-- Begin Div \"".$id."\" -->".LB);
    printf("<div id=\"".$id."\" class=\"".$class."\" title=\"".$title."\">".LB);
}
function closeDiv($id) {
    printf(LB."</div>".LB);
    printf("<!-- End Div \"".$id."\" -->".LB);
}

function createForm ($name, $method, $action) {
    printf(LB.LB."<!-- Begin Form \"".$name."\" -->".LB);
    printf("<form name=\"".$name."\" method=\"".$method."\" action=\"".$action."\">".LB);
}
function closeForm ($name){
    printf(LB."</form>".LB);
    printf("<!-- End of Form \"".$name."\" -->".LB.LB);
}
///////////////////////////////////////////////////////////////////////////////
/*               End of Div and Form functions                               */
///////////////////////////////////////////////////////////////////////////////



///////////////////////////////////////////////////////////////////////////////
/*               Beginning of Form Field functions                           */
///////////////////////////////////////////////////////////////////////////////
function submitbutton($align, $class, $name, $text) {
    printf("<p align=\"".$align."\"><input class=\"".$class."\" type=\"submit\" name=\"".$name."\" value=\"".$text."\"></p>".LB);
}

function textfield ($type, $class, $name, $value, $size) {
    printf("<input type=\"".$type."\" class=\"".$class."\" name=\"".$name."\" value=\"".$value."\" size=\"".$size."\">".LB);
}
///////////////////////////////////////////////////////////////////////////////
/*               End of Form Field functions                                 */
///////////////////////////////////////////////////////////////////////////////





///////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////
// UNCLEARED FUNCTIONS
///////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////


function printHTML($text) {
    printf("\t\t".$text.LB);
}


function printCenter ($string) {

    printf("<center>" . $string . "</center>".LB);

}

function printBreak ($num) {

    for ($i = 1; ($i<=$num); $i++) {
        printf("<br/>");
    }
}


function openpar($align) {
    printf("<p align=\"".$align."\">");
}

function closepar() {
    printf("</p>");
}





function menuLinks($options){
    $str = "| ";
    for($i=0; $i<count($options); $i++) {
        $str .= "<a href=\"".$options[$i][0]."\">".$options[$i][1]."</a> | ";
    }
    printCenter($str);
} // end function menuoptions


function createDropDownMenu ($menuname, $text, $items) {

    printf($text . " <select name = \"". $menuname . "\">");
    for ($i=0; $i < count($items); $i++) {
        $selectext = NULL;
        if($items[$i][0] == $items[$i][2]) $selectext = "selected";
        printf("<option value = \"" . $items[$i][0] . "\" ".$selectext.">" . $items[$i][1] . "</option>");
    }
    printf("</select>");
} // end of createDropDownNew function

function createUserTypeDropDown ($name, $text, $selected) {
    if ($selected == NULL) $selected = "Guest";
    $typelist = getUserTypeList();
    $items = NULL;
    for($i=0; $i<count($typelist); $i++) {
        $items[$i][0] = $typelist[$i][0];
        $items[$i][1] = $typelist[$i][0];
        $items[$i][2] = $selected;
    }
    createDropDownMenu($name, $text, $items);

}

function createUserStatusDropDown ($name, $text, $selected) {
    if($selected == NULL) $selected = "Active";
    $items = array (array ("Active", "Active", $selected),
                    array ("Disabled", "Disabled", $selected));
    createDropDownMenu($name, $text, $items);
}

function createCatStatusDropDown ($name, $text, $selected) {
    if($selected == NULL) $selected = "Active";
    $items = array (array ("Active", "Active", $selected),
                    array ("Inactive", "Inactive", $selected),
                    array ("Locked", "Locked", $selected));
    createDropDownMenu($name, $text, $items);
}

function createCatTypeDropDown ($name, $text, $selected) {

    if($selected == NULL) $selected = "TABLE";
    $items = array (array ("Table", "Table", $selected),
                    array ("Link", "Link", $selected));
    createDropDownMenu($name, $text, $items);
}



function createRadioList ($name, $align, $heading, $vertical, $list) {
    printf("<p align=\"" . $align . "\">" . $heading . "<br/>");
    for ($i=0; $i < count($list); $i++) {
        $checked = "";
        if ($list[$i][2]) $checked = "checked";
        printf("<input type=\"radio\" name=\"".$name."\" value=\"".$list[$i][0]."\"".$checked.">".$list[$i][1]);
        if (($vertical) && ($i+1 < count($list))) printf("<br/>");
    }
    printf("</p>");

} // end of createRadioList



function createCheckBoxes($name, $list, $checked, $orientation){
    for ($i=0; $i<count($list); $i++){
        ($checked[$i]) ? $checkstatus = "checked" : $checkstatus = "";
        printf("<input type=\"checkbox\" name=\"".$name."\" value=\"".$list[$i][0]."\" ".$checkstatus. "/>".$list[$i][1]);
        if($orientation) {
            printBreak(1);
        }
        else {
            if (($i+1) < count($list)) printf(" | ");
        }
    }
} // end of createCheckBox function



function createSelectMenu($name, $maxsize, $items){

    if (count($items)<$maxsize) $maxsize = count($items);
    printf("<select name=\"".$name."\"size=\"".$maxsize."\" multiple>");

    for ($i=0; $i<count($items); $i++){
        printf("<option value=\"".$items[$i][0]."\">" . $items[$i][1] . "</option>");
    }
    printf("</select>");

} // end of createSelectMenu function






?>