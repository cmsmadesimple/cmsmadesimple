<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
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
#$Id: Link.inc.php 4225 2007-10-14 16:31:00Z calguy1000 $

/**
 * Class definition and methods for Page Link content type
 *
 * @package CMS
 * @license GPL
 */

/**
 * Class for CMS Made Simple Page Link content type
 *
 * @package CMS
 * @version $Revision$
 * @license GPL
 */
class PageLink extends ContentBase
{

    function IsCopyable()
    {
        return TRUE;
    }

    function IsViewable()
    {
      return FALSE;
    }

    function FriendlyName()
    {
      return lang('contenttype_pagelink');
    }

// calguy1000: commented this out so that this page can be seen in cms_selflink
// but not sure what it's gonna mess up.
//     function HasUsableLink()
//     {
//       return false;
//     }

    function SetProperties()
    {
      parent::SetProperties();
      $this->RemoveProperty('cachable',1);
      //$this->RemoveProperty('showinmenu',1);
      $this->RemoveProperty('secure',0);
      $this->AddContentProperty('page',10,1,'int');
      $this->AddContentProperty('params',10,1);
		
      //Turn off caching
      $this->mCachable = false;
    }

    function FillParams($params,$editing = false)
    {
		parent::FillParams($params,$editing);

      if (isset($params))
	{
	  $parameters = array('page', 'params' );
	  foreach ($parameters as $oneparam)
	    {
	      if (isset($params[$oneparam]))
		{
		  $this->SetPropertyValue($oneparam, $params[$oneparam]);
		}
	    }
	}
    }

    function ValidateData()
    {
      $errors = parent::ValidateData();
      if( $errors === FALSE )
	{
	  $errors = array();
	}

      $gCms = cmsms();
      $contentops =& $gCms->GetContentOperations();		
		
      $page = $this->GetPropertyValue('page');
      if ($page == '-1')
	{
	  $errors[]= lang('nofieldgiven',array(lang('page')));
	  $result = false;
	}

      // get the content type of page.
      else
	{
	  $destobj =& $contentops->LoadContentFromID($page);
	  if( !is_object($destobj) )
	    {
	      $errors[] = lang('destinationnotfound');
	      $result = false;
	    }
	  else if( $destobj->Type() == 'pagelink' )
	    {
	      $errors[] = lang('pagelink_circular');
	      $result = false;
	    }
	  else if( $destobj->Alias() == $this->mAlias )
	    {
	      $errors[] = lang('pagelink_circular');
	      $result = false;
	    }
	}
      
      return (count($errors) > 0?$errors:FALSE);
    }

    function TabNames()
    {
      $res = array(lang('main'));
      if( check_permission(get_userid(),'Manage All Content') )
	{
	  $res[] = lang('options');
	}
      return $res;
    }

    function display_single_element($one,$adding)
    {
      switch($one) {
      case 'page':
	{
	  $gCms = cmsms();
	  $contentops =& $gCms->GetContentOperations();
	  
	  $tmp = $contentops->CreateHierarchyDropdown($this->mId, 
						      $this->GetPropertyValue('page'), 'page', 1, 0, 0, 0);
	  if( !empty($tmp) ) return array(lang('destination_page').':',$tmp);
	}
	break;
	
      case 'params':
	{
	  $val = cms_htmlentities($this->GetPropertyValue('params'));
	  return array(lang('additional_params').':','<input type="text" name="params" value="'.$val.'" />');
	}
	break;

      default:
	return parent::display_single_element($one,$adding);
      }
    }

    function EditAsArray($adding = false, $tab = 0, $showadmin = false)
    {
      switch($tab)
	{
	case '0':
	  return $this->display_attributes($adding);
	  break;
	case '1':
	  return $this->display_attributes($adding,1);
	  break;
	}
    }

    function GetURL($rewrite = true)
    {
      $page = $this->GetPropertyValue('page');
      $params = $this->GetPropertyValue('params');
      
      $gCms = cmsms();
      $contentops =& $gCms->GetContentOperations();
      $destcontent =& $contentops->LoadContentFromId($page);
      if( is_object( $destcontent ) ) 
	{
	  $url = $destcontent->GetURL();
	  $url .= $params;
	  return $url;
	}
    } 
}

# vim:ts=4 sw=4 noet
?>
