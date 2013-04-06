<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
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
 * A simple class for handling page template resources.
 *
 * @package CMS
 * @author Robert Campbell
 * @internal
 * @ignore
 * @copyright Copyright (c) 2012, Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since 1.11
 */
class CMSPageTemplateResource extends CMS_Fixed_Resource_Custom
{
	static private $_templates;
	private $_section;

	public function __construct($section = '')
	{
		if( in_array($section,array('top','head','body')) ) {
			$this->_section = $section;
		}
	}

	protected function buildUniqueResourceName(Smarty $smarty,$resource_name)
	{
		return parent::buildUniqueResourceName($smarty,$resource_name).'--'.$this->_section;
	}

	private function get_template($name)
	{
		if( !is_array(self::$_templates) || !isset(self::$_templates[$name]) ) {
			if( !is_array(self::$_templates) ) self::$_templates = array();

			$templateops = cmsms()->GetTemplateOperations();
			if( $name == -1 ) {
				$templateobj = $templateops->LoadDefaultTemplate();
			}
			else {
				$templateobj = $templateops->LoadTemplateByID($name);
			}

			if( is_object($templateobj) && $templateobj !== FALSE ) {
				$name = $templateobj->name;
				self::$_templates[$name] = $templateobj;
			}
			else {
				debug_display('no template '.$name);
			}
		}
		
		if( isset(self::$_templates[$name]) ) {
			return self::$_templates[$name];
		}
	}

	protected function fetch($name,&$source,&$mtime)
	{
		if( is_sitedown() && cmsms()->is_frontend_request() ) {
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

		$tpl = $this->get_template($name);
		if( !is_object($tpl) ) return;

		// Get section, do magic.
		switch( $this->_section ) {
			case 'top':
				$mtime = $tpl->modified_date;
				$pos1 = stripos($tpl->content,'<head');
				$pos2 = stripos($tpl->content,'<header');
				if( $pos1 === FALSE || $pos1 == $pos2 ) return;
				$source = substr($tpl->content,0,$pos1);
				return;

			case 'head':
				$mtime = $tpl->modified_date;
				$pos1 = stripos($tpl->content,'<head');
				$pos1a = stripos($tpl->content,'<header');
				$pos2 = stripos($tpl->content,'</head>');
				if( $pos1 === FALSE || $pos1 == $pos1a || $pos2 === FALSE ) return;
				$source = substr($tpl->content,$pos1,$pos2-$pos1+7);
				return;

			case 'body':
				$mtime = $tpl->modified_date;
				$pos = stripos($tpl->content,'</head>');
				if( $pos !== FALSE ) {
					$source = substr($tpl->content,$pos+7);
				}
				else {
					$source = $tpl->content;
				}
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
# vim:ts=4 sw=4 noet
?>