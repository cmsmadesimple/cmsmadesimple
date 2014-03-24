<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
#Visit our homepage at: http://cmsmadesimple.org
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
#$Id$

/**
 * Class definition and methods for the Separator content type
 *
 * @package CMS
 * @subpackage content_types
 * @license GPL
 */

/**
 * Implements the CMS Made Simple Separator content type
 *
 * A separator is used simply for navigations to provide a visual separation between menu items.  Typically
 * as a horizontal or vertical bar.
 *
 * @package CMS
 * @subpackage content_types
 * @license GPL
 */
class Separator extends ContentBase
{

    function SetProperties()
    {
		parent::SetProperties();
		$this->RemoveProperty('secure',0);
		$this->RemoveProperty('template','-1');
		$this->RemoveProperty('alias','');
		$this->RemoveProperty('title','');
		$this->RemoveProperty('menutext','');
		$this->RemoveProperty('target','');
		$this->RemoveProperty('accesskey','');
		$this->RemoveProperty('titleattribute','');
		$this->RemoveProperty('cachable',true);
		$this->RemoveProperty('page_url','');
		$this->RemoveProperty('tabindex','');
    }

    function GetURL($rewrite = true) { return '#';  }
	function IsViewable() { return FALSE; }
    function FriendlyName() { return lang('contenttype_separator'); }
    function HasUsableLink() { return false; }
    function WantsChildren() { return false; }
    function RequiresAlias() { return FALSE; }
	public function HasSearchableContent() { return FALSE; }

    function TabNames()
    {
		$res = array(lang('main'));
		if( check_permission(get_userid(),'Manage All Content') ) $res[] = lang('options');
		return $res;
    }

    function EditAsArray($adding = false, $tab = 0, $showadmin = false)
    {
		switch($tab) {
		case '0':
			return $this->display_attributes($adding);
			break;
		case '1':
			return $this->display_attributes($adding,1);
			break;
		}
    }

    function ValidateData()
    {
		$this->mName = CMS_CONTENT_HIDDEN_NAME;
		return parent::ValidateData();
    }

}

# vim:ts=4 sw=4 noet
?>
