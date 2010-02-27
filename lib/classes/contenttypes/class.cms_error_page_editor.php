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

class CmsErrorPageEditor extends CmsContentEditor
{
  public function __construct($content_obj)
  {
    parent::__construct($content_obj);

    $profile = $this->get_profile();
	$profile->remove_by_name('menu_text');
	$profile->remove_by_name('parent_id');
	$profile->remove_by_name('active');
	$profile->remove_by_name('show_in_menu');
    $profile->remove_by_name('cachable');
	$profile->remove_by_name('secure');
	$profile->remove_by_name('class_name');
	$profile->remove_by_name('target');
	$profile->remove_by_name('title_attribute');
	$profile->remove_by_name('access_key');
	$profile->remove_by_name('tab_index');
	$profile->remove_by_name('image');
	$profile->remove_by_name('thumbnail');
	$profile->remove_by_name('extra1');
	$profile->remove_by_name('extra2');
	$profile->remove_by_name('extra3');
	$profile->remove_by_name('searchable');

	// alias gets moved to the main tab.
	//$profile->remove_by_name('alias');
	$profile->add_attribute(new CmsContentTypeProfileAttribute('alias','main'));
  }

  public function validate()
  {
	  // here we make sure that all the attributes we've disabled
	  // are set to the appropriate values.
	  $content_obj = $this->get_content();
	  $content_obj->set_menu_text('----');
	  $content_obj->set_parent_id('-1');
	  $content_obj->set_active(1);
	  $content_obj->set_show_in_menu(0);
	  $content_obj->set_cachable(0);
	  $content_obj->set_secure(0);
	  $content_obj->set_target('_none');
	  $content_obj->set_searchable(0);
	  
	  die($content_obj->alias());
	  if( $content_obj->alias() == '' )
		  {
			  die('no alias');
		  }
	  return parent::validate();
  }

  protected function get_single_element($content_obj,&$attr,$adding = false)
  {
	  $prompt = '';
	  $field = '';
	  switch( $attr->get_name() )
	  {
	  case 'alias':
		  {
			  $prompt = lang('error_type');
			  $dropdownopts = '<option value="">'.lang('none').'</option>';
			  foreach ($content_obj->error_types as $code=>$name)
				  {
					  $dropdownopts .= '<option value="error' . $code . '"';
					  if ('error'.$code == $content_obj->alias)
						  {
							  $dropdownopts .= ' selected="selected" ';
						  }
					  $dropdownopts .= ">{$name} ({$code})</option>";
				  }
			  $field = '<select name="alias">'.$dropdownopts.'</select>';
		  }
		  break;

	  default:
		  return parent::get_single_element($content_obj,$attr,$adding);
	  }

	  if( !empty($prompt) && !empty($field) )
	  {
		  return array($prompt.':',$field);
	  }

  }
} // end of class.

#
# EOF
#
?>