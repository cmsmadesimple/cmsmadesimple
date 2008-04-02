<?php
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
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

function smarty_cms_block_permission($params,$content,&$smarty)
{
  if( isset($params['check']) )
    {
      $check = explode(',',trim($params['check']));
      $user = CmsLogin::get_current_user();
      foreach( $check as $oneitem )
	{
	  list($module,$perm) = explode(':',$oneitem);
	  $res = '';
	  if( $perm == '' )
	    {
	      $perm = $module;
	      $res = CmsAcl::check_core_permission($perm,$user);
	    }
	  else
	    {
	      $res = CmsAcl::check_permission($module,'',$perm,-1,null,$user);
	    }
	  if( $res ) return $content;
	}
    }
  return '';
}

?>