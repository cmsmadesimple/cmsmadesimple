<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
# CMS - CMS Made Simple
# (c)2004-2009 by Ted Kulp (ted@cmsmadesimple.org)
# This project's homepage is: http://cmsmadesimple.org
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# BUT withOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

class Content extends CmsContentBase
{
    var $stylesheet;
	
    function __construct()
    {
		parent::__construct();

		#Turn on preview
		$this->SetPreviewAble(true);

	}

    function IsCopyable()
    {
        return TRUE;
    }

    function FriendlyName()
    {
      return lang('contenttype_content');
    }


    function Show($param = 'content_en')
    {
	  // check for additional content blocks
	  return $this->GetPropertyValue($param);
    }

    function IsDefaultPossible()
    {
	  return TRUE;
    }


    function EditAsArray($adding = false, $tab = 0, $showadmin = false)
    {
	global $gCms;
	
	$config = $gCms->GetConfig();
	$templateops =& $gCms->GetTemplateOperations();
	$ret = array();
	$this->stylesheet = '';
	if ($this->TemplateId() > 0)
	{
	    $this->stylesheet = '../stylesheet.php?templateid='.$this->TemplateId();
	}


	if ($tab == 0)
	{
	  // now do basic attributes
	  $tmp = $this->display_attributes($adding);
	  if( !empty($tmp) )
	    {
	      foreach( $tmp as $one ) {
		$ret[] = $one;
	      }
	    }

	  // and the content blocks
	  foreach($this->_contentBlocks as $blockName => $blockInfo)
	    {
	      $this->AddExtraProperty($blockName);
	      $parameters[] = $blockInfo['id'];
	    }
	  
	  // add content blocks.
	  foreach($this->_contentBlocks as $blockName => $blockInfo)
	    {
	      $data = $this->GetPropertyValue($blockInfo['id']);
	      if( empty($data) && isset($blockInfo['default']) ) $data = $blockInfo['default'];
	      $tmp = $this->display_content_block($blockName,$blockInfo,$data,$adding);
	      if( !$tmp ) continue;
	      $ret[] = $tmp;
	    }
	}

	if ($tab == 1)
	{
	  // now do advanced attributes
	  $tmp = $this->display_attributes($adding,1);
	  if( !empty($tmp) )
	    {
	      foreach( $tmp as $one ) {
		$ret[] = $one;
	      }
	    }

	    $tmp = get_preference(get_userid(),'date_format_string','%x %X');
	    if( empty($tmp) ) $tmp = '%x %X';
	    $ret[]=array(lang('last_modified_at').':', strftime($tmp, strtotime($this->GetModifiedDate()) ) );
	    $userops =& $gCms->GetUserOperations();
	    $modifiedbyuser = $userops->LoadUserByID($this->LastModifiedBy());
	    if($modifiedbyuser) $ret[]=array(lang('last_modified_by').':', $modifiedbyuser->username); 
	}

	return $ret;
    }


	protected function validate()
	{
		parent::validate();
		if( $this->template_id() < 0 )
			{
				$this->add_validation_error(lang('nofieldgiven',array(lang('template'))));
			}

		$tmp = $this->get_property_value('content_en');
		if( $tmp == '' )
			{
				$this->add_validation_error(lang('nofieldgiven',array(lang('content'))));
			}


	}

} // end of class

# vim:ts=4 sw=4 noet
?>
