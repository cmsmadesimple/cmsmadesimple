<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://www.cmsmadesimple.org
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
#$Id: lang.php 4711 2008-06-20 18:06:46Z alby $

#Reduce version from admin/lang.php for installer + smarty function

function smarty_lang($params, &$smarty)
{
	if (count($params))
	{
		$tmp = array();
		foreach ($params as $k=>$v)
		{
			$tmp[] = $v;
		}

		$str = $tmp[0];
		$tmp2 = array();
		for ($i = 1; $i < count($tmp); $i++)
			$tmp2[] = $params[$i];

		return ilang($str, $tmp2);
	}
}


#Nice decent default
$current_language = isset($frontendlang) ? $frontendlang : 'en_US';

#Only do language stuff for install pages
if (isset($CMS_INSTALL_PAGE))
{
	$nls = array();
	$lang = array();

	#Check to see if there is already a language in use...
	if (isset($_POST["default_cms_lang"]))
	{
		$current_language = $_POST["default_cms_lang"];
		if ($current_language == '')
		{
		  cms_cookies::erase("cms_language");
		}
		else if (isset($_POST["change_cms_lang"]))
		{
		  cms_cookies::set("cms_language", $_POST["change_cms_lang"]);
		}
	}
	else if (isset($_SESSION['login_cms_language']))
	{
		debug_buffer('Setting language to: ' . $_SESSION['login_cms_language']);
		$current_language = $_SESSION['login_cms_language'];
		cms_cookies::set('cms_language', $_SESSION['login_cms_language']);
		unset($_SESSION['login_cms_language']);
	}
	else if (isset($_COOKIE["cms_language"]))
	{
		$current_language = $_COOKIE["cms_language"];
	}

	#First load the english one so that we have strings to fall back on
	$base_lang = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR;
	@include($base_lang . 'en_US.php');

	#Now load the real file
	if ($current_language != 'en_US')
	{
	    $file = $base_lang . 'ext' . DIRECTORY_SEPARATOR . $current_language . '.php';
	    if (!is_file($file))
	      {
		$file = $base_lang . $current_language . '.php';
	      }

	    if (is_file($file) && strlen($current_language) == 5 && strpos($current_language, ".") === false)
	    {
	        include ($file);
	    }
	}

	$info = CmsNlsOperations::set_language($current_language);
}

# vim:ts=4 sw=4 noet
?>
