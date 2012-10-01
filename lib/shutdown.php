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
#$Id$

function cms_shutdown_function()
{
  $error = error_get_last();
  if( $error['type'] == E_ERROR || $error['type'] == E_USER_ERROR ) {
    $str = 'ERROR DETECTED: '.$error['message'].' at '.$error['file'].':'.$error['line'];
    debug_bt_to_log();
    debug_to_log($str);
    $db = cmsms()->GetDb();
    if( is_object($db) ) {
      // put mention into the admin log
      audit('','ERROR',$str);
    }
  }
}

register_shutdown_function('cms_shutdown_function');

#
# EOF
#
?>