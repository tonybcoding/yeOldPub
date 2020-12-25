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

function fullparse($curstr) {
    $curstr = specialCharacters($curstr);                              // Handle special characters and line breaks
    if($_SESSION['parsemessage']) unset ($_SESSION['parsemessage']);
    if (checkEvenNumofFormat($curstr, BOLD)) {
        $curstr = parseText($curstr, BOLD, "<b>", "</b>");
    }
    if (checkEvenNumofFormat($curstr, ITALIC)) {
        $curstr = parseText($curstr, ITALIC, "<i>", "</i>");
    }
    if (checkEvenNumofFormat($curstr, UNDERLINE)) {
        $curstr = parseText($curstr, UNDERLINE, "<u>", "</u>");
    }
    if (checkEvenNumofFormat($curstr, HYPERLINK)) {
        $curstr = parseLink($curstr, HYPERLINK, LINKSTART, "url");         // Search for hyperlink identifier and replace
    }
    if (checkEvenNumofFormat($curstr, EMAILLINK)) {
        $curstr = parseLink($curstr, EMAILLINK, EMAILSTART, "email");      // Search for email identifier and replace
    }
    return $curstr;
}

function checkEvenNumofFormat ($str, $formatstr) {
    $even = true;
    $num = substr_count($str, $formatstr);
    if (floatval($num/2) != intval($num/2)) {
        $_SESSION['parsemessage'] .="Uneven number of format characters: ".$formatstr."</br>";
        $even = false;
    }
    return $even;
}











///////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////
// UNCLEARED FUNCTIONS
///////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////




















function parseLink ($fullstr, $linkid, $linksym, $linktype) {

       // Search for linkid identifier and replace
       While (stristr($fullstr, $linkid)) {
           // find opening and closing positions of this instance
           $offset1 = strpos($fullstr, $linkid);
           $offset2 = strpos($fullstr, $linkid, $offset1+1);

           // break out entire string between $linkid identifiers
           $frontstr = substr($fullstr, 0, $offset1);
           $hyperstr = substr($fullstr, $offset1, ($offset2-$offset1+strlen($linkid)));
           $backstr  = substr($fullstr, ($offset2+strlen($linkid)));
           $hyperstr = substr($hyperstr, strlen($linkid), strlen($hyperstr)-(strlen($linkid)*2));

           // if $offset is false, then no "$linksym" was found, entire line is intended to be the URL
           if (!$offset1 = strpos($hyperstr, $linksym)) {
              $textlink = $urllink = $hyperstr;
           }
           // else text to be displayed is different than linkid
           else {
              $textlink = substr($hyperstr, 0, $offset1);
              $urllink  = substr($hyperstr, $offset1 + strlen($linksym));
           }

           if ($linktype == "url") {
              $fullstr = ($frontstr . "<a href=\"" . $urllink . "\" TARGET=\"_new\">" . $textlink . "</a>" . $backstr);
           }
           else {
              $fullstr = ($frontstr . "<a href=\"mailto:" . $urllink . "\">" . $textlink . "</a>" . $backstr);
           }
       } // end of while loop to search for linkid identifier
       return $fullstr;
} // end of parseText function


function parseText ($fullstr, $symbol, $formatbegin, $formatend) {

       While (stristr($fullstr, $symbol)) {

           // find opening
           $parsestr = stristr($fullstr, $symbol);
           $offset = strlen($fullstr)-strlen($parsestr);
           $newstr = (substr($fullstr,0,$offset) . $formatbegin . substr($fullstr,($offset+strlen($symbol))));

           // find closing
           $fullstr = $newstr;
           $parsestr = stristr($fullstr, $symbol);
           $offset = strlen($fullstr)-strlen($parsestr);
           $newstr = (substr($fullstr,0,$offset) . $formatend . substr($fullstr,($offset+strlen($symbol))));
           $fullstr = $newstr;
       } // end of while loop to search for formatting indicators
       return $fullstr;
} // end of parseText function

function specialCharacters ($fullstr){

       // find end of lines in text and create breaks
       $linebreak = ("\r\n");  // this appears to be windows eol combination
       $fullstr = str_replace($linebreak, "<br/>", $fullstr);
       
       // how do we handle submitted double quotes, single quotes, and backslashes?
       // MySQL can handle double quotes and backslashes, but adds a ' in front of intentional single quote
       
       
       return $fullstr;
}

?>