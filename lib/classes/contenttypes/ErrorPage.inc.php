<?php
#CMS - CMS Made Simple
#(c)2004-2010 by Ted Kulp (ted@cmsmadesimple.org)
#This projects homepage is: http://cmsmadesimple.org
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
 * Main class for CMS Made Simple ErrorPage content type
 *
 * @package CMS
 * @version $Revision$
 * @license GPL
 */
class ErrorPage extends Content
{
    private $doAliasCheck;
    private $error_types;
    private $valid_aliases;

    public function __construct()
    {
        parent::__construct();

        global $CMS_ADMIN_PAGE;
        $this->error_types = ['404' => lang('404description'),
                              '403' => lang('403description'),
                              '503' => lang('503description') ];
        $this->valid_aliases = [ 'error404', 'error403', 'error503' ];
        $this->doAliasCheck = false;
        $this->doAutoAliasIfEnabled = false;
        $this->mType = strtolower(get_class($this));
    }

    public function HandlesAlias()
    {
        return true;
    }

    public function IsSearchable()
    {
        return false;
    }

    public function FriendlyName()
    {
        return lang('contenttype_errorpage');
    }

    public function SetProperties()
    {
        parent::SetProperties();
        $this->RemoveProperty('secure',0);
        $this->RemoveProperty('searchable',0);
        //$this->RemoveProperty('parent',-1);
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
        $this->AddProperty('alias',1,self::TAB_MAIN,true);

        #Turn on preview
        $this->mPreview = true;
    }

    public function IsCopyable()
    {
        return FALSE;
    }

    public function IsDefaultPossible()
    {
        return FALSE;
    }

    public function HasUsableLink()
    {
        return false;
    }

    public function WantsChildren()
    {
        return false;
    }

    public function IsSystemPage()
    {
        return true;
    }

    public function FillParams(array $params,bool $editing = false)
    {
        parent::FillParams($params,$editing);
        //$this->mParentId = -1;
        $this->mAlias = get_parameter_value($params,'alias');
        $this->mShowInMenu = false;
        $this->mCachable = false;
        $this->mActive = true;
    }

    public function display_single_element(string $one,bool $adding)
    {
        switch($one) {
            case 'alias':
                $dropdownopts = '';
                //$dropdownopts = '<option value="">'.lang('none').'</option>';
                foreach ($this->error_types as $code=>$name) {
                    $dropdownopts .= '<option value="error' . $code . '"';
                    if ('error'.$code == $this->mAlias) $dropdownopts .= ' selected="selected" ';
                    $dropdownopts .= ">{$name} ({$code})</option>";
                }
                return ['*'.lang('error_type').':', '<select name="alias">'.$dropdownopts.'</select>'];

            default:
                return parent::display_single_element($one,$adding);
        }
    }

    public function ValidateData()
    {
        // $this->SetPropertyValue('searchable',0);
        // force not searchable.

        $errors = parent::ValidateData();
        if ($errors == FALSE) $errors = [];

        //Do our own alias check
        if ($this->mAlias == '') {
            $errors[] = lang('invalidalias');
        }
        else if (!in_array($this->mAlias, $this->valid_aliases)) {
            $errors[] = lang('missingparams');
        }
        else if ($this->mAlias != $this->mOldAlias) {
            $error = cmsms()->GetContentOperations()->CheckAliasError($this->mAlias, $this->mId);
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
