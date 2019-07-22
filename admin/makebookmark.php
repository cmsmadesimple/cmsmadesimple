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
#
#$Id$

global $CMS_ADMIN_PAGE;
$CMS_ADMIN_PAGE = 1;

require_once('../lib/include.php');
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

include_once("header.php");

check_login();
$config = cmsms()->GetConfig();
$key = get_parameter_value($_GET,'key');
if( !$key || !isset($_SESSION[$key]) ) throw new \InvalidArgumentException('Missing param');
list($sig,$url,$title) = explode(':',base64_decode($_SESSION[$key]),3);
unset($_SESSION[$key]);
if( $sig != sha1($url.cmsms()->get_site_identifier()) ) throw new \InvalidArgumentException('Invalid/Incorrect session data');

$newmark = new Bookmark();
$newmark->user_id = get_userid();
$newmark->url = $url;
$newmark->title = $title;
$result = $newmark->save();

if ($result)
	{
    header('HTTP_REFERER: '.$config['admin_url'].'/index.php');
    redirect($url);
}
else
	{
    include_once("header.php");
    echo "<h3>". lang('erroraddingbookmark') . "</h3>";
}
