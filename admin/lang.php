<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://cmsmadesimple.sf.net
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
#
#$Id$

#Nice decent default
$current_language = "en_US";

#Only do language stuff for admin pages
if (isset($CMS_ADMIN_PAGE)) {

	#Read in all current languages...
	$dir = dirname(__FILE__)."/lang";
	$ls = dir($dir);
	while (($file = $ls->read()) != "") {
		if (is_file("$dir/$file") && strpos($file, "nls.php") != 0) {
			include("$dir/$file");
		}
	}

	#Check to see if there is already a language in use...
	if (isset($_POST["change_cms_lang"]))
	{
		$current_language = $_POST["change_cms_lang"];
		setcookie("cms_language", $_POST["change_cms_lang"]);
	}
	else if (isset($_COOKIE["cms_language"]))
	{
		$current_language = $_COOKIE["cms_language"];
	}
	else
	{
		#No, take a stab at figuring out the default language...
		#Figure out default language and set it if it exists
		if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
			$svrstring = $_SERVER["HTTP_ACCEPT_LANGUAGE"];
			$alllang = substr($svrstring,0,strpos($svrstring, ";"));
			$langs = explode(",", $alllang);

			foreach ($langs as $onelang) {
				#Check to see if lang exists...
				if (isset($nls['language'][$onelang])) {
					$current_language = $onelang;
					setcookie("cms_language", $onelang);
					break;
				}
				#Check to see if alias exists...
				if (isset($nls['alias'][$onelang])) {
					$alias = $nls['alias'][$onelang];
					if (isset($nls['language'][$alias])) {
						$current_language = $alias;
						setcookie("cms_language", $alias);
						break;
					}
				}
			}
		}
	}
	#Ok, we have a language to load, let's load it already...
	if (isset($nls['file'][$current_language]))
	{
		foreach ($nls['file'][$current_language] as $onefile)
		{
			include($onefile);
		}
	}

	global $gCms;
	$gCms->nls = $nls;
	$gCms->current_language = $current_language;
}

# vim:ts=4 sw=4 noet
?>
