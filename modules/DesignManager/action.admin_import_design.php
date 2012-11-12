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

	try {
	  // check uploaded file for compatibility / validity
	  $xml = new dm_xml_reader();
	  $xml->open($_FILES[$key]['tmp_name']);
	  $xml->setParserProperty(XMLReader::VALIDATE, true);
	  debug_display('foo');
	  @$xml->next('template');
	  if( $xml->isValid() ) die('valid');
	  debug_display('foo2');
// 	  $nn = 0;
// 	  while( $xml->read() ) {
// 	    if( $nn++ >= 150 ) break;
// 	    echo "DEBUG: ".$xml->nodeType." == ".$xml->localName." -- ".$xml->value."<br/>";
// 	    if( !$xml->isValid() ) echo "NOT VALID<br/>";
// 	  }
	}
	catch( CmsXMLErrorException $e ) {
	  throw new CmsException($this->Lang('error_readxml'));
	}
	die('foo');

	// copy uploaded file to temporary location
	$tmpfile = tempname(TMP_CACHE_LOCATION,'dm_');
	if( $tmpfile === FALSE ) {
	  throw new CmsException($this->Lang('error_create_tempfile'));
	}
	@copy($_FILES[$key]['tmp_name'],$tmpfile);

	// redirect to this action, with step2.
      }
    }
    catch( CmsException $e ) {
      echo $this->ShowErrors($e->GetMessage());
    }

    echo $this->ProcessTemplate('admin_import_design.tpl');
    break;

  case 2:
    // preview what's going to be imported

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