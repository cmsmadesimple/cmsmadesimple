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
if( !$this->VisibleToAdminUser() ) return;
$tmp = json_decode(base64_decode($params['allparms']));
if( isset($params['allparms']) ) $params = array_merge($params,json_decode(base64_decode($params['allparms']),TRUE));
$this->SetCurrentTab('templates');

try {
    if( !isset($params['bulk_action']) || !isset($params['tpl_select']) ||
        !is_array($params['tpl_select']) || count($params['tpl_select']) == 0 ) {
        throw new \LogicException($this->Lang('error_missingparam'));
    }
    if( isset($params['cancel']) ) {
        $this->SetMessage($this->Lang('msg_cancelled'));
        $this->RedirectToAdminTab();
    }

    if( !$this->CheckPermission('Modify Templates') ) {
        // check if we have ownership/delete permission for these templates
        $my_templates = CmsLayoutTemplate::template_query(array(0=>'u:'.get_userid(),'as_list'=>1));
        if( !is_array($my_templates) || count($my_templates) == 0 ) {
            throw new \RuntimeException($this->Lang('error_retrieving_mytemplatelist'));
        }
        $tpl_ids = array_keys($my_templates);

        foreach( $params['tpl_select'] as $one ) {
            if( !in_array($one,$tpl_ids) ) throw new \RuntimeException($this->Lang('error_permission_bulkoperation'));
        }
    }

    $bulk_op = null;
    $templates = CmsLayoutTemplate::load_bulk($params['tpl_select']);
    switch( $params['bulk_action'] ) {
    case 'delete':
        $bulk_op = 'bulk_action_delete';
        if( isset($params['submit']) ) {
            if( !isset($params['check1']) || !isset($params['check2']) ) {
                echo $this->ShowErrors($this->Lang('error_notconfirmed'));
            }
            else {
                foreach( $templates as $one ) {
                    if( in_array($one->get_id(),$params['tpl_select']) ) {
                        $one->delete();
                    }
                }

                $this->SetMessage($this->Lang('msg_bulkop_complete'));
                $this->RedirectToAdminTab();
            }
        }
        break;

    case 'export':
        $bulk_op = 'bulk_action_export';
        $first_tpl = $templates[0];
        $outfile = $first_tpl->get_content_filename();
        $dn = dirname($outfile);
        if( !is_dir($dn) || !is_writable($dn) ) {
            throw new \RuntimeException($this->Lang('error_assets_writeperm'));
        }
        if( isset($params['submit']) ) {
            $n = 0;
            foreach( $templates as $one ) {
                if( in_array($one->get_id(),$params['tpl_select']) ) {
                    $outfile = $one->get_content_filename();
                    if( !is_file($outfile) ) {
                        file_put_contents($outfile,$one->get_content());
                        $n++;
                    }
                }
            }
            if( $n == 0 ) throw new \RuntimeException($this->Lang('error_bulkexport_noneprocessed'));

            $this->SetMessage($this->Lang('msg_bulkop_complete'));
            $this->RedirectToAdminTab();
        }
        break;

    case 'import':
        $bulk_op = 'bulk_action_import';
        $first_tpl = $templates[0];
        if( isset($params['submit']) ) {
            $n=0;
            foreach( $templates as $one ) {
                if( in_array($one->get_id(),$params['tpl_select']) ) {
                    $infile = $one->get_content_filename();
                    if( is_file($infile) && is_readable($infile) && is_writable($infile) ) {
                        $data = file_get_contents($infile);
                        $one->set_content($data);
                        $one->save();
                        unlink($infile);
                        $n++;
                    }
                }
            }
            if( $n == 0 ) {
                throw new \RuntimeException($this->Lang('error_bulkimport_noneprocessed'));
            }

            $this->SetMessage($this->Lang('msg_bulkop_complete'));
            $this->RedirectToAdminTab();
        }
        break;

    default:
        throw new \LogicException($this->Lang('error_missingparam'));
    }

    $smarty->assign('bulk_op',$bulk_op);
    $allparms = base64_encode(json_encode(array('tpl_select'=>$params['tpl_select'], 'bulk_action'=>$params['bulk_action'])));
    $smarty->assign('allparms',$allparms);
    $smarty->assign('templates',$templates);

    echo $this->ProcessTemplate('admin_bulk_template.tpl');
}
catch( \Exception $e ) {
    // master exception
    $this->SetError($e->GetMessage());
    $this->RedirectToAdminTab();
}

#
# EOF
#
