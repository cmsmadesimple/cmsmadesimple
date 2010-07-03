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
if (!CmsAcl::check_core_permission('Modify Groups',$user)) die('permission denied');

// setup

$group = array();
if ( !empty($params['gid']) )
	{
		$gid = (int)$params['gid'];
		$group[] = cms_orm('CmsGroup')->find_by_id($gid);
	}
elseif( is_array($params['groups']) )
	{
		foreach( $params['groups'] as $group_id )
			{
				if ( $group_id > 0 )
					{
						$gid = (int)$group_id;
						$group[] = cms_orm('CmsGroup')->find_by_id($gid);
					}
			}
	}

if( count($group) < 1 )
	{
		die('insufficient parameters');
	}

if( isset($params['cancel']) )
	{
		$this->Redirect->module_url(array('action' => 'defaultadmin', 'selected_tab' => 'groups'));
	}
else if( isset( $params['submit']))
	{
		foreach( $group as $onegroup )
			{
				foreach( $params["selected_{$onegroup->name}"] as $defn_id => $flag )
					{
						if( $flag )
							{
								CmsAcl::set_permission_by_id($defn_id,$onegroup->id);
							}
						else
							{
								CmsAcl::remove_permission_by_id($defn_id,$onegroup->id);
							}
					}
			}
		$this->Redirect->module_url(array('action' => 'defaultadmin', 'selected_tab' => 'groups'));
	}

$perms = CmsAcl::get_permission_definitions('','',false);
$groupperms = array();
foreach( $group as $onegroup)
{
	$groupperms[$onegroup->name] = CmsAcl::get_group_permissions($onegroup->id);
}

$newperms = array();
foreach( $perms as $oneperm )
{
	$oneperm['selected'] = array();
	foreach( $groupperms as $name => $group_permissions )
	{
		$oneperm['selected'][$name] = 0;
		foreach( $group_permissions as $onegroupperm )
		{
			if( $onegroupperm['id'] == $oneperm['id'] )
			{
				$oneperm['selected'][$name] = 1;
			}
		}
	}
	$newperms[] = $oneperm;
}

$smarty->assign('group',$group);
$smarty->assign('permissions',$newperms);
$smarty->assign('module_action','admin_editgroupperms');
echo $this->Template->process('editgroupperms.tpl',$id,$return_id);
# 
# EOF
#
?>
