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


	public function __construct($contentobj)
	{
		if( !is_a($contentobj,'CmsContentBase') )
			{
				return;
			}
		$this->_contentobj =& $contentobj;

		// this defines the editing profile, tabs, and order of the fields in the tabs.
		$profile = new CmsContentTypeProfile();
		$profile->add_attribute(new CmsContentTypeProfileAttribute('title','main',1));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('menu_text','main',2));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('parent_id','main',3));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('active','options',1));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('show_in_menu','options',2));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('cachable','options',3));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('secure','options',5));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('alias','options',6));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('target','options',7));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('title_attribute','options',8));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('access_key','options',9));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('tab_index','options',10));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('image','options',11));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('thumbnail','options',12));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('extra1','options',13));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('extra2','options',13));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('extra3','options',13));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('owner','options',14));
		$profile->add_attribute(new CmsContentTypeProfileAttribute('additionaleditors','options',15));
		$this->_profile = $profile;
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
		// get tab names from the contentobj profile.
		$tmp = $this->_profile->get_tab_list();
		$results = array();
		foreach( $tmp as $tabname => $permission )
			{
				if( empty($permission) || check_permission(get_userid(),$permission) )
					{
						$results[$tabname] = lang($tabname);
					}
			}
		return $results;
	}


	public function get_tab_elements($tabname,$adding = FALSE)
	{
		$attrs = $this->_profile->find_all_by_tab($tabname);
		if( !$attrs ) return FALSE;
		
		$ret = array();
		for( $i = 0; $i < count($attrs); $i++ )
			{
				$ret[] = $this->get_single_element($this->_contentobj,$attrs[$i],$adding);
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
				$prompt = lang('cachable');
				$field  = '<input type="hidden" name="cachable" value="0"/><input class="pagecheckbox" type="checkbox" value="1" name="cachable"'.($content_obj->cachable()?' checked="checked"':'').' />';
				break;

			case 'title':
				$prompt = lang('title');
				$field  = '<input type="text" name="name" value="'.cms_htmlentities($content_obj->name()).'" />';
				break;

			case 'menu_text':
				$prompt = lang('menutext');
				$field  = '<input type="text" name="menu_text" value="'.cms_htmlentities($content_obj->menu_text).'" />';
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

			case 'secure':
				$prompt = lang('secure');
				$field = '<input type="hidden" name="secure" value="0"/><input class="pagecheckbox" type="checkbox" value="1" name="secure"'.($content_obj->get_property_value('secure')?' checked="checked"':'').' />';

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
					$dir = $config['image_uploads_path'];
					$data = $content_obj->get_property_value('image');
					$field = create_file_dropdown('image',$dir,$data,'jpg,jpeg,png,gif','',true,'','thumb_',0);
				}
				break;

			case 'thumbnail':
				{
					$prompt = lang('thumbnail');
					$dir = $config['image_uploads_path'];
					$data = $content_obj->get_property_value('thumbnail');
					$field = create_file_dropdown('thumbnail',$dir,$data,'jpg,jpeg,png,gif','',true,'','thumb_',0);
				}
				break;

			case 'titleattribute':
				$prompt = lang('titleattribute');
				$field = '<input type="text" name="title_attribute" maxlength="255" size="80" value="'.cms_htmlentities($content_obj->titleattribute()).'" />';
				break;

			case 'accesskey':
				$prompt = lang('accesskey');
				$field = '<input type="text" name="access_key" maxlength="5" value="'.cms_htmlentities($content_obj->access_key()).'" />';
				break;

			case 'tabindex':
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

				/*
			case 'additionaleditors':
				{
					$prompt = lang('additionaleditors');
					$text = '<input name="additional_editors" type="hidden" value=""/>';
					$text .= '<select name="additional_editors[]" multiple="multiple" size="5">';

					$userops =& $gCms->GetUserOperations();
					$groupops =& $gCms->GetGroupOperations();
					$allusers =& $userops->LoadUsers();
					$allgroups =& $groupops->LoadGroups();
					$addteditors = $this->GetAdditionalEditors();
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
							if ($oneuser->id != $this->Owner() && $oneuser->id != 1)
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
				*/

			default:
				// throw an exception?
			}

		if( !empty($prompt) && !empty($field) )
			{
				return array($prompt.':',$field);
			}
	}

	public function fill_from_form_data($params)
	{
		$props = array('name','menu_text','parent_id','active','show_in_menu','cachable','alias',
					   'title_attribute','access_key','tab_index','owner_id','additionaleditors');
		$content_obj = $this->get_content();
		foreach( $props as $oneprop )
			{
				$str = 'set_'.$oneprop;
				if( isset($params[$oneprop]) )
					$content_obj->$str($params[$oneprop]);
			}

		$props = array('secure','target','image','thumbnail','extra1','extra2','extra3');
		foreach( $props as $oneprop )
			{
				if( isset($params[$oneprop]) )
					$content_obj->set_property_value($oneprop,$params[$oneprop]);
			}
	}


	public function validate()
	{
		$content_obj = $this->get_content();
		$tmp = $content_obj->check_not_valid();
		if( $tmp )
			{
				return $content_obj->get_validation_errors();
			}
		return FALSE;
	}


	public function save()
	{
		$content_obj = $this->get_content();
		$res = $content_obj->save();
		if( !$res ) die('save failed');

		CmsContentOperations::SetAllHierarchyPositions();
		CmsCache::clear();
		audit($content_obj->id(),$content_obj->name(),'Edited Content');
	}

} // end of class

#
# EOF
#
?>
