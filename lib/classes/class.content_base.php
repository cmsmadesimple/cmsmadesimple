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

/**
 * A class to provide backwards compatibility
 *
 */
class ContentBase extends CmsContentBase
{
	private $_editor_obj;

	public function __construct();
	{
		if( method_exists($this,'SetProperties') )
			{
				$this->SetProperties();
			}
	}

  private function &get_editor()
  {
	  if( is_null($this->_editor_obj) )
	  {
		  $editortype = CmsContentOperations::get_content_editor_type($this);
		  $editor = new $editortype($this);
	  }
	  return $this->_editor_obj;
  }

  final public function friendly_name()
  {
	  if( method_exists($this,'FriendlyName') )
		  {
			  return $this->FriendlyName();
		  }
	  return parent::friendly_name();
  }

  final public function wants_children()
  {
	  if( method_exists($this,'WantsChildren') )
		  {
			  return $this->WantsChildren();
		  }
	  return parent::wants_children();
  }

  public function has_usable_link()
  {
	  if( method_exists($this,'HasUsableLink') )
		  {
			  return $this->HasUsableLink();
		  }
	  return parent::has_usable_link();
  }

  public function IsCopyable()
  {
	  if( method_exists($this,'IsCopyable') )
		  {
			  return $this->IsCopyable();
		  }
	  return $this->is_copyable();
  }

  public function IsViewable()
  {
	  if( method_exists($this,'IsViewable') )
		  {
			  return $this->IsViewable();
		  }
	  return $this->is_viewable();
  }

  public function IsSystemPage()
  {
	  if( method_exists($this,'IsSystemPage') )
		  {
			  return $this->IsSystemPage();
		  }
	  return $this->is_system_page();
  }

  public function IsDefaultPossible()
  {
	  if( method_exists($this,'IsDefaultPossible') )
		  {
			  return $this->IsDefaultPossible();
		  }
	  return $this->is_default_possible();
  }


  ////////////////////////////////////////////////////////////////////////////////

  public function SetName($name)
  {
	  return $this->set_name($name);
  }

  public function SetAlias($alias,$doAutoAliasIfEnabled = true)
  {
	  return $this->set_alias($alias);
  }

  public function SetType($type)
  {
	  return $this->set_type();
  }

  public function SetOwner($owner)
  {
	  return $this->set_owner($owner);
  }

  public function SetMetaData($metadata)
  {
	  return $this->set_metadata($metadata);
  }

  public function TabIndex()
  {
	  return $this->tab_index();
  }

  public function SetTabIndex($tabindex)
  {
	  return $this->set_tab_index($tabindex);
  }

  public function TitleAttribute()
  {
	  return $this->title_attribute();
  }

  public function SetTitleAttribute($titleattribute)
  {
	  return $this->set_title_attribute($titleattribute);
  }

  public function AccessKey()
  {
	  return $this->access_key();
  }

  public function SetAccessKey($accesskey)
  {
	  return $this->set_access_key($accesskey);
  }

  public function ParentId()
  {
	  return $this->parent_id();
  }

  public function SetParentId($parentid)
  {
	  return $this->set_parent_id($parentid);
  }

  public function TemplateId()
  {
	  return $this->template_id();
  }

  public function SetTemplateId($templateid)
  {
	  return $this->set_template_id($templateid);
  }

  public function ItemOrder()
  {
	  return $this->item_order();
  }

  public function SetItemOrder($itemorder)
  {
	  return $this->set_item_order($itemorder);
  }

  public function SetHierarchy($hierarchy)
  {
	  return $this->set_hierarchy($hierarchy);
  }

  public function HierarchyPath()
  {
	  return $this->hierarchy_path();
  }

  public function SetHierarchyPath($hierarchypath)
  {
	  return $this->set_hierarchy_path($hierarchypath);
  }

  public function SetActive($flag)
  {
	  return $this->set_active($flag);
  }

  public function ShowInMenu()
  {
	  return $this->show_in_menu();
  }

  public function SetShowInMenu($flag)
  {
	  return $this->set_show_in_menu($flag);
  }

  public function DefaultContent()
  {
	  return $this->default_content();
  }

  public function SetDefaultContent($flag)
  {
	  return $this->set_default_content($flag);
  }

  public function SetCachable($flag)
  {
	  return $this->set_cachable($flag);
  }

  public function Markup()
  {
	  return $this->markup();
  }

  public function SetMarkup($markup)
  {
	  return $this->set_markup();
  }

  public function LastModifiedBy()
  {
	  return $this->last_modified_by();
  }

  public function SetLastModifiedBy($lastmodifiedby)
  {
	  return $this->set_last_modified_by($lastmodifiedby);
  }

  public function MenuText()
  {
	  return $this->menu_text();
  }

  public function SetMenuText($menutext)
  {
	  return $this->set_menu_text($menutext);
  }

  public function GetCreationDate()
  {
	  $this->create_date();
  }

  public function GetModifiedDate()
  {
	  $this->modified_date();
  }


  public function Properties()
  {
	  die('not supported');
  }

  public function HasProperty($name)
  {
	  return $this->has_property($name);
  }

  public function GetPropertyValue($name)
  {
	  return $this->property_value($name);
  }

  public function SetPropertyValue($name,$value)
  {
	  return $this->set_property_value($name,$value);
  }

  public function GetAdditionalEditors()
  {
	  //stack_trace();
	  //echo "TODO: ContentBase::GetAdditionalEditors<br/>";
  }

  public function SetAdditionalEditors($editorsarray)
  {
	  //stack_trace();
	  //echo "TODO: ContentBase::SetAdditionalEditors<br/>";
  }

  public function RemoveProperty($name,$dflt)
  {
	  $profile = $this->get_editor()->get_profile();
	  $profile->remove_by_name($name);
  }

  public function AddBaseProperty($name,$priority,$is_requried = 0,$type = 'string')
  {
	  $profile = $this->get_editor()->get_profile();
	  $profile->add_attribute(new CmsContentTypeProfileAttribute($name,'main',$priority));
  }

  public function AddExtraProperty($name,$type='string')
  {
	  $profile = $this->get_editor()->get_profile();
	  $profile->add_attribute(new CmsContentTypeProfileAttribute($name,'options'));
  }

  public function AddContentProperty($name,$priority,$is_required = 0,$type = 'string')
  {
	  $profile = $this->get_editor()->get_profile();
	  $profile->add_attribute(new CmsContentTypeProfileAttribute($name,'main',$priority));
  }

  /**
   * Get the list of tab names for this content type
   * from the content type profile.
   *
   * Other methods may override this function for
   * backwards compatibility
   */
  public function TabNames()
  {
	  $editor = $this->get_editor();
	  return $editor->get_tab_names();
  }


  /**
   * Get all of the fields for a particular tab as an array of arrays
   * 
   */
  public function EditAsArray($adding = false,$tab = 0,$showadmin = false)
  {
    return array(array('Error','Edit Not Defined!'));
  }


  protected function display_attributes($adding,$negative = 0)
  {
	  $editor = $this->get_editor();
	  $idx = 0;
	  if( $negative ) $idx = 1;
	  return $editor->get_tab_elements($idx,$adding);
  }

} // end of class


#
# EOF
#
?>