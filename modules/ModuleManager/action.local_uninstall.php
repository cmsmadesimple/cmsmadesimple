<?php
#---------------------------------------------------------------------------
# CMS Made Simple - Power for the professional, Simplicity for the end user.
# (c) 2004 - 2011 by Ted Kulp
# (c) 2011 - 2018 by the CMS Made Simple Development Team
# (c) 2018 and beyond by the CMS Made Simple Foundation
# This project's homepage is: https://www.cmsmadesimple.org
#---------------------------------------------------------------------------
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#---------------------------------------------------------------------------

if( !isset($gCms) ) exit;
if( !$this->CheckPermission('Modify Modules') ) return;

$this->SetCurrentTab('installed');

try {
    $mod = get_parameter_value($params,'mod');
    if( !$mod ) {
        $this->SetError($this->Lang('error_missingparams'));
        $this->RedirectToAdminTab();
    }

    $ops = ModuleOperations::get_instance();
    $modinstance = $ops->get_module_instance($mod,'',TRUE);
    if( !is_object($modinstance) ) {
        // uh-oh
        $this->SetError($this->Lang('error_getmodule',htmlspecialchars($mod)));
        $this->RedirectToAdminTab();
    }

    if( isset($params['cancel']) ) {
        $this->RedirectToAdminTab();
    }

    if( isset($params['submit']) ) {
        try {
            if( !isset($params['confirm']) || $params['confirm'] != 1 ) throw new \RuntimeException($this->Lang('error_notconfirmed'));
            $postmsg = $modinstance->UninstallPostMessage();
            if( $postmsg == '' ) $postmsg = $this->Lang('msg_module_uninstalled',$mod);
            $result = $ops->UninstallModule($mod);
            if( $result[0] == FALSE ) throw new \RuntimeException($result[1]);
            $this->SetMessage($postmsg);
            $this->RedirectToAdminTab();
        }
        catch( \Exception $e ) {
            $this->ShowErrors($e->GetMessage());
        }
    }

    $tpl = $smarty->CreateTemplate($this->GetTemplateResource('local_uninstall.tpl'));
    $tpl->assign('mod',$this);
    $tpl->assign('actionid',$id);
    $tpl->assign('module_name',$modinstance->GetName());
    $tpl->assign('module_version',$modinstance->GetVersion());
    $msg = $modinstance->UninstallPreMessage();
    if( !$msg ) $msg = $this->Lang('msg_module_uninstall');
    $tpl->assign('msg',$msg);
    $tpl->display();
}
catch( \Exception $e ) {
    echo $this->ShowErrors($e->GetMessage());
}

#
# EOF
#