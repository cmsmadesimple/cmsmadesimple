<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
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

// security
if (!isset($gCms)) die("Can't call actions directly!");
$user = CmsLogin::get_current_user();
if (!CmsAcl::check_core_permission('Manage Groups',$user)) die('permission denied');

// setup
if( !isset($params['gid']) )
	{
		die('insufficient parameters');
	}
$gid = (int)$params['gid'];
$group = CmsGroupOperations::load_group_by_id($gid);

if( isset($params['cancel']) )
	{
		$this->redirect($id,'defaultadmin',$return_id,array('selected_tab'=>'groups'));
	}
else if( isset( $params['submit']) )
	{
		foreach( $params['selected'] as $defn_id => $flag )
			{
				$defn = CmsAcl::get_permission_definition_by_id($defn_id);
				if( $flag )
					{
						CmsAcl::set_permission($defn['module'],$defn['extra_attr'],$defn['name'],-1,$gid);
					}
				else
					{
						CmsAcl::remove_permission($defn['module'],$defn['extra_attr'],$defn['name'],$gid);
					}
			}
		$this->redirect($id,'defaultadmin',$return_id,array('selected_tab'=>'groups'));
	}

$perms = CmsAcl::get_permission_definitions('','',false);
$groupperms = CmsAcl::get_group_permissions($gid);

$newperms = array();
foreach( $perms as $oneperm )
{
	$oneperm['selected'] = 0;
	foreach( $groupperms as $onegroupperm )
	{
		if( $onegroupperm['id'] == $oneperm['id'] )
		{
			$oneperm['selected'] = 1;
		}
	}
	$newperms[] = $oneperm;
}

$smarty->assign('group',$group);
$smarty->assign('permissions',$newperms);
echo $this->process_template('editgroupperms.tpl',$id,$return_id);
# 
# EOF
#
?>