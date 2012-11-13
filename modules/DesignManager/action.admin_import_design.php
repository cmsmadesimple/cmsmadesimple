<?php // -*- mode:php; tab-width:2; indent-tabs-mode:t; c-basic-offset:2; -*-
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
if( !isset($gCms) ) exit;
if( !$this->CheckPermission('Manage Designs') ) return;

$this->SetCurrentTab('designs');

if( isset($params['cancel']) ) {
  if( $params['cancel'] == $this->Lang('cancel') ) {
    $this->SetMessage($this->Lang('msg_cancelled'));
  }
  $this->RedirectToAdminTab();
}

$step = 1;
if( isset($params['step']) ) {
  $step = (int)$params['step'];
}

try {
  switch( $step ) {
  case 1:
    try {
      if( isset($params['next1']) ) {
				// check for uploaded file
				$key = $id.'import_xml_file';
				if( !isset($_FILES[$key]) || $_FILES[$key]['name'] == '' ) {
					throw new CmsException($this->Lang('error_nofileuploaded'));
				}
				if( $_FILES[$key]['error'] != 0 || $_FILES[$key]['tmp_name'] == '' || $_FILES[$key]['type'] == '') {
					throw new CmsException($this->Lang('error_uploading','xml'));
				}
				if( $_FILES[$key]['type'] != 'text/xml' ) {
					throw new CmsException($this->Lang('error_upload_filetype'));
				}

				// basic check or validity
				// if there's no DOCTYPE within 100 bytes, there's a problem.
				$fh = fopen($_FILES[$key]['tmp_name'],'r');
				if( !$fh ) {
					throw new CmsException($this->Lang('error_uploading'));
				}
				$str = fread($fh,200);
				if( strpos($str,'<!DOCTYPE') === FALSE ) {
					throw new CmsException($this->Lang('error_readxml'));
				}
				fclose($fh);

				// get the first element
				$x = '<!ELEMENT ';
				$p = strpos($str,'<!ELEMENT ');
				if( $p === FALSE ) {
					throw new CmsException($this->Lang('error_readxml'));
				}
				$str = substr($str,$p+strlen($x));
				$p = strpos($str,' ');
				if( $p === FALSE ) {
					// highly unlikely.
					throw new CmsException($this->Lang('error_readxml'));
				}
				$word = substr($str,0,$p);
				switch( $word ) {
				case 'theme':
					$reader = new dm_theme_reader($_FILES[$key]['tmp_name']);
					$reader->validate();
					// old ThemeManger theme
					break;

				case 'design':
					// new DesignManager design
					$reader = new dm_design_reader($_FILES[$key]['tmp_name']);
					$reader->validate();
					break;
					
				default:
					throw new CmsException($this->Lang('error_readxml'));
				}

				// copy uploaded file to temporary location
				$tmpfile = tempnam(TMP_CACHE_LOCATION,'dm_');
				if( $tmpfile === FALSE ) {
					throw new CmsException($this->Lang('error_create_tempfile'));
				}
				@copy($_FILES[$key]['tmp_name'],$tmpfile);

				// redirect to this action, with step2.
				$this->Redirect($id,'admin_import_design',$returnid,
												array('step'=>2,'tmpfile'=>$tmpfile));
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

			$reader = new dm_design_reader($tmpfile);
			$smarty = cmsms()->GetSmarty();
			$tmp = $reader->get_design_info();
			$smarty->assign('design_info',$reader->get_design_info());
			//$smarty->assign('templates',$reader->get_template_list
		}
    catch( CmsException $e ) {
      echo $this->ShowErrors($e->GetMessage());
    }
    echo $this->ProcessTemplate('admin_import_design2.tpl');
    break;

  case 3:
    // do the importing.

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