<?php
#-------------------------------------------------------------------------
# Module: UserGuide
# Author: Chris Taylor
# Copyright: (C) 2018 Chris Taylor, chris@binnovative.co.uk
# Licence: GNU General Public License version 3
#          see /UserGuide/lang/LICENCE.txt or <http://www.gnu.org/licenses/>
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2011 by Ted Kulp (wishy@cmsmadesimple.org)
# Project's homepage is: http://www.cmsmadesimple.org
# Module's homepage is: http://dev.cmsmadesimple.org/projects/UserGuide
#-------------------------------------------------------------------------
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 3 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.
#-------------------------------------------------------------------------

class UserGuide extends CMSModule {

    const MANAGE_PERM = 'manage_userguide';
    const USE_PERM = 'use_userguide';
    const ADMIN_SECTION_DEFAULT = 'main';
    const LOADING_ICON = '../modules/UserGuide/lib/images/loading.gif';
    const EMBED_TYPES = [
        'vimeo',
        'youtube',
        'local',
        'code'
    ];
    const EMBED_PREFIX_VIMEO = '//player.vimeo.com/video/';
    const EMBED_PREFIX_YOUTUBE = '//www.youtube.com/embed/';
    const DEFAULT_CONTENT_XML = '/lib/userguide_default_content.xml';

    public function GetVersion() { return '1.0'; }
    public function GetFriendlyName() { return $this->GetPreference('customModuleName', 'User Guide'); }
    public function GetAdminDescription() { return $this->Lang('admindescription'); }
    public function IsPluginModule() { return FALSE; }
    public function HasAdmin() { return TRUE; }
    public function LazyLoadAdmin() { return TRUE; }
    public function GetAdminSection() { return $this->GetPreference('adminSection', self::ADMIN_SECTION_DEFAULT); }
    public function VisibleToAdminUser() { 
        return ( $this->CheckPermission(self::USE_PERM) || $this->CheckPermission(self::MANAGE_PERM) ); 
    }
    public function GetHelp() { return $this->Lang('help'); }
    public function GetAuthor() { return 'Chris Taylor'; }
    public function GetAuthorEmail() { return 'chris@binnovative.co.uk'; }
    public function GetChangeLog() { return $this->Lang('changelog'); }
    public function MinimumCMSVersion() { return '2.0'; }


    function __construct() {
        parent::__construct();
    }

    public function UninstallPreMessage() {
        return $this->Lang('ask_uninstall');
    }


    public function GetHeaderHTML()
    {
        $module_path = $this->GetModuleURLPath();
        $header_links = '<link rel="stylesheet" type="text/css" href="'.$module_path.'/lib/css/UserGuide_admin.css">';
        // see if custom.css file exists
        $customCSSfile = 'assets/module_custom/UserGuide/custom.css';
        if ( file_exists(CMS_ROOT_PATH.'/'.$customCSSfile) ) {
            $header_links .= '<link rel="stylesheet" type="text/css" href="../'.$customCSSfile.'">';
        }
        $header_links .= '<script language="javascript" src="'.$module_path.'/lib/js/UserGuide_admin.js"></script>';
        return $header_links;
    }



    /**
    * @link http://www.cmsmadesimple.org/apidoc/CMS/CMSModule.html#HasCapability
    * @ignore
    */
    function HasCapability($capability, $params = array()) {
        switch ($capability) {
            default:
                return FALSE;
        }
    }


    /**
    * @link https://apidoc.cmsmadesimple.org/classes/CMSModule.html#method_GetAdminMenuItems
    */
    public function GetAdminMenuItems()
    {
        $out = [];
        if ( $this->VisibleToAdminUser() ) {
            $out[] = CmsAdminMenuItem::from_module($this);
        }

        if ( $this->CheckPermission(self::MANAGE_PERM) && $this->GetPreference('separate_settings', 0) ) {
            $obj = new CmsAdminMenuItem();
            $obj->module = $this->GetName();
            $obj->section = 'siteadmin';
            $obj->title = $this->Lang('title_userguide_settings');
            $obj->description = $this->Lang('desc_userguide_settings');
            $obj->action = 'admin_settings';
            $out[] = $obj;
        }
        return $out;
    }


}