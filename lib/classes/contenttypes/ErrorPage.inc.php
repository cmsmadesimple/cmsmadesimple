<?php
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

require_once('Content.inc.php');
class ErrorPage extends Content
{
  var $error_types;
	
  public function __construct()
  {
    $this->ContentBase();
    $this->error_types = array('404' => lang('404description'));
    $this->set_alias('error404');
  }

  function handles_alias()
  {
    return true;
  }

  public function friendly_name()
  {
    return lang('contenttype_errorpage');
  }
	
//   function SetProperties()
//   {
//     parent::SetProperties();
//     $this->RemoveProperty('searchable',false);
//     $this->RemoveProperty('parent',-1);
//     $this->RemoveProperty('showinmenu',false);
//     $this->RemoveProperty('menutext','');
//     $this->RemoveProperty('target','');
//     $this->RemoveProperty('extra1','');
//     $this->RemoveProperty('extra2','');
//     $this->RemoveProperty('extra3','');
//     $this->RemoveProperty('image','');
//     $this->RemoveProperty('thumbnail','');
//     $this->RemoveProperty('accesskey','');
//     $this->RemoveProperty('titleattribute','');
//     $this->RemoveProperty('secure',false);
//     $this->RemoveProperty('active',true);
//     $this->RemoveProperty('cachable',false);

//     $this->RemoveProperty('alias','');
//     $this->AddBaseProperty('alias',10,1);

//     #Turn on preview
//     $this->mPreview = true;
//   }

  public function is_copyable()
  {
    return FALSE;
  }

  public function is_default_possible()
  {
    return FALSE;
  }

  function has_usable_link()
  {
    return false;
  }

  function wants_children()
  {
    return false;
  }

  function WantsChildren()
  {
    return false;
  }
	
  function is_system_page()
  {
    return true;
  }


//   function ValidateData()
//   {
//     $errors = parent::ValidateData();
//     if ($errors == FALSE)
//       {
// 	$errors = array();
//       }
    
//     //Do our own alias check
//     if ($this->Alias() == '')
//       {
// 	$errors[] = lang('nofieldgiven', array(lang('error_type')));
//       }
//     else if (in_array($this->Alias(), $this->error_types))
//       {
// 	$errors[] = lang('nofieldgiven', array(lang('error_type')));
//       }
//     else if ($this->Alias() != $this->OldAlias())
//       {
// 	global $gCms;
// 	$contentops =& $gCms->GetContentOperations();
// 	$error = $contentops->CheckAliasError($this->Alias(), $this->Id());
// 	if ($error !== FALSE)
// 	  {
// 	    if ($error == lang('aliasalreadyused'))
// 	      {
// 		$errors[] = lang('errorpagealreadyinuse');
// 	      }
// 	    else
// 	      {
// 		$errors[] = $error;
// 	      }
// 	  }
//       }
    
//     return (count($errors) > 0 ? $errors : FALSE);
//   }
}

# vim:ts=4 sw=4 noet
?>
