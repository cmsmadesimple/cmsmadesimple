<?php
/**
 * Smarty plugin
 * ----------------------------------------------------------------
 * Type:     modifier
 * Name:     summarize
 * Purpose:  returns desired amount of words from the full string
 *        ideal for article text, etc.
 * Auther:   MarkS, AKA Skram, mark@mark-s.net /
 *        http://dev.cmsmadesimple.org/users/marks/
 * ----------------------------------------------------------------
 **/
function smarty_cms_modifier_summarize($string,$numwords='5',$etc='...'){

//=Put each word (any character or group of characters seperated by a space) into the field's array
$stringarray = explode(" ",$string);

//=While loop to add int ($numwords) words to the summary string ($returnstring)
$i = 0;
while($i < $numwords){
    $returnstring .= " ".$stringarray[$i];
    $i++;
}

//If set, the suffix (by default "...") will now be added to the summary ($returnstring)
$returnstring .= "...";

//Return the summary!
return $returnstring;
    
}//end of function
?>
