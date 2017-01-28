<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#Visit our homepage at: http://www.cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

/**
 * Smarty plugin
 * ----------------------------------------------------------------
 * Type:    modifier
 * Name:    summarize
 * Purpose: returns desired amount of words from the full string
 *        	ideal for article text, etc.
 * Author:  MarkS, AKA Skram, mark@mark-s.net /
 *        	http://dev.cmsmadesimple.org/users/marks/
 * ----------------------------------------------------------------
 **/
 
function smarty_modifier_summarize($string,$numwords='5',$etc='...')
{
	$tmp = explode(" ",strip_tags($string));
	$stringarray = array();
	
	for( $i = 0; $i < count($tmp); $i++ )
	{
		if( $tmp[$i] != '' ) $stringarray[] = $tmp[$i];
	}
	
	if( $numwords >= count($stringarray) )
    {
		return $string;
    }
	
	$tmp = array_slice($stringarray,0,$numwords);
	$tmp = implode(' ',$tmp).$etc;
	return $tmp;    
}
?>