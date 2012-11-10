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
if( !$this->CheckPermission('Modify Templates') ) return;

$this->SetCurrentTab('templates');

try {
  if( !isset($params['tpl']) ) {
    throw new CmsException($this->Lang('error_missingparam'));
  }

  if( isset($params['cancel']) ) {
    $this->SetMessage($this->Lang('msg_cancelled'));
    $this->Redirect($id,'admin_edit_template',$returnid,array('tpl'=>$params['tpl']));
  }

  $tpl_obj = CmsLayoutTemplate::load($params['tpl']);

  if( isset($params['submit']) ) {
    if( !isset($params['check1']) || !isset($params['check2']) ) {
      throw new CmsException($this->Lang('error_notconfirmed'));
    }

    $db = cmsms()->GetDb();
    $time = $db->DbTimeStamp(time());
    $query = 'UPDATE '.cms_db_prefix()."content 
              SET template_id = ?, last_modified_by = ?, modified_date = $time";
    $dbr = $db->Execute($query,array($tpl_obj->get_id(),get_userid()));
    if( !$dbr ) {
      throw new CmsSQLErrorException($db->sql.' -- '.$db->ErrorMsg());
    }

    $this->SetMessage($this->Lang('msg_allpagesupdated'));
		$this->Redirect($id,'admin_edit_template',$returnid,array('tpl'=>$params['tpl']));
  }

  // build a display

  $cats = CmsLayoutTemplateCategory::get_all();
  $out = array();
  $out[0] = $this->Lang('prompt_none');
  if( is_array($cats) && count($cats) ) {
    foreach( $cats as $one ) {
      $out[$one->get_id()] = $one->get_name();
    }
  }
  $smarty->assign('category_list',$out);

  $userops = cmsms()->GetUserOperations();
  $allusers = $userops->LoadUsers();
  $tmp = array();
  foreach( $allusers as $one ) {
    $tmp[$one->id] = $one->username;
  }
  if( is_array($tmp) && count($tmp) ) {
    $smarty->assign('user_list',$tmp);
  }

  // lets see if we can find a content block in this template.
  try {
    $parser = cmsms()->get_template_parser(); 
    cms_utils::set_app_data('tmp_template',$tpl_obj->get_content());
    $parser->fetch('cms_template:appdata;tmp_template');

    // get content blocks
    $blocks = CMS_Content_Block::get_content_blocks();
    if( !is_array($blocks) || !count($blocks) ) {
      $smarty->assign('noblocks',1);
    }
  }
  catch ( SmartyCompilerException $e ) {
      $smarty->assign('template_error',$e->GetMessage());
  }

  $smarty->assign('extraparms',array('tpl'=>$params['tpl']));
  $type = CmsLayoutTemplateType::load($tpl_obj->get_type_id());
  $smarty->assign('template_type',$type);
  $smarty->assign('template',$tpl_obj);
  echo $this->ProcessTemplate('admin_set_all_pages.tpl');
}
catch( CmsException $e ) {
  $this->SetError($e->GetMessage());
  $this->Redirect($id,'admin_edit_template',$returnid,array('tpl'=>$params['tpl']));
}
#
# EOF
#
?>