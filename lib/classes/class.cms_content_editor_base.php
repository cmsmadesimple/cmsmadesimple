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
#$Id: class.cms_content_base.php 5976 2009-09-07 14:23:29Z wishy $

class CmsContentEditorBase
{
	private $_contentobj;
	private $_profile;
	private $_merged_basic = false;
	private $_merged_module = false;
	private $_merged_module_tabs = false;

	public function __construct($contentobj)
	{
		if( !is_a($contentobj,'CmsContentBase') )
			{
				return;
			}
		$this->_contentobj =& $contentobj;

		// this defines the editing profile, tabs, and order of the fields in the tabs.
		$profile = new CmsContentTypeProfile();
		$profile->add_tab('main');
		$profile->add_tab('navigation','Manage All Content');
		$profile->add_tab('options','Manage All Content');

		$profile->add_attribute(new CmsContentTypeProfileAttribute('title','main'));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('parent_id','main'));

		$profile->add_attribute(new CmsContentTypeProfileAttribute('show_in_menu','navigation'));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('menu_text','navigation'));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('target','navigation'));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('title_attribute','navigation'));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('class_name','navigation'));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('access_key','navigation'));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('tab_index','navigation'));

		$profile->add_attribute(new CmsContentTypeProfileAttribute('active','options'));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('cachable','options'));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('secure','options'));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('alias','options'));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('image','options'));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('thumbnail','options'));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('extra1','options'));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('extra2','options'));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('extra3','options'));

		$profile->add_attribute(new CmsContentTypeProfileAttribute('owner','permissions',
																   array('CmsContentEditorBase','perm_act_as_owner')));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('additionaleditors','permissions',
																   array('CmsContentEditorBase','perm_act_as_owner')));

		$this->_profile = $profile;
	}


	private function _merge_basic_attributes()
	{
		if( $this->_merged_basic ) return;

		// move all of the 'basic attributes' to the main tab.
		$tmp = get_site_preference('basic_attributes');
		if( $tmp )
		{
			$profile = $this->get_profile();
			$basic_attrs = explode(',',$tmp);
			foreach( $basic_attrs as $one_attr_name )
			{
				$attr = $profile->find_by_name($one_attr_name);
				if( is_object($attr) )
				{
					$attr->set_tab('main');
				}
			}
		}

		$this->_merged_basic = true;
	}


	private function _merge_module_attributes()
	{
		if( $this->_merged_module ) return;

		$gCms = cmsms();
		$profile = $this->get_profile();
		$content_obj = $this->get_content();
		foreach( $gCms->modules as $name => &$data )
		{
			if( !isset($data['object']) ) continue;
			$module =& $data['object'];

			if( !$module->HasCapability('content_attributes') ) continue;
			if( !method_exists($module,'get_content_attributes') ) continue;
			
			$attrs = $module->get_content_attributes(get_class($content_obj));
			if( is_array($attrs) )
			{
				for( $i = 0; $i < count($attrs); $i++ )
				{
					$attrs[$i]->set_module($name);
					$profile->add_attribute($attrs[$i]);
				}
			}
		}

		$this->_merged_module = true;
	}


	private function _get_module_attribute_input($content_obj,$attr,$adding = false)
	{
		$mname = strtolower($attr->get_module());
		if( !$mname || $mname == 'core' ) return;

		$module = ModuleOperations::get_module($attr->get_module());
		if( !$module ) return;

		if( !$module->HasCapability('content_attributes') ) return;
		$helper = $attr->get_helper();
		if( !is_object($helper) || !is_a($helper,'CmsContentAttributeHelperBase') ) return;

		$data = $helper->get_attribute_input($attr,$content_obj,$adding);
		if( !is_array($data) ) return;

		return $data;
	}


	private function _get_module_tabs()
	{
		$profile = $this->get_profile();
		$attrs = $profile->find_all_with_helper();
		if( !is_array($attrs) ) return;

		for( $i = 0; $i < count($attrs); $i++ )
		{
			$helper = $attrs[$i]->get_helper();
			$tabs = $helper->get_attribute_tabs();
			if( is_array($tabs) )
			{
				foreach( $tabs as $key => $data )
				{
					$profile->add_tab($key,$data['permission'],$data['prompt']);
				}
			}
		}
	}

	private function _fill_module_attributes($content_obj,$params)
	{
		$profile = $this->get_profile();
		$attrs = $profile->find_all_with_helper();
		if( !is_array($attrs) ) return;

		for( $i = 0; $i < count($attrs); $i++ )
		{
			$helper = $attrs[$i]->get_helper();
			$helper->get_attributes_from_formdata($content_obj,$params);
		}
	}


	private function _validate_module_attribute($content_obj,$attr)
	{
		$profile = $this->get_profile();
		$attrs = $profile->find_all_with_helper();
		if( !is_array($attrs) ) return;

		$tmp = array();
		for( $i = 0; $i < count($attrs); $i++ )
		{
			$helper = $attrs[$i]->get_helper();
			$ret = $helper->validateattributes($content_obj);
			if( $ret ) $tmp = array_merge($tmp,$ret);
		}
		
		if( count($tmp) ) return $tmp;
		return FALSE;
	}


	protected function &get_content()
	{
		return $this->_contentobj;
	}


	public function &get_profile()
	{
		return $this->_profile;
	}


	public function get_tab_names()
	{
		$this->_merge_module_attributes();
		$this->_get_module_tabs();
		$this->_merge_basic_attributes();

		// get tab names from the contentobj profile.
		$tmp = $this->_profile->get_tab_list();
		$results = array();
		foreach( $tmp as $tabname => $data )
		{
			$permission = $data['permission'];
			if( empty($permission) || check_permission(get_userid(),$permission) )
			{
				// now go through all the elements in each tab
				// and see if we have permission for at least one of them.
				$attrs = $this->_profile->find_all_by_tab($tabname);
				if( !$attrs ) continue;
				for( $i = 0; $i < count($attrs); $i++ )
				{
					$attr =& $attrs[$i];
					$perm = $attr->get_permission();
					$okay = true;
					if( $perm != '' )
					{
						$okay = false;
						if( is_array($perm) )
						{
							$okay = call_user_func($perm,get_userid());
						}
						else if( function_exists($perm) )
						{
							$okay = call_user_func($perm,get_userid());
						}
						else
						{
							$okay = check_permission(get_userid(),$perm);
						}
					}
					if( $okay )
					{
						$results[$tabname] = $data['prompt'];
						break;
					}
				}
			}
		}
		return $results;
	}


	public function get_tab_elements($tabname,$adding = FALSE)
	{
		$this->_merge_module_attributes();
		$this->_merge_basic_attributes();

		$attrs = $this->_profile->find_all_by_tab($tabname);
		if( !is_array($attrs) ) return FALSE;
		
		$ret = array();
		for( $i = 0; $i < count($attrs); $i++ )
		{
			$attr =& $attrs[$i];
			$perm = $attr->get_permission();
			$okay = false;
			if( $perm == '' )
			{
				$okay = true;
			}
			else
			{
				if( is_array($perm) )
				{
					$okay = call_user_func($perm,get_userid());
				}
				else if( function_exists($perm) )
				{
					$okay = call_user_func($perm,get_userid());
				}
				else
				{
					$okay = check_permission(get_userid(),$perm);
				}
			}
			if( $okay )
				{
					$tmp = $this->get_single_element($this->_contentobj,$attrs[$i],$adding);
					if( $tmp )
					{
						$ret[] = $tmp;
					}
					else
					{
					}
				}
		}
		return $ret;
	}


	protected function get_single_element($content_obj,$attr,$adding = false)
	{
		$gCms = cmsms();
		$config = cms_config();

		$prompt = '';
		$field  = '';

		switch( $attr->get_name() )
		{
		case 'cachable':
			{
				$str = ($content_obj->cachable())?' checked="checked"':'';
				$prompt = lang('cachable');
				$field  = '<input type="hidden" name="cachable" value="0"/>';
				$field .= '<input class="pagecheckbox" type="checkbox" value="1" name="cachable"'.$str.'/>';
			}
			break;
			
		case 'title':
			$prompt = lang('title');
			$field  = '<input type="text" name="name" value="'.cms_htmlentities($content_obj->name()).'" />';
			break;
			
		case 'menu_text':
			$prompt = lang('menutext');
			$field  = '<input type="text" name="menu_text" value="'.cms_htmlentities($content_obj->menu_text).'" />';
			break;

		case 'class_name':
			$prompt = lang('css_class_name');
			$field  = '<input type="text" name="class_name" value="'.cms_htmlentities($content_obj->get_property_value('class_name')).'" />';
			break;
			
		case 'parent_id':
			{
				$prompt = lang('parent');
				$contentops =& $gCms->GetContentOperations();
				$tmp = $contentops->CreateHierarchyDropdown($content_obj->id, $content_obj->parent_id, 'parent_id', 0, 1);
				if( empty($tmp) || !check_permission(get_userid(),'Manage All Content') )
					$field = '<input type="hidden" name="parent_id" value="'.$content_obj->parent_id().'" />';
				if( !empty($tmp) ) $field = $tmp;
			}
			break;

		case 'active':
			$prompt = lang('active');
			$field = '<input type="hidden" name="active" value="0"/><input class="pagecheckbox" type="checkbox" name="active" value="1"'.($content_obj->active()?' checked="checked"':'').' />';
			break;
			
		case 'show_in_menu':
			$prompt = lang('showinmenu');
			$field = '<input type="hidden" name="show_in_menu" value="0"/><input class="pagecheckbox" type="checkbox" value="1" name="showinmenu"'.($content_obj->showinmenu()?' checked="checked"':'').' />';
			break;
			
		case 'secure':
			$prompt = lang('secure_page');
			$field = '<input type="hidden" name="secure" value="0"/><input class="pagecheckbox" type="checkbox" value="1" name="secure"'.($content_obj->get_property_value('secure')?' checked="checked"':'').' />';
			break;
			
		case 'target':
			{
				$prompt = lang('target');
				$text = '<option value="---">'.lang('none').'</option>';
				$val = $content_obj->get_property_value('target');
				$text .= '<option value="_blank"'.($val=='_blank'?' selected="selected"':'').'>_blank</option>';
				$text .= '<option value="_parent"'.($val=='_parent'?' selected="selected"':'').'>_parent</option>';
				$text .= '<option value="_self"'.($val=='_self'?' selected="selected"':'').'>_self</option>';
				$text .= '<option value="_top"'.($val=='_top'?' selected="selected"':'').'>_top</option>';
				$field = '<select name="target">'.$text.'</select>';
			}
			break;
			
		case 'alias':
			$prompt = lang('pagealias');
			$field = '<input type="text" name="alias" value="'.$content_obj->alias().'" />';
			break;
			
		case 'image':
			{
				$prompt = lang('image');
				$tmp = trim(get_site_preference('content_image_path',''),'/');
				$dir = cms_join_path($config['image_uploads_path'],$tmp);
				$data = basename($content_obj->get_property_value('image'));
				$field = create_file_dropdown('image',$dir,$data,'jpg,jpeg,png,gif',true,'thumb_');
			}
			break;
			
		case 'thumbnail':
			{
				$prompt = lang('thumbnail');
				$tmp = trim(get_site_preference('content_image_path',''),'/');
				$dir = cms_join_path($config['image_uploads_path'],$tmp);
				$data = basename($content_obj->get_property_value('thumbnail'));
				$field = create_file_dropdown('thumbnail',$dir,$data,'jpg,jpeg,png,gif',true,'thumb_',0);
			}
			break;
			
		case 'title_attribute':
			$prompt = lang('titleattribute');
			$field = '<input type="text" name="title_attribute" maxlength="255" size="80" value="'.cms_htmlentities($content_obj->titleattribute()).'" />';
			break;
			
		case 'access_key':
			$prompt = lang('accesskey');
			$field = '<input type="text" name="access_key" maxlength="5" value="'.cms_htmlentities($content_obj->access_key()).'" />';
			break;
			
		case 'tab_index':
			$prompt = lang('tabindex');
			$field = '<input type="text" name="tab_index" maxlength="5" value="'.cms_htmlentities($content_obj->tab_index()).'" />';
			break;
			
		case 'extra1':
			$prompt = lang('extra1');
			$field = '<input type="text" name="extra1" maxlength="255" size="80" value="'.cms_htmlentities($content_obj->get_property_value('extra1')).'" />';
			break;
			
		case 'extra2':
			$prompt = lang('extra2');
			$field = '<input type="text" name="extra2" maxlength="255" size="80" value="'.cms_htmlentities($content_obj->get_property_value('extra2')).'" />';
			break;
			
		case 'extra3':
			$prompt = lang('extra3');
			$field = '<input type="text" name="extra3" maxlength="255" size="80" value="'.cms_htmlentities($content_obj->get_property_value('extra3')).'" />';
			break;
			
		case 'owner':
			{
				$prompt = lang('owner');
				$showadmin = check_ownership(get_userid(), $content_obj->id());
				$userops =& $gCms->GetUserOperations();
				if (!$adding && $showadmin)
					{
						$field = $userops->GenerateDropdown($content_obj->owner());
					}
			}
			break;
			
		case 'additionaleditors':
			{
				$prompt = lang('additionaleditors');
				$text = '<input name="additional_editors" type="hidden" value=""/>';
				$text .= '<select name="additional_editors[]" multiple="multiple" size="5">';
				
				$userops =& $gCms->GetUserOperations();
				$groupops =& $gCms->GetGroupOperations();
				$allusers =& $userops->LoadUsers();
				$allgroups =& $groupops->LoadGroups();
				$addteditors = $content_obj->get_additional_users();
				foreach ($allgroups as $onegroup)
				{
					if( $onegroup->id == 1 ) continue;
					$val = $onegroup->id*-1;
					$text .= '<option value="'.$val.'"';
					if( in_array($val,$addteditors) )
					{
						$text .= ' selected="selected"';
					}
					$text .= '>'.lang('group').': '.$onegroup->name."</option>";		   
				}
				
				foreach ($allusers as $oneuser)
				{
					if ($oneuser->id != $content_obj->owner() && $oneuser->id != 1)
					{
						$text .= '<option value="'.$oneuser->id.'"';
						if (in_array($oneuser->id, $addteditors))
						{
							$text .= ' selected="selected"';
						}
						$text .= '>'.$oneuser->username.'</option>';
					}
				}
				
				$text .= '</select>';
				$field = $text;
			}
			break;

		default:
			$tmp = $this->_get_module_attribute_input($content_obj,$attr);
			if( is_array($tmp) )
				{
					return $tmp;
				}
			break;
		}

		if( !empty($prompt) && !empty($field) )
		{
			return array($prompt.":",$field);
		}
	}

	public function fill_from_form_data($params)
	{
		// basic attributes
		$props = array('name','menu_text','parent_id','active','show_in_menu','cachable','alias',
					   'title_attribute','access_key','tab_index','owner_id');
		$content_obj = $this->get_content();
		foreach( $props as $oneprop )
		{
			$str = 'set_'.$oneprop;
			if( isset($params[$oneprop]) )
				$content_obj->$str($params[$oneprop]);
		}

		// attributes to be handled specially
		$props = array('image','thumbnail');
		$tdir = trim(get_site_preference('content_image_path',''),'/');
		foreach( $props as $oneprop )
		{
			if( isset($params[$oneprop]) )
			{
				$tmp = cms_join_path($tdir,$params[$oneprop]);
				$content_obj->set_property_value($oneprop,$tmp);
			}
		}

		// pre-defined attributes
		$props = array('secure','target','extra1','extra2','extra3','class_name');
		foreach( $props as $oneprop )
		{
			if( isset($params[$oneprop]) )
				$content_obj->set_property_value($oneprop,$params[$oneprop]);
		}

		// module defined attributes
		$this->_fill_module_attributes($content_obj,$params);

		// additional users.
		$content_obj->set_additional_users($params['additional_editors']);
	}


	public function validate()
	{
		$content_obj = $this->get_content();
		$tmp = $content_obj->check_not_valid();
		if( $tmp )
		{
			$tmp = $content_obj->validation_errors;
			return $tmp;
		}
		return FALSE;
	}


	public function save()
	{
		$content_obj = $this->get_content();
		$res = $content_obj->save();
		if( !$res )
		{
			stack_trace();
			die('save failed');
		}

		CmsContentOperations::SetAllHierarchyPositions();
		CmsCache::clear();
		audit($content_obj->id(),$content_obj->name(),'Edited Content');
	}

	public static function perm_act_as_owner($uid)
	{
		if( check_permission(get_userid(),'Manage All Content') ) return true;
		$content_obj = $this->get_content();
		return $uid == $content_obj->owner_id;
	}

	public static function perm_can_edit($uid)
	{
		$content_obj = $this->get_content();
		return $content_obj->check_edit_permission($uid);
	}
} // end of class

#
# EOF
#
?>
