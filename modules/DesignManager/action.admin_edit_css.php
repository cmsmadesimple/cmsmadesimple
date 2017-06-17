<?php
#-------------------------------------------------------------------------
# Module: AdminSearch - A CMSMS addon module to provide template management.
# (c) 2012 by Robert Campbell <calguy1000@cmsmadesimple.org>
#
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
#
#-------------------------------------------------------------------------
if (!isset($gCms)) exit ;
if (!$this->CheckPermission('Manage Stylesheets')) return;

$this->SetCurrentTab('stylesheets');
$css_id = (int) get_parameter_value($params,'css');

if( isset($params['cancel']) ) {
    if( $params['cancel'] == $this->Lang('cancel') ) $this->SetMessage($this->Lang('msg_cancelled'));
    $this->RedirectToAdminTab();
}

try {
    $css_ob = null;
    $message = $this->Lang('msg_stylesheet_saved');
    $response = 'success';
    $apply = isset($params['apply']) ? 1 : 0;
    $extraparms = array();

    if ($css_id) {
        $css_ob = CmsLayoutStylesheet::load($css_id);
        $extraparms['css'] = $css_id;
    } else {
        $css_ob = new CmsLayoutStylesheet();
    }

    try {
        if (isset($params['submit']) || isset($params['apply']) && $response !== 'error') {
            if (isset($params['name'])) $css_ob->set_name($params['name']);
            if (isset($params['description'])) $css_ob->set_description($params['description']);
            if (isset($params['content'])) $css_ob->set_content($params['content']);
            $typ = array();
            if (isset($params['media_type'])) $typ = $params['media_type'];
            $css_ob->set_media_types($typ);
            if (isset($params['media_query'])) $css_ob->set_media_query($params['media_query']);
            if ($this->CheckPermission('Manage Designs')) {
                $design_list = array();
                if (isset($params['design_list'])) $design_list = $params['design_list'];
                $css_ob->set_designs($design_list);
            }
            $css_ob->save();

            if (!$apply) {
                $this->SetMessage($message);
                $this->RedirectToAdminTab();
            }
        }
        else if( isset($params['export']) ) {
            $outfile = $css_ob->get_content_filename();
            $dn = dirname($outfile);
            if( !is_dir($dn) || !is_writable($dn) ) {
                throw new \RuntimeException($this->Lang('error_assets_writeperm'));
            }
            if( is_file($outfile) && !is_writable($outfile) ) {
                throw new \RuntimeException($this->Lang('error_assets_writeperm'));
            }
            file_put_contents($outfile,$css_ob->get_content());
        }
        else if( isset($params['import']) ) {
            $infile = $css_ob->get_content_filename();
            if( !is_file($infile) || !is_readable($infile) || !is_writable($infile) ) {
                throw new \RuntimeException($this->Lang('error_assets_readwriteperm'));
            }
            $data = file_get_contents($infile);
            unlink($infile);
            $css_ob->set_content($data);
            $css_ob->save();
        }
    } catch( \Exception $e ) {
        $message = $e->GetMessage();
        $response = 'error';
    }

    //
    // prepare to display.
    //
    if (!$apply && $css_ob && $css_ob->get_id() && dm_utils::locking_enabled()) {
        $smarty->assign('lock_timeout', $this->GetPreference('lock_timeout'));
        $smarty->assign('lock_refresh', $this->GetPreference('lock_refresh'));
        try {
            $lock_id = CmsLockOperations::is_locked('stylesheet', $css_ob->get_id());
            $lock = null;
            if( $lock_id > 0 ) {
                // it's locked... by somebody, make sure it's expired before we allow stealing it.
                $lock = CmsLock::load('stylesheet',$css_ob->get_id());
                if( !$lock->expired() ) throw new CmsLockException('CMSEX_L010');
                CmsLockOperations::unlock($lock_id,'stylesheet',$css_ob->get_id());
            }
        } catch( CmsException $e ) {
            $response = 'error';
            $message = $e->GetMessage();

            if (!$apply) {
                $this->SetError($message);
                $this->RedirectToAdminTab();
            }
        }
    }

    // handle the response message
    if ($apply) {
        $this->GetJSONResponse($response, $message);
    } elseif (!$apply && $response == 'error') {
        echo $this->ShowErrors($message);
    }

    $designs = CmsLayoutCollection::get_all();
    if (is_array($designs) && count($designs)) {
        $out = array();
        foreach ($designs as $one) {
            $out[$one->get_id()] = $one->get_name();
        }
        $smarty->assign('design_list', $out);
    }

    if( $css_ob->get_id() > 0 ) {
        \CmsAdminThemeBase::GetThemeObject()->SetSubTitle($this->Lang('edit_stylesheet').': '.$css_ob->get_name()." ({$css_ob->get_id()})");
    } else {
        \CmsAdminThemeBase::GetThemeObject()->SetSubTitle($this->Lang('create_stylesheet'));
    }

    $smarty->assign('has_designs_right', $this->CheckPermission('Manage Designs'));
    $smarty->assign('extraparms', $extraparms);
    $smarty->assign('css', $css_ob);
    if ($css_ob && $css_ob->get_id()) $smarty->assign('css_id', $css_ob->get_id());

    echo $this->ProcessTemplate('admin_edit_css.tpl');
} catch( CmsException $e ) {
    $this->SetError($e->GetMessage());
    $this->RedirectToAdminTab();
}

#
# EOF
#
