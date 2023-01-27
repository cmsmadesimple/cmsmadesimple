<?php
#CMS - CMS Made Simple
#(c)2004-2012 by Ted Kulp (wishy@users.sf.net)
#Visit our homepage at: http://www.cmsmadesimple.org
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
 * A simple class for handling layout templates as a resource.
 *
 * @package CMS
 * @author Robert Campbell
 * @internal
 * @ignore
 * @copyright Copyright (c) 2012, Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since 1.12
 */
class CmsTemplateResource extends CMS_Fixed_Resource_Custom
{
	private $_section;

	public function __construct($section = '')
	{
		if( in_array($section,array('top','head','body')) ) $this->_section = $section;
	}

	public function buildUniqueResourceName(Smarty $smarty,$resource_name, $is_config = false)
	{
		return parent::buildUniqueResourceName($smarty,$resource_name,$is_config).'--'.$this->_section;
	}

	private function &get_template($name)
	{
		$obj = CmsLayoutTemplate::load($name);
		$ret = new StdClass;
		$ret->modified = $obj->get_modified();
		$ret->content = $obj->get_content();
		return $ret;
	}

	protected function fetch($name,&$source,&$mtime)
	{
		if( is_sitedown() && CmsApp::get_instance()->is_frontend_request() ) {
			$source = '';
			$mtime = time();
			if( $this->_section == 'body' ) {
				header('HTTP/1.0 503 Service Unavailable');
				header('Status: 503 Service Unavailable');
				$source = get_site_preference('sitedownmessage');
			}
			return;
		}

		if( $name == 'notemplate' ) {
			$source = '{content}';
			$mtime = time(); // never cache...
			return;
		}
		else if( startswith($name,'appdata;') ) {
			$name = substr($name,8);
			$source = cms_utils::get_app_data($name);
			$mtime = time();
			return;
		}

		$source = '';
		$mtime = null;

		try {
			$tpl = $this->get_template($name);
			if( !is_object($tpl) ) return;
		}
		catch( Exception $e ) {
			audit('','Core','Missing Template: '.$name);
			return;
		}

		switch( $this->_section ) {
		case 'top':
			$mtime = $tpl->modified;
			$pos1 = stripos($tpl->content,'<head');
			$pos2 = stripos($tpl->content,'<header');
			if( $pos1 === FALSE || $pos1 == $pos2 ) return;
			$source = trim(substr($tpl->content,0,$pos1));
			return;

		case 'head':
			$mtime = $tpl->modified;
			$pos1 = stripos($tpl->content,'<head');
			$pos1a = stripos($tpl->content,'<header');
			$pos2 = stripos($tpl->content,'</head>');
			if( $pos1 === FALSE || $pos1 == $pos1a || $pos2 === FALSE ) return;
			$source = trim(substr($tpl->content,$pos1,$pos2-$pos1+7));
			return;

		case 'body':
			$mtime = $tpl->modified;
			$pos = stripos($tpl->content,'</head>');
			if( $pos !== FALSE ) {
				$source = trim(substr($tpl->content,$pos+7));
			}
			else {
				$source = $tpl->content;
			}
			return;

		default:
			$source = trim($tpl->content);
			$mtime = $tpl->modified;
			return;
		}
	}

	public static function page_type_lang_callback($key)
	{
		if( $key == CmsLayoutTemplateType::CORE ) return 'Core';
		return lang($key);
	}

	public static function generic_type_lang_callback($key)
	{
		if( $key == CmsLayoutTemplateType::CORE ) return 'Core';
		return lang($key);
	}

	public static function reset_page_type_defaults()
	{
		$config = \cms_config::get_instance();
		$file = cms_join_path($config['admin_path'],'templates','orig_page_template.tpl');
		$contents = '';
		if( is_file($file) ) $contents = @file_get_contents($file);
		return $contents;
	}

    public static function template_help_callback($typename)
    {
        $typename = trim($typename);
        if( $typename == 'generic' ) return;
        $key = 'tplhelp_'.$typename;
        return lang($key);
    }
} // end of class

#
# EOF
