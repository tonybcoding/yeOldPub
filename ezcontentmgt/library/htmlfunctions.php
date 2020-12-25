<?php

require 'constants.php';

// Function: printOpenHTML
// Parameters: (1) test for specific page
// Author: Tony Burge, 12/10/2007
// Return: None
function printOpenHTML ($specific) {

    // build a nice screen
    printf("<html>");
    printf("<head>");
    printf("<title>ye old Pub.com - ezContentMgt - %s</title>", $specific);
    printf("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\"/>");
    printf("<style type=\"text/css\" media=\"all\">@import \"css/master.css\";</style>");
    printf("</head>");
    printf("<body>");
    printf("<p align=\"center\">");
    printf("<img src=\"../images/general/logotextonly.gif\" width=\"212\" height=\"44\">");
    printf("<h1><center>ezContentMgt Utility</center></h1>");
    printf("<div align=\"center\">");
    printf("<table width=\"".MAINTBLSIZE."\" border=\"0\">");
    printf("<br/><center><h2>%s</h2></center></br>", $specific);
    printf("<hr>");

} // end of printOpenHTML function


// Function: printCloseHTML
// Parameters: None.
// Author: Tony Burge, 12/10/2007
// Return: None
function printCloseHTML () {

    printf("</table>");
    printf("</div>");
    printf("</body>");
    printf("</html>");

} // end of printCloseHTML function


// Function: printCenter
// Description: prints $string in HTML <center> format with appended <br/>s as indicated by $numbreaks
// Parameters: string to center
// Calls: printBreak (htmlfunctions.php)
// Author: Tony Burge, 12/10/2007
// Return: None
function printCenter ($string) {

    printf("<center>" . $string . "</center>");

} // end of printCener function


// Function: printBreak
// Description: num of <br/>s as specified by ($num)
// Parameters: (1) string to center
// Author: Tony Burge, 12/10/2007
// Return: None
function printBreak ($num) {

    for ($i = 1; ($i<=$num); $i++) {
        printf("<br/>");
    }
}


// Function: createForm
// Description: creates HTML form
// Parameters: (1) name, (2) method, (3) action
// Author: Tony Burge, 12/10/2007
// Return: None
function createForm ($name, $method, $action) {

    printf("<form name=\"".$name."\" method=\"".$method."\" action=\"".$action."\">");

} // end of createForm function


// Function: closeForm
// Description: prints </form> html tag
// Parameters: none
// Author: Tony Burge, 12/10/2007
// Return: None
function closeForm (){

    printf("</form>");

} // end of closeForm function


// Function: createTable
// Description: creats <TABLE> element
// Parameters: (1) table width, (2) border width, (3) cell padding
// Author: Tony Burge, 12/10/2007
// Return: None
function createTable ($width, $bw, $cp){

    printf("<div align=\"center\">");
    printf("<table width=\"".$width."\" border=\"".$bw."\" cellpadding = \"".$cp."\">");

} // end of createTable function

// Function: closeTable
// Description: closes <TABLE> element
// Parameters: None
// Author: Tony Burge, 12/10/2007
// Return: None
function closeTable (){

    printf("</table></div>");

} // end of closeTable function

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



// table row functions

function rowstart() {
    printf(RS);
}

function nextcell() {
    printf(NC);
}

function rowend() {
    printf(RE);
}

function nextrow() {
    rowend();
    rowstart();
}

// submit button fuction
function submitbutton($align, $name, $text) {
    printf("<p align=\"".$align."\"><input type=\"submit\" name=\"".$name."\" value=\"".$text."\"></p>");
}

// textfield function
function textfield ($type, $name, $value, $size) {
    printf("<input type=\"".$type."\" name=\"".$name."\" value=\"".$value."\" size=\"".$size."\">");
}

function openpar($align) {
    printf("<p align=\"".$align."\">");
}

function closepar() {
    printf("</p>");
}




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