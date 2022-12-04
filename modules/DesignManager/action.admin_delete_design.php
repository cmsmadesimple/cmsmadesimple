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
if( !$this->CheckPermission('Manage Designs') ) return;

$this->SetCurrentTab('designs');
if( isset($params['cancel']) ) {
    $this->SetMessage($this->Lang('msg_cancelled'));
    $this->RedirectToAdminTab();
}

try {
    if( !isset($params['design']) ) {
        throw new CmsException($this->Lang('error_missingparam'));
    }
    $design = CmsLayoutCollection::load($params['design']);

    $can_delete_stylesheets = $this->CheckPermission('Manage Stylesheets');
    $can_delete_templates = $this->CheckPermission('Modify Templates');

    if( isset($params['submit']) ) {
        if( !isset($params['confirm_delete1']) || $params['confirm_delete1'] != 'yes' ||
            !isset($params['confirm_delete2']) || $params['confirm_delete2'] != 'yes') {
            $this->SetError($this->Lang('error_notconfirmed'));
            $this->RedirectToAdminTab();
        }

        if( isset($params['delete_stylesheets']) && $can_delete_stylesheets ) {
            $css_id_list = $design->get_stylesheets();
            if( is_array($css_id_list) && count($css_id_list) ) {
                // get the designs that are attached to these stylesheets
                $css_list = CmsLayoutStylesheet::load_bulk($css_id_list);
                if( is_array($css_list) && count($css_list) ) {
                    foreach( $css_list as &$css ) {
                        $x = $css->get_designs();
                        if( is_array($x) && count($x) == 1 && $x[0] == $design->get_id() ) {
                            // its orphaned
                            $css->delete();
                        }
                    }
                }
            }
        }

        if( isset($params['delete_templates']) && $can_delete_templates ) {
            $tpl_id_list = $design->get_templates();
            if( is_array($tpl_id_list) && count($tpl_id_list) ) {
				$templates = CmsLayoutTemplate::load_bulk($tpl_id_list);
				if( is_array($templates) && count($templates) ) {
					foreach( $templates as &$tpl ) {
						$x = $tpl->get_designs();
						if( is_array($x) && count($x) == 1 && $x[0] == $design->get_id() ) {
							// its orphaned
							$tpl->delete();
						}
					}
				}
            }
        }

        // done... we 'force' the delete because we loaded the design object
		// before deleting the templates and stylesheets.
        $design->delete(TRUE);
        $this->SetMessage($this->Lang('msg_design_deleted'));
        $this->RedirectToAdminTab();
    }

    $smarty->assign('tpl_permission',$can_delete_templates);
    $smarty->assign('css_permission',$can_delete_stylesheets);
    $smarty->assign('design',$design);
    echo $this->ProcessTemplate('admin_delete_design.tpl');
}
catch( CmsException $e ) {
    $this->SetError($e->GetMessage());
    $this->RedirectToAdminTab();
}
