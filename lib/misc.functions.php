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

// redirect ***************************************************************
// Purpose: Redirects to relative URL on the current site
// Author: http://www.edoceo.com/
function redirect($to)
{
	$schema = $_SERVER['SERVER_PORT'] == '443' ? 'https' : 'http';
	$host = strlen($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:$_SERVER['SERVER_NAME'];
	if (ini_get("session.use_trans_sid") != "0") {
		$to = $to."?".session_name()."=".session_id();
	}
	if (headers_sent()) return false;
	else
	{
		//header("HTTP/1.1 301 Moved Permanently");
		 header("HTTP/1.1 302 Found");
		// header("HTTP/1.1 303 See Other");
		// header("Location: $schema://$host$to");
		header("Location: $to");
		exit();
	}
}

# vim:ts=4 sw=4 noet
?>
