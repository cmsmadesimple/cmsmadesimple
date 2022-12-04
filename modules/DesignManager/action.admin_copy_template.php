<?php
#-------------------------------------------------------------------------
# Module: DesignManager - A CMSMS addon module to provide template management.
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
if( !isset($gCms) ) exit;
if( !$this->CheckPermission('Modify Templates') ) {
    // no manage templates permission
    if( !$this->CheckPermission('Add Templates') ) {
        // no add templates permission
        if( !isset($params['tpl']) || !CmsLayoutTemplate::user_can_edit($params['tpl']) ) {
            // no parameter, or no ownership/addt_editors.
            return;
        }
    }
}

$this->SetCurrentTab('templates');
if( !isset($params['tpl']) ) {
    $this->SetError($this->Lang('error_missingparam'));
    $this->RedirectToAdminTab();
}
if( isset($params['cancel']) ) {
    $this->SetMessage($this->Lang('msg_cancelled'));
    $this->RedirectToAdminTab();
}

try {
    $orig_tpl = CmsLayoutTemplate::load($params['tpl']);

    if( isset($params['submit']) || isset($params['submitandedit']) ) {

        try {
            $new_tpl = clone($orig_tpl);
            $new_tpl->set_owner(get_userid());
            $new_tpl->set_name(trim($params['new_name']));
            $new_tpl->set_additional_editors(array());

            // only if have manage themes right.
            if( $this->CheckPermission('Modify Designs') ) {
				$new_tpl->set_designs($orig_tpl->get_designs());
            }
            else {
				$new_tpl->set_designs(array());
            }
            $new_tpl->save();

            if( isset($params['submitandedit']) ) {
				$this->SetMessage($this->Lang('msg_template_copied_edit'));
				$this->Redirect($id,'admin_edit_template',$returnid,array('tpl'=>$new_tpl->get_id()));
            }
            else {
				$this->SetMessage($this->Lang('msg_template_copied'));
				$this->RedirectToAdminTab();
            }
        }
        catch( CmsException $e ) {
            echo $this->ShowErrors($e->GetMessage());
        }
    }

    // build a display.
    $cats = CmsLayoutTemplateCategory::get_all();
    $out = array();
    $out[0] = $this->Lang('prompt_none');
    if( is_array($cats) && count($cats) ) {
        foreach( $cats as $one ) {
            $out[$one->get_id()] = $one->get_name();
        }
    }
    $smarty->assign('category_list',$out);

    $types = CmsLayoutTemplateType::get_all();
    if( is_array($types) && count($types) ) {
        $out = array();
        foreach( $types as $one ) {
            $out[$one->get_id()] = $one->get_langified_display_value();
        }
        $smarty->assign('type_list',$out);
    }

    $designs = CmsLayoutCollection::get_all();
    if( is_array($designs) && count($designs) ) {
        $out = array();
        foreach( $designs as $one ) {
            $out[$one->get_id()] = $one->get_name();
        }
        $smarty->assign('design_list',$out);
    }

    $userops = cmsms()->GetUserOperations();
    $allusers = $userops->LoadUsers();
    $tmp = array();
    foreach( $allusers as $one ) {
        $tmp[$one->id] = $one->username;
    }
    if( is_array($tmp) && count($tmp) ) {
        $smarty->assign('user_list',$tmp);
    }

    $new_name = $orig_tpl->get_name();
    $p = strrpos($new_name,' -- ');
    $n = 2;
    if( $p !== FALSE ) {
        $n = (int)substr($new_name,$p+4)+1;
        $new_name = substr($new_name,0,$p);
    }
    $new_name .= ' -- '.$n;
    $smarty->assign('new_name',$new_name);

    $smarty->assign('tpl',$orig_tpl);
    echo $this->ProcessTemplate('admin_copy_template.tpl');
}
catch( CmsException $e ) {
    $this->SetError($e->GetMessage());
    $this->RedirectToAdminTab();
}
#
# EOF
#
?>