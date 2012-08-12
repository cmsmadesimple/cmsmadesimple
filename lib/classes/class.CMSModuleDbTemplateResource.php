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
 * A simple class to resolve an issue with smarty
 * 
 * @since 1.11
 * @package CMS
 */
abstract class CMS_Fixed_Resource_Custom extends Smarty_Resource_Custom
{
  public function populate(Smarty_Template_Source $source, Smarty_Internal_Template $_template = null)
  {
        $source->filepath = $source->type . ':' . $source->name;
        $source->uid = sha1($source->type . ':' . $source->name);

        $mtime = $this->fetchTimestamp($source->name);
        if ($mtime !== null) {
            $source->timestamp = $mtime;
        } else {
            $this->fetch($source->name, $content, $timestamp);
            $source->timestamp = isset($timestamp) ? $timestamp : false;
            if( isset($content) ) $source->content = $content;
        }
        $source->exists = !!$source->timestamp;
  }
}

/**
 * A simple class to handle a module database template.
 * 
 * @ignore
 * @internal
 * @since 1.11
 * @package CMS
 */
class CMSModuleDbTemplateResource extends CMS_Fixed_Resource_Custom
{
  protected function fetch($name,&$source,&$mtime)
  {
    debug_buffer('','CMSModuleDbTemplateResource start'.$name);
    $db = cmsms()->GetDb();
    
    $tmp = explode(';',$name);
    $query = "SELECT * from ".cms_db_prefix()."module_templates WHERE module_name = ? and template_name = ?";
    $row = $db->GetRow($query, preg_split('/;/', $name));
    if ($row) {
      $source = $row['content'];
      $mtime = $db->UnixTimeStamp($row['modified_date']);
    }
    debug_buffer('','CMSModuleDbTemplateResource end'.$name);
  }
} // end of class


/**
 * A simple class to handle a module file template.
 * 
 * @ignore
 * @internal
 * @package CMS
 * @since 1.11
 */
class CMSModuleFileTemplateResource extends CMS_Fixed_Resource_Custom
{
  protected function fetch($name,&$source,&$mtime)
  {
    $source = null;
    $mtime = null;
    $params = preg_split('/;/', $name);
    if( count($params) != 2 ) return;

    $config = cmsms()->GetConfig();
    $files = array();
    $files[] = cms_join_path($config['root_path'],'module_custom',$params[0],'templates',$params[1]);
    $files[] = cms_join_path($config['root_path'],'modules',$params[0],'templates',$params[1]);
		
    foreach( $files as $one ) {
      if( file_exists($one) ) {
			
	$source = @file_get_contents($one);
	$mtime = @filemtime($one);
	return;
      }
    }
  }
} // end of class


#
# EOF
#
?>