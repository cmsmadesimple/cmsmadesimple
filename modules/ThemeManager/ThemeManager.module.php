<?php
#-------------------------------------------------------------------------
# Module: ThemeManager - a module for importing and exporting template
#   and stylesheet packages.
# Version: 1.0.8, Robert Campbell <rob@techcom.dyndns.org>
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------

define( "DTD_VERSION", "1.2" );

function get_urls($string, $strict=true) {
  $types = array("href", "src", "url");
  while(list(,$type) = each($types)) {
    $innerT = $strict?'[a-z0-9:?=&@/._-]+?':'.+?';
    preg_match_all ("|$type\=([\"'`])(".$innerT.")\\1|i", $string, $matches);
    $ret[$type] = $matches[2];
  }
  
  return $ret;
};

#-------------------------------------------------------------------------
class ThemeManager extends CMSModule
{
  var $dtd = '
<!DOCTYPE theme [
  <!ELEMENT theme (name,dtdversion,template+,stylesheet+,assoc+,reference*,mmtemplate*)>
  <!ELEMENT name (#PCDATA)>
  <!ELEMENT dtdversion (#PCDATA)>
  <!ELEMENT template (tname,tencoding,tdata)>
  <!ELEMENT tname (#PCDATA)>
  <!ELEMENT tencoding (#PCDATA)>
  <!ELEMENT tdata (#PCDATA)>
  <!ELEMENT stylesheet (cssname,cssmediatype,cssencoding,cssdata)>
  <!ELEMENT cssname (#PCDATA)>
  <!ELEMENT cssmediatype (#PCDATA)>
  <!ELEMENT cssdata (#PCDATA)>
  <!ELEMENT assoc (assoc_tname,assoc_cssname)>
  <!ELEMENT assoc_tname (#PCDATA)>
  <!ELEMENT assoc_cssname (#PCDATA)>
  <!ELEMENT reference (refname,refencoded,reflocation,refdata)>
  <!ELEMENT refname (#PCDATA)>
  <!ELEMENT refencoded (#PCDATA)>
  <!ELEMENT reflocation (#PCDATA)>
  <!ELEMENT refdata (#PCDATA)>
  <!ELEMENT mmtemplate (mmtemplate_name,mmtemplate_data)>
  <!ELEMENT mmtemplate_name (#PCDATA)>
  <!ELEMENT mmtemplate_data (#PCDATA)>
]>';

  /*---------------------------------------------------------
   GetName()
   ---------------------------------------------------------*/
  function GetName()
  {
    return 'ThemeManager';
  }


  /*---------------------------------------------------------
   GetFriendlyName()
   ---------------------------------------------------------*/
  function GetFriendlyName()
  {
    return $this->Lang('friendlyname');
  }

	
  /*---------------------------------------------------------
   GetVersion()
   ---------------------------------------------------------*/
  function GetVersion()
  {
    return '1.1.7';
  }


  function LazyLoadAdmin()
  {
    return TRUE;
  }

  /*---------------------------------------------------------
   MinimumCVSVersion()
   ---------------------------------------------------------*/
  function MinimumCMSVersion()
  {
    return "1.10.3";
  }


  /*---------------------------------------------------------
   GetHelp()
   ---------------------------------------------------------*/
  function GetHelp()
  {
    return $this->Lang('help');
  }


  /*---------------------------------------------------------
   GetAuthor()
   ---------------------------------------------------------*/
  function GetAuthor()
  {
    return 'calguy1000';
  }


  /*---------------------------------------------------------
   GetAuthorEmail()
   This returns a string that is presented in the Module
   Admin if you click on the "About" link. It helps users
   of your module get in touch with you to send bug reports,
   questions, cases of beer, and/or large sums of money.
   ---------------------------------------------------------*/
  function GetAuthorEmail()
  {
    return 'calguy1000@hotmail.com';
  }


  /*---------------------------------------------------------
   GetChangeLog()
   This returns a string that is presented in the module
   Admin if you click on the About link. It helps users
   figure out what's changed between releases.
   See the note on localization at the top of this file.
   ---------------------------------------------------------*/
  function GetChangeLog()
  {
    return $this->ProcessTemplate('changelog.tpl');
  }


  /*---------------------------------------------------------
   IsPluginModule()
   ---------------------------------------------------------*/
  function IsPluginModule()
  {
    return false;
  }


  /*---------------------------------------------------------
   HasAdmin()
   ---------------------------------------------------------*/
  function HasAdmin()
  {
    return true;
  }


  /*---------------------------------------------------------
   IsAdminOnly()
   ---------------------------------------------------------*/
  function IsAdminOnly()
  {
    return true;
  }


  /*---------------------------------------------------------
   GetAdminSection()
   ---------------------------------------------------------*/
  function GetAdminSection()
  {
    return 'layout';
  }


  /*---------------------------------------------------------
   GetAdminDescription()
   ---------------------------------------------------------*/
  function GetAdminDescription()
  {
    return $this->Lang('moddescription');
  }


  /*---------------------------------------------------------
   VisibleToAdminUser()
   ---------------------------------------------------------*/
  function VisibleToAdminUser()
  {
    return $this->CheckPermission('Manage Themes') ||
      ($this->CheckPermission('Add Stylesheets') &&
      $this->CheckPermission('Add Templates') &&
      $this->CheckPermission('Add Stylesheet Assoc'));
  }

  /*---------------------------------------------------------
   GetDependencies()
   ---------------------------------------------------------*/
  function GetDependencies()
  {
    return array();
  }


  /*---------------------------------------------------------
   Install()
   ---------------------------------------------------------*/
  function Install()
  {
    // this module does not have any tables of its own

    // create a permission
    $this->CreatePermission('Manage Themes', 'Manage Themes');
  }


  /*---------------------------------------------------------
   InstallPostMessage()
   ---------------------------------------------------------*/
  function InstallPostMessage()
  {
    return $this->Lang('postinstall');
  }


  /*---------------------------------------------------------
   Upgrade()
   ---------------------------------------------------------*/
  function Upgrade($oldversion, $newversion)
  {
    // nothing to do here, yet
  }
	
	
  /*---------------------------------------------------------
   Uninstall()
   ---------------------------------------------------------*/
  function Uninstall()
  {
    // remove the permissions
    $this->RemovePermission('Manage Themes');
  }


  /*---------------------------------------------------------
   DisplayErrorPage($id, $params, $return_id, $message)
   NOT PART OF THE MODULE API
   ---------------------------------------------------------*/
  function DisplayErrorPage($id, &$params, $returnid, $message='')
  {
    /*$this->smarty->assign('title_error', $this->Lang('error'));
    if ($message != '')
      {
	$this->smarty->assign('message', $message);
      }

    // Display the populated template
    echo $this->ProcessTemplate('error.tpl');*/
    echo $this->ShowErrors($message);
  }


  /*---------------------------------------------------------
    _mkdirr( $pathname, $mode )
    NOT PART OF THE MODULE API

    Make a directory recursively
    ---------------------------------------------------------*/
  function _mkdirr ($pathname, $mode = 0777)
  {
    // Check if directory already exists
    if (is_dir ($pathname) || empty ($pathname))
    {
      return true;
    }

    // Ensure a file does not already exist with the same name
    if (is_file ($pathname))
    {
      // RC: Modification such that this isn't an error
      return true;
    }

    // Crawl up the directory tree
    $next_pathname =
      substr ($pathname, 0, strrpos ($pathname, DIRECTORY_SEPARATOR));
    if ($this->_mkdirr ($next_pathname, $mode))
    {
      if (!file_exists ($pathname))
      {
	 return @mkdir ($pathname, $mode);
      }
    }

    return false;
  }

}

?>