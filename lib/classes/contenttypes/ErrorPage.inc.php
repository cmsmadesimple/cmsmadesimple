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
 * Define the error page content type
 * @package CMS
 * @license GPL
 */

/**
 * Implement the ErrorPage content type
 *
 * Error pages are not navigble, do not have their own URLS and typically do not appear in navigations.
 * They are used for handling different types of errors.
 *
 * @package CMS
 * @license GPL
 */
class ErrorPage extends Content
{
	/**
	 * @ignore
	 */
	protected $doAliasCheck;

	/**
	 * @ignore
	 */
	protected $error_types;

	/**
	 * @ignore
	 */
	public function __construct()
	{
		parent::__construct();

		global $CMS_ADMIN_PAGE;
		if( isset($CMS_ADMIN_PAGE) ) $this->error_types = array('404' => lang('404description'));
		$this->doAliasCheck = false;
		$this->doAutoAliasIfEnabled = false;
		$this->mType = strtolower(get_class($this)) ;
	}

	/**
	 * The ErrorPage content type handles, and requires an alias
	 *
	 * @return true
	 */
	public function HandlesAlias() { return true; }

	/**
	 * Return the friendly name for this content type
	 *
	 * @return string
	 */
	public function FriendlyName() { return lang('contenttype_errorpage');	}

	/**
	 * This content type supports preview
	 *
	 * @return true
	 */
	public function HasPreview() {	return TRUE; }

	/**
	 * This content type is not copyable
	 *
	 * @return false
	 */
	public function IsCopyable() { return FALSE; }

	/**
	 * Pages using this content type cannot be the default.
	 *
	 * @return false
	 */
	public function IsDefaultPossible() { return FALSE; }

	/**
	 * This content type has no usable link and cannot be navigated to
	 *
	 * @return false
	 */
	public function HasUsableLink() { return false; }

	/**
	 * This content type does not want any children
	 *
	 * @return false
	 */
	public function WantsChildren() { return false; }

	/**
	 * This content type does create system pages
	 *
	 * @return true
	 */
	public function IsSystemPage() { return true; }

	/**
	 * This content type does not generate content that can be cached
	 *
	 * @return false
	 */
	public function HasSearchableContent() { return FALSE; }

	/**
	 * Setup the properties for this content type
	 * many properties are disabled so that they will not appear in the edit form
	 */
	function SetProperties()
	{
		parent::SetProperties();
		$this->RemoveProperty('secure',0);
		$this->RemoveProperty('parent',-1);
		$this->RemoveProperty('showinmenu',false);
		$this->RemoveProperty('menutext','');
		$this->RemoveProperty('target','');
		$this->RemoveProperty('extra1','');
		$this->RemoveProperty('extra2','');
		$this->RemoveProperty('extra3','');
		$this->RemoveProperty('image','');
		$this->RemoveProperty('thumbnail','');
		$this->RemoveProperty('accesskey','');
		$this->RemoveProperty('titleattribute','');
		$this->RemoveProperty('active',true);
		$this->RemoveProperty('cachable',false);
		$this->RemoveProperty('page_url','');

		$this->RemoveProperty('alias','');
		$this->AddBaseProperty('alias',10,1);
	}

	/**
	 * Fill the content object from the edit form
	 *
	 * @param array $params
	 * @param bool  $editing  Are we in an add or edit operation
	 */
	function FillParams($params,$editing = false)
	{
		parent::FillParams($params,$editing);
		$this->mParentId = -1;
		$this->mShowInMenu = false;
		$this->mCachable = false;
		$this->mActive = true;
	}

	/**
	 * Display a single element on the edit form
	 *
	 * @param string $one The property name
	 * @param bool   $adding
	 * @return string[] An array of 2 strings.  The first element is the prompt, the second element is the input field.
	 */
    function display_single_element($one,$adding)
    {
		switch($one) {
		case 'alias':
			$dropdownopts = '';
			//$dropdownopts = '<option value="">'.lang('none').'</option>';
			foreach ($this->error_types as $code=>$name) {
				$dropdownopts .= '<option value="error' . $code . '"';
				if ('error'.$code == $this->mAlias)	$dropdownopts .= ' selected="selected" ';
				$dropdownopts .= ">{$name} ({$code})</option>";
			}
			return array(lang('error_type').':', '<select name="alias">'.$dropdownopts.'</select>');
			break;

		default:
			return parent::display_single_element($one,$adding);
		}
    }


	/**
	 * Validate the object to ensure correctness
	 * @return string[]|false False indicates no validation errors.  Otherwise an array of strings is returned.
	 */
	function ValidateData()
	{
		// $this->SetPropertyValue('searchable',0);
		// force not searchable.
		$errors = parent::ValidateData();
		if ($errors == FALSE) $errors = array();

		//Do our own alias check
		if ($this->mAlias == '') {
			$errors[] = lang('nofieldgiven', array(lang('error_type')));
		}
		else if (in_array($this->mAlias, $this->error_types)) {
			$errors[] = lang('nofieldgiven', array(lang('error_type')));
		}
		else if ($this->mAlias != $this->mOldAlias) {
			$gCms = cmsms();
			$contentops = $gCms->GetContentOperations();
			$error = $contentops->CheckAliasError($this->mAlias, $this->mId);
			if ($error !== FALSE) {
				if ($error == lang('aliasalreadyused')) {
					$errors[] = lang('errorpagealreadyinuse');
				}
				else {
					$errors[] = $error;
				}
			}
		}
		return (count($errors) > 0 ? $errors : FALSE);
	}
}

# vim:ts=4 sw=4 noet
?>