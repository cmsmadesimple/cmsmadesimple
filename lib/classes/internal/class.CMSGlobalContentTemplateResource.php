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
 * A simple class to handle fetching global content resources.
 * 
 * @ignore
 * @internal
 * @since 1.11
 * @package CMS
 */
class CMSGlobalContentTemplateResource extends CMS_Fixed_Resource_Custom
{
  protected function fetch($name,&$source,&$mtime)
  {
    debug_buffer('start global_content_get_template');
    $gCms = cmsms();
    $config = $gCms->GetConfig();
    $gcbops = $gCms->GetGlobalContentOperations();

    $oneblob = $gcbops->LoadHtmlBlobByName($name);
    if ($oneblob)
      {
	$text = $oneblob->content;
	$source = $text;
	$mtime = $oneblob->modified_date;

	// So no one can do anything nasty, take out the php smarty tags.  Use a user
	// defined plugin instead.
	if (!(isset($config["use_smarty_php_tags"]) && $config["use_smarty_php_tags"] == true))
	  {
	    $source = preg_replace("/\{\/?php\}/", "", $source);
	  }
      }
    else
      {
	$source = "<!-- Html blob '" . $name . "' does not exist  -->";
		// put mention into the admin log
		audit('', 'Global Content Block: '.$name , 'Can not open or does not exist!');
	$mtime = time();
      }
    debug_buffer('end global_content_get_template');
    return true;
  }
} // end of class


#
# EOF
#
?>