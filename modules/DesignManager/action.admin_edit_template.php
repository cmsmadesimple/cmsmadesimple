<?php
#-------------------------------------------------------------------------
# Module: AdminSearch - A CMSMS addon module to provide template management.
# (c) 2012 by Robert Campbell <calguy1000@cmsmadesimple.org>
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.

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
if (!$this->CheckPermission('Modify Templates')) {
    // no manage templates permission
    if (!$this->CheckPermission('Add Templates')) {
        // no add templates permission
        if (!isset($params['tpl']) || !CmsLayoutTemplate::user_can_edit($params['tpl'])) {
            // no parameter, or no ownership/addt_editors.
            return;
        }
    }
}

$this->SetCurrentTab('templates');
$tpl_id = (int) get_parameter_value($params,'tpl');

if (isset($params['cancel'])) {
    if ($params['cancel'] == $this->Lang('cancel')) $this->SetMessage($this->Lang('msg_cancelled'));
    $this->RedirectToAdminTab();
}

try {
    $tpl_obj = null;
    $type_obj = null;
    $type_is_readonly = false;
    $message = $this->Lang('msg_template_saved');
    $response = 'success';
    $apply = isset($params['apply']) ? 1 : 0;

    $extraparms = array();
    if (isset($params['import_type'])) {
        $tpl_obj = CmsLayoutTemplate::create_by_type($params['import_type']);
        $tpl_obj->set_owner(get_userid());
        $design = CmsLayoutCollection::load_default();
        if( $design ) {
            $tpl_obj->add_design($design);
        }
        $extraparms['import_type'] = $params['import_type'];
        $type_is_readonly = true;
    } else if (isset($params['tpl'])) {
        $tpl_obj = CmsLayoutTemplate::load($params['tpl']);
        $tpl_obj->get_designs();
        $extraparms['tpl'] = $params['tpl'];
    } else {
        $this->SetError($this->Lang('error_missingparam'));
        $this->RedirectToAdminTab();
    }
    $type_obj = CmsLayoutTemplateType::load($tpl_obj->get_type_id());

    try {
        if (isset($params['submit']) || isset($params['apply']) ) {
            // do the magic.
            if (isset($params['description'])) $tpl_obj->set_description($params['description']);
            if (isset($params['type'])) $tpl_obj->set_type($params['type']);
            if (isset($params['default'])) $tpl_obj->set_type_dflt($params['default']);
            if (isset($params['owner_id'])) $tpl_obj->set_owner($params['owner_id']);
            if (isset($params['addt_editors']) && is_array($params['addt_editors']) && count($params['addt_editors'])) {
                $tpl_obj->set_additional_editors($params['addt_editors']);
            }
            if (isset($params['category_id'])) $tpl_obj->set_category($params['category_id']);
            $tpl_obj->set_listable(isset($params['listable'])?$params['listable']:1);
            if( isset($params['contents']) ) $tpl_obj->set_content($params['contents']);
            $tpl_obj->set_name(strip_tags($params['name']));

            if ($this->CheckPermission('Manage Designs')) {
                $design_list = array();
                if (isset($params['design_list'])) $design_list = $params['design_list'];
                $tpl_obj->set_designs($design_list);
            }

            // lastly, check for errors in the template before we save.
            if( isset($params['contents']) ) {
                cms_utils::set_app_data('tmp_template', $params['contents']);
                /*
                $parser = new \CMSMS\internal\page_template_parser('cms_template:appdata;tmp_template',$smarty);
                $parser->compileTemplateSource();
                if ($type_obj->get_content_block_flag()) {
                    $contentBlocks = CMS_Content_Block::get_content_blocks();
                    if (!is_array($contentBlocks) || count($contentBlocks) == 0) {
                        throw new CmsEditContentException('No content blocks defined in template');
                    }
                }
                */
            }

            // if we got here, we're golden.
            $tpl_obj->save();

            if (!$apply) {
                $this->SetMessage($message);
                $this->RedirectToAdminTab();
            }

        }
        else if( isset($params['export']) ) {
            $outfile = $tpl_obj->get_content_filename();
            $dn = dirname($outfile);
            if( !is_dir($dn) || !is_writable($dn) ) {
                throw new \RuntimeException($this->Lang('error_assets_writeperm'));
            }
            if( is_file($outfile) && !is_writable($outfile) ) {
                throw new \RuntimeException($this->Lang('error_assets_writeperm'));
            }
            file_put_contents($outfile,$tpl_obj->get_content());
        }
        else if( isset($params['import']) ) {
            $infile = $tpl_obj->get_content_filename();
            if( !is_file($infile) || !is_readable($infile) || !is_writable($infile) ) {
                throw new \RuntimeException($this->Lang('error_assets_readwriteperm'));
            }
            $data = file_get_contents($infile);
            unlink($infile);
            $tpl_obj->set_content($data);
            $tpl_obj->save();
        }
    } catch( \Exception $e ) {
        $message = $e->GetMessage();
        $response = 'error';
    }

    //
    // BUILD THE DISPLAY
    //
    if (!$apply && $tpl_obj && $tpl_obj->get_id() && dm_utils::locking_enabled()) {
        $smarty->assign('lock_timeout', $this->GetPreference('lock_timeout'));
        $smarty->assign('lock_refresh', $this->GetPreference('lock_refresh'));
        try {
            $lock_id = CmsLockOperations::is_locked('template', $tpl_obj->get_id());
            $lock = null;
            if( $lock_id > 0 ) {
                // it's locked... by somebody, make sure it's expired before we allow stealing it.
                $lock = CmsLock::load('template',$tpl_obj->get_id());
                if( !$lock->expired() ) throw new CmsLockException('CMSEX_L010');
                CmsLockOperations::unlock($lock_id,'template',$tpl_obj->get_id());
            }
        } catch( CmsException $e ) {
            $message = $e->GetMessage();
            $this->SetError($message);
            $this->RedirectToAdminTab();
        }
    }

    // handle the response message
    if ($apply) {
        $this->GetJSONResponse($response, $message);
    } elseif (!$apply && $response == 'error') {
        echo $this->ShowErrors($message);
    }

    if( ($tpl_id = $tpl_obj->get_id()) > 0 ) {
        \CmsAdminThemeBase::GetThemeObject()->SetSubTitle($this->Lang('edit_template').': '.$tpl_obj->get_name()." ($tpl_id)");
    } else {
        \CmsAdminThemeBase::GetThemeObject()->SetSubTitle($this->Lang('create_template'));
    }

    $smarty->assign('type_obj', $type_obj);
    $smarty->assign('extraparms', $extraparms);
    $smarty->assign('template', $tpl_obj);

    $cats = CmsLayoutTemplateCategory::get_all();
    $out = array();
    $out[''] = $this->Lang('prompt_none');
    if (is_array($cats) && count($cats)) {
        foreach ($cats as $one) {
            $out[$one->get_id()] = $one->get_name();
        }
    }
    $smarty->assign('category_list', $out);

    $types = CmsLayoutTemplateType::get_all();
    if (is_array($types) && count($types)) {
        $out = array();
        $out2 = array();
        foreach ($types as $one) {
            $out2[] = $one->get_id();
            $out[$one->get_id()] = $one->get_langified_display_value();
        }
        $smarty->assign('type_list', $out);
        $smarty->assign('type_is_readonly', $type_is_readonly);
    }

    $designs = CmsLayoutCollection::get_all();
    if (is_array($designs) && count($designs)) {
        $out = array();
        foreach ($designs as $one) {
            $out[$one->get_id()] = $one->get_name();
        }
        $smarty->assign('design_list', $out);
    }

    if ($tpl_obj->get_id()) $smarty->assign('tpl_id', $tpl_obj->get_id());
    $smarty->assign('has_manage_right', $this->CheckPermission('Modify Templates'));
    $smarty->assign('has_themes_right', $this->CheckPermission('Manage Designs'));
    if ($this->CheckPermission('Modify Templates') || $tpl_obj->get_owner_id() == get_userid()) {

        $userops = cmsms()->GetUserOperations();
        $allusers = $userops->LoadUsers();
        $tmp = array();
        foreach ($allusers as $one) {
            //FIXME Why skip admin here? If template owner is admin this would unset admin as owner
            //if ($one->id == 1)
            //    continue;
            $tmp[$one->id] = $one->username;
        }
        if (is_array($tmp) && count($tmp)) $smarty->assign('user_list', $tmp);

        $groupops = cmsms()->GetGroupOperations();
        $allgroups = $groupops->LoadGroups();
        foreach ($allgroups as $one) {
            if ($one->id == 1) continue;
            if ($one->active == 0) continue;
            $tmp[$one->id * -1] = $this->Lang('prompt_group') . ': ' . $one->name;
            // appends to the tmp array.
        }
        if (is_array($tmp) && count($tmp)) $smarty->assign('addt_editor_list', $tmp);
    }
    echo $this->ProcessTemplate('admin_edit_template.tpl');
} catch( CmsException $e ) {
    $this->SetError($e->GetMessage());
    $this->RedirectToAdminTab();
}

#
# EOF
#