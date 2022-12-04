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
  if( $params['cancel'] == $this->Lang('cancel') ) $this->SetMessage($this->Lang('msg_cancelled'));
  $this->RedirectToAdminTab();
}

$step = 1;
if( isset($params['step']) ) $step = (int)$params['step'];

try {
  switch( $step ) {
  case 1:
    try {
      if( isset($params['next1']) ) {
				// check for uploaded file
				$key = $id.'import_xml_file';
				if( !isset($_FILES[$key]) || $_FILES[$key]['name'] == '' ) throw new CmsException($this->Lang('error_nofileuploaded'));
				if( $_FILES[$key]['error'] != 0 || $_FILES[$key]['tmp_name'] == '' || $_FILES[$key]['type'] == '') {
					throw new CmsException($this->Lang('error_uploading','xml'));
				}
				if( $_FILES[$key]['type'] != 'text/xml' ) throw new CmsException($this->Lang('error_upload_filetype',$_FILES[$key]['type']));

				$reader = dm_reader_factory::get_reader($_FILES[$key]['tmp_name']);
				$reader->validate();

				// copy uploaded file to temporary location
				$tmpfile = tempnam(TMP_CACHE_LOCATION,'dm_');
				if( $tmpfile === FALSE ) throw new CmsException($this->Lang('error_create_tempfile'));
				@copy($_FILES[$key]['tmp_name'],$tmpfile);

				// redirect to this action, with step2.
				$this->Redirect($id,'admin_import_design',$returnid,array('step'=>2,'tmpfile'=>$tmpfile));
      }
    }
    catch( CmsException $e ) {
      echo $this->ShowErrors($e->GetMessage());
    }

    echo $this->ProcessTemplate('admin_import_design.tpl');
    break;

  case 2:
    // preview what's going to be imported
		try {
			if( !isset($params['tmpfile']) ) {
				// bad error, redirect to admin tab.
				$this->SetError($this->Lang('error_missingparam'));
				$this->RedirectToAdminTab();
			}
			$tmpfile = trim($params['tmpfile']);
			if( !file_exists($tmpfile) ) {
				// bad error, redirect to admin tab.
				$this->SetError($this->Lang('error_filenotfound',$tmpfile));
				$this->RedirectToAdminTab();
			}

			$reader = dm_reader_factory::get_reader($tmpfile);

			if( isset($params['next2']) ) {
				$error = null;
				if( !isset($params['check1']) ) {
					$error = 1;
					echo $this->ShowErrors($this->Lang('error_notconfirmed'));
				}
				else if( !isset($params['newname']) || $params['newname'] == '' ) {
					$error = 1;
					echo $this->ShowErrors($this->Lang('error_missingparam'));
				}
				else {
					// redirect to this action, with step3.
					$this->Redirect($id,'admin_import_design',$returnid,array('step'=>3,'tmpfile'=>$tmpfile,'newname'=>$params['newname'], 'newdescription'=>$params['newdescription']));
				}
			}

			// suggest a new name for the 'theme'.
			$smarty = cmsms()->GetSmarty();
			$smarty->assign('tmpfile',$tmpfile);
			$smarty->assign('cms_version',CMS_VERSION);
			$design_info = $reader->get_design_info();
			$smarty->assign('design_info',$design_info);
			$smarty->assign('templates',$reader->get_template_list());
			$smarty->assign('stylesheets',$reader->get_stylesheet_list());
			$newname = CmsLayoutCollection::suggest_name($design_info['name']);
            $smarty->assign('new_name',$newname);
		}
    catch( CmsException $e ) {
      echo $this->ShowErrors($e->GetMessage());
    }
    echo $this->ProcessTemplate('admin_import_design2.tpl');
    break;

  case 3:
		// do the importing.
		if( !isset($params['tmpfile']) || !isset($params['newname']) ||
				$params['newname'] == '') {
			// bad error, redirect to admin tab.
			throw new CmsException($this->Lang('error_missingparam'));
		}
		$tmpfile = trim($params['tmpfile']);
    $newname = trim($params['newname']);
		$newdescription = trim($params['newdescription']);

		if( !file_exists($tmpfile) ) {
			// bad error, redirect to admin tab.
			throw new CmsException($this->Lang('error_filenotfound',$tmpfile));
		}

		$destdir = $config['uploads_path'].'/designmanager_import';
		$reader = dm_reader_factory::get_reader($tmpfile);
    $reader->set_suggested_name($newname);
    $reader->set_suggested_description($newdescription);
		$reader->import();
		$this->SetMessage($this->Lang('msg_design_imported'));
		$this->RedirectToAdminTab();
		break;

  default:
  }
}
catch( CmsException $e ) {
  $this->SetError($e->GetMessage());
  $this->RedirectToAdminTab();
}

#
# EOF
#
?>