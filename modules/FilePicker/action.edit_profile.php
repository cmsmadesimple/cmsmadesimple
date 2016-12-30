<?php
#-------------------------------------------------------------------------
# Module: FilePicker - A CMSMS addon module to provide file picking capabilities.
# (c) 2016 by Fernando Morgado <jomorg@cmsmadesimple.org>
# (c) 2016 by Robert Campbell <calguy1000@cmsmadesimple.org>
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
#-------------------------------------------------------------------------
#-------------------------------------------------------------------------
# BEGIN_LICENSE
#-------------------------------------------------------------------------
# This file is part of FilePicker
# FilePicker is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# FilePicker is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#-------------------------------------------------------------------------
# END_LICENSE
#-------------------------------------------------------------------------

if( !defined('CMS_VERSION') ) exit;

if( isset($params['cancel']) )
{
	$this->Redirect($id, 'defaultadmin', $returnid, array() );
}

$profile_id = isset($params['id']) ? $params['id'] : NULL; 
$profile_id = isset($params['_id']) ? $params['_id'] : $profile_id; 


if( !empty($profile_id) && (int)$profile_id > -1)
{
	# we are editing
	$tmp = $this->_get_profile_by_id($profile_id);  
	$profile = new stdClass();
	$profile->id = $profile_id;
	$profile->name = $tmp['name'];
	$profile->params = $this->_get_profile_data($tmp['data']);
}
else
{
	# new one
	$profile = new stdClass();
	$profile->id = -1;
	$profile->name = '';
	$profile->params = $this->_get_profile_data();
}

if( isset($params['submit']) || isset($params['apply']) )
{
	$profile->id = $profile_id;
	$profile->name = $this->_conform_profile_name($params['name']);
  
	foreach($this->_get_profile_data('') as $k => $v)
	{
		if($profile->params[$k]['type'] == ProfileParameter::TYPE_CHECKBOX)
		{
			if(isset( $params[$k]) )
			$profile->params[$k]['value'] = (bool)$params[$k];
			continue;
		}
		
		if($profile->params[$k]['type'] == ProfileParameter::TYPE_MULTISELECT)
		{
			if(isset( $params[$k]) )
			$profile->params[$k]['value'] = implode(',', $params[$k]);
			continue;
		}
		
		$profile->params[$k]['value'] = $params[$k];
	}
  
	$this->_save_profile($profile);
  
	if( isset($params['submit']) )
	{
		$this->Redirect($id, 'defaultadmin', $returnid, array('msg' => $this->Lang('msg_success') ) );
	}
	else
	{
		$this->ShowMessage( $this->Lang('msg_success') );
	}
}

$smarty->assign('profile', $profile);

echo $this->ProcessTemplate('edit_profile.tpl');

#
# EOF
#
?>