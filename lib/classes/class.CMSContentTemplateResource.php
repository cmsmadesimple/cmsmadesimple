<?php
#CMS - CMS Made Simple
#(c)2004-2012 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://www.cmsmadesimple.org
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
#$Id: content.functions.php 6863 2011-01-18 02:34:48Z calguy1000 $

/**
 * @package CMS
 */

/**
 * A simple class for handling content templates (page template) smarty resources.
 *
 * @package CMS
 * @author Robert Campbell
 * @internal
 * @ignore
 * @copyright Copyright (c) 2012, Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since 1.11
 */
class CMSContentTemplateResource extends CMS_Fixed_Resource_Custom
{
  protected function fetch($name,&$source,&$mtime)
  {
    $gCms = cmsms();
    $config = $gCms->GetConfig();
    $contentobj = $gCms->variables['content_obj'];

    if (!is_object($contentobj))
      {
	// We've a custom error message...  return it here
	header("HTTP/1.0 404 Not Found");
	header("Status: 404 Not Found");
	if ($name == 'content_en')
	  $source = get_site_preference('custom404');
	else
	  $source = null;
	$mtime = time();
	return;
      }
    else if( isset($_SESSION['cms_preview_data']) && $contentobj->Id() == '__CMS_PREVIEW_PAGE__' )
      {
	if( !isset($_SESSION['cms_preview_data']['content_obj']) )
	  {
	    $contentops = $gCms->GetContentOperations();
	    $_SESSION['cms_preview_data']['content_obj'] = $contentops->LoadContentFromSerializedData($_SESSION['cms_preview_data']);
	    $contentobj =& $_SESSION['cms_preview_data']['content_obj'];
	  }
	$contentobj =& $_SESSION['cms_preview_data']['content_obj'];
	$source = $contentobj->Show($name);
	$mtime = $contentobj->GetModifiedDate();

	// So no one can do anything nasty, take out the php smarty tags.  Use a user
	// defined plugin instead.
	if (!(isset($config["use_smarty_php_tags"]) && $config["use_smarty_php_tags"] == true))
	  {
	    $source = preg_replace("/\{\/?php\}/", "", $source);
	  }
	return;
      }
    else
      {
	if (isset($contentobj) && $contentobj !== FALSE)
	  {
	    $source = $contentobj->Show($name);
	    $mtime = $contentobj->GetModifiedDate();

	    // So no one can do anything nasty, take out the php smarty tags.  Use a user
	    // defined plugin instead.
	    if (!(isset($config["use_smarty_php_tags"]) && $config["use_smarty_php_tags"] == true))
	      {
		$source = preg_replace("/\{\/?php\}/", "", $source);
	      }
					
	    return;
	  }
      }
    $source = null;
    $mtime = null;
    return;
  }
} // end of class


#
# EOF
#
?>