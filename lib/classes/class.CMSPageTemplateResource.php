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

class CMSPageTemplateResource extends Smarty_Resource_Custom
{
  static private $_templates;
  private $_section;

  public function __construct($section)
  {
    parent::__construct();
    if( in_array($section,array('top','head','body')) )
      {
	$this->_section = $section;
      }
  }

  protected function buildUniqueResourceName(Smarty $smarty,$resource_name)
  {
    return parent::buildUniqueResourceName($smarty,$resource_name).'--'.$this->_section;
  }

  private function get_template($name)
  {
    if( !is_array(self::$_templates) || !isset(self::$_templates[$name]) )
      {
	if( !is_array(self::$_templates) ) self::$_templates = array();
	global $gCms;
	$templateops = $gCms->GetTemplateOperations();
	$templateobj = $templateops->LoadTemplateByID($name);
	if (isset($templateobj) && $templateobj !== FALSE)
	  {
	    self::$_templates[$name] = $templateobj;
	  }
      }
    if( isset(self::$_templates[$name]) )
      {
	return self::$_templates[$name];
      }
  }

  protected function fetch($name,&$source,&$mtime)
  {
    if( is_sitedown() )
      {
	header('HTTP/1.0 503 Service Unavailable');
	header('Status: 503 Service Unavailable');

	$source = get_site_preference('sitedownmessage');
	$mtime = time();
	return;
      }

    if( $name == 'notemplate' ) return '{content}';

    $source = null;
    $mtime = null;
    
    $tpl = $this->get_template($name);
    if( !is_object($tpl) ) return;

    switch( $this->_section )
      {
      case 'top':
	$pos = stripos($tpl->content,'<head>');
	if( $pos === FALSE ) return;
	$source = substr($tpl->content,0,$pos);
	$mtime = $tpl->modified_date;
	return;

      case 'head':
	$pos1 = stripos($tpl->content,'<head');
	$pos2 = stripos($tpl->content,'</head>');
	if( $pos1 === FALSE || $pos2 === FALSE ) return;
	$source = substr($tpl->content,$pos1,$pos2-$pos1+7);
	$mtime = $tpl->modified_date;
	return;

      case 'body':
	$pos = stripos($tpl->content,'</head>');
	if( $pos === FALSE ) return;
	$source = substr($tpl->content,$pos+7);
	$mtime = $tpl->modified_date;
	return;

      default:
	$source = $tpl->content;
	$mtime = $tpl->modified_date;
	return;
      }
  }
} // end of class

#
# EOF
#
?>