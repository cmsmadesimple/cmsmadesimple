<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: ModuleManager (c) 2008 by Robert Campbell
#         (calguy1000@cmsmadesimple.org)
#  An addon module for CMS Made Simple to allow browsing remotely stored
#  modules, viewing information about them, and downloading or upgrading
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# Visit our homepage at: http://www.cmsmadesimple.org
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# However, as a special exception to the GPL, this software is distributed
# as an addon module to CMS Made Simple.  You may not use this software
# in any Non GPL version of CMS Made simple, or in any version of CMS
# Made simple that does not indicate clearly and obviously in its admin
# section that the site was built with CMS Made simple.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------
#END_LICENSE
if( !isset($gCms) ) exit;

if( isset($params['modulehelp']) ) {
    // this is done before permissions checks
    $params['mod'] = $params['modulehelp'];
    unset($params['modulehelp']);
    include(__DIR__.'/action.local_help.php');
    return;
}

if( !$this->VisibleToAdminUser() ) exit;
$tmp = ModuleOperations::get_instance()->GetQueueResults();
if( is_array($tmp) && count($tmp) ) {
    $tmp2 = array();
    foreach( $tmp as $key => $data ) {
        $msg = $data[1];
        if( !$msg ) {
            $msg = $this->Lang('unknown');
            if( $data[0] ) $msg = $this->Lang('success');
        }
        $tmp2[] = $key.': '.$msg;
    }
    echo $this->ShowMessage($tmp2);
}

echo '<div class="pagewarning">'."\n";
echo '<h3>'.$this->Lang('notice')."</h3>\n";
$link = '<a target="_blank" href="http://dev.cmsmadesimple.org">forge</a>';
echo '<p>'.$this->Lang('general_notice',$link,$link)."</p>\n";
echo '<h3>'.$this->Lang('use_at_your_own_risk')."</h3>\n";
echo '<p>'.$this->Lang('compatibility_disclaimer')."</p></div>\n";

$connection_ok = modmgr_utils::is_connection_ok();
if( !$connection_ok ) echo $this->ShowErrors($this->Lang('error_request_problem'));

// this is a bit ugly.
modmgr_utils::get_images();

$newversions = [];
if( $connection_ok ) {
    try {
        $newversions = modulerep_client::get_newmoduleversions();
    }
    catch( Exception $e ) {
        echo $this->ShowErrors($e->GetMessage());
    }
}

echo $this->StartTabHeaders();
if( $this->CheckPermission('Modify Modules') ) {
    echo $this->SetTabHeader('installed',$this->Lang('installed'));
    if( $connection_ok ) {
        $num = ( is_array($newversions) ) ? count($newversions) : 0;
        echo $this->SetTabHeader('newversions',$num.' '.$this->Lang('tab_newversions') );
        echo $this->SetTabHeader('search',$this->Lang('search'));
        echo $this->SetTabHeader('modules',$this->Lang('availmodules'));
    }
}
if( $this->CheckPermission('Modify Site Preferences') ) echo $this->SetTabHeader('prefs',$this->Lang('prompt_settings'));
echo $this->EndTabHeaders();

echo $this->StartTabContent();
if( $this->CheckPermission('Modify Modules') ) {
    echo $this->StartTab('installed',$params);
    include(dirname(__FILE__).'/function.admin_installed.php');
    echo $this->EndTab();

    if( $connection_ok ) {
        echo $this->StartTab('newversions',$params);
        include(dirname(__FILE__).'/function.newversionstab.php');
        echo $this->EndTab();

        echo $this->StartTab('search',$params);
        include(dirname(__FILE__).'/function.search.php');
        echo $this->EndTab();

        echo $this->StartTab('modules',$params);
        include(dirname(__FILE__).'/function.admin_modules_tab.php');
        echo $this->EndTab();
    }
}
if( $this->CheckPermission('Modify Site Preferences') ) {
    echo $this->StartTab('prefs',$params);
    include(dirname(__FILE__).'/function.admin_prefs_tab.php');
    echo $this->EndTab();
}
echo $this->EndTabContent();
