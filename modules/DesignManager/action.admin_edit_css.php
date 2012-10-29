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
if( !$this->CheckPermission('Modify Stylesheets') ) return;

$this->SetCurrentTab('stylesheets');
if( isset($params['cancel']) ) {
  $this->SetMessage($this->Lang('msg_cancelled'));
  $this->RedirectToAdminTab();
}

try {
  $css_ob = null;
  $extraparms = array();

  if( isset($params['css']) ) {
    $css_ob = CmsLayoutStylesheet::load($params['css']);
    $extraparms['css'] = $params['css'];
  }
  else {
    $css_ob = new CmsLayoutStylesheet();
  }

  try {
    if( isset($params['submit']) && $params['submit'] == $this->Lang('submit') ) {
      if( isset($params['name']) ) $css_ob->set_name($params['name']);
      if( isset($params['description']) ) $css_ob->set_description($params['description']);
      if( isset($params['content']) ) $css_ob->set_content($params['content']);
      $typ = array();
      if( isset($params['media_type']) ) {
	$typ = $params['media_type'];
      }
      $css_ob->set_media_types($typ);
      if( isset($params['media_query']) ) $css_ob->set_media_query($params['media_query']);
      if( $this->CheckPermission('Manage Designs') ) {
	$design_list = array();
	if( isset($params['design_list']) ) {
	  $design_list = $params['design_list'];
	}
	$css_ob->set_designs($design_list);
      }

      $css_ob->save();
      $this->SetMessage($this->Lang('msg_stylesheet_saved'));
      $this->RedirectToAdminTab();
    }
  }
  catch( CmsException $e ) {
    echo $this->ShowErrors($e->GetMessage());
  }

  // prepare to display.
  $designs = CmsLayoutCollection::get_all();
  if( is_array($designs) && count($designs) ) {
    $out = array();
    foreach( $designs as $one ) {
      $out[$one->get_id()] = $one->get_name();
    }
    $smarty->assign('design_list',$out);
  }

  $smarty->assign('has_designs_right',$this->CheckPermission('Manage Designs'));
  $smarty->assign('extraparms',$extraparms);
  $smarty->assign('css',$css_ob);
  
  echo $this->ProcessTemplate('admin_edit_css.tpl');
}
catch( CmsException $e ) {
  $this->SetError($e->GetMessage());
  $this->RedirectToAdminTab();
}

#
# EOF
#
?>
