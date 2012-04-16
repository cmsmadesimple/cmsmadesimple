<?php
#CMS - CMS Made Simple
#(c)2004-2012 by Ted Kulp (wishy@users.sf.net)
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
#$Id$

$CMS_ADMIN_PAGE = TRUE;
$CMS_STYLESHEET = TRUE;

require_once("../include.php");
cms_admin_sendheaders('text/css');

$theme=get_preference(get_userid(),"admintheme",CmsAdminThemeBase::GetDefaulttheme());
$style="style";

$thelang = CmsNlsOperations::get_language_info(CmsNlsOperations::get_current_language());
if( is_object($thelang) && $thelang->direction() == 'rtl' )
  {
    $style.="-rtl";
  }
if (isset($_GET['ie']))
  {
    $style.="_ie";
  }
$style .= ".css";

if (file_exists(dirname(__FILE__)."/themes/".$theme."/css/".$style))
  {
    cms_readfile(dirname(__FILE__)."/themes/".$theme."/css/".$style);
  }
else if (file_exists(dirname(__FILE__)."/themes/default/css/".$style))
  {
    cms_readfile(dirname(__FILE__)."/themes/default/css/".$style);
  }

$allmodules = ModuleOperations::get_instance()->GetLoadedModules();
if( is_array($allmodules) && count($allmodules) )
  {
    foreach( $allmodules as $key => &$object )
      {
	if( $object->HasAdmin() )
	  {
	    echo $object->AdminStyle();
	  }
      }
  }
# vim:ts=4 sw=4 noet
?>
