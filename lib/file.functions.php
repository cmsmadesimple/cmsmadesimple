<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://cmsmadesimple.sf.net
#
# library written by Thijs Elenbaas (thijs@contemplated.net)
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
 * File related functions. Used within the file manager and possibly
 * in filehandling programs 
 *
 */

/**
 * Finds the file extention to for current filename
 * to the admin login.
 *
 * @since 6.3 ?
 */
function get_file_extention($filename) {
		
	global $filetype;	
		
	// get the file extention
	$extension = strtolower(substr(strrchr($filename, "."), 1));
	//extention type if nothing is found:
	$found_ext = "unknown";
	// See if extention is listed
	if ($filetype[$extension]['img']) {
	    // Yes, listed!
		$found_ext = $extension;
	} else {
	 	// apparently not, so let's browse the aliasses
		foreach ($filetype  as $current_ext=>$current_type) {	  
		   if ($current_type[alias]) {
			   foreach ($current_type[alias] as $current_alias) {
				   if ($extension == $current_alias) { 
						$found_ext = $current_ext;
						break(2);
				   };
			   }
		   }
		}
	}
	return $found_ext;
}

function display_file($filename) {
	$do_display = true;	
	
	global $excludefilters;
		
	foreach ($excludefilters as $currentfilter) {
		if (fnmatch($currentfilter, $filename)) {
			$do_display = false;
			break(1);
		}
	}
	return $do_display;
}


# vim:ts=4 sw=4 noet
?>
