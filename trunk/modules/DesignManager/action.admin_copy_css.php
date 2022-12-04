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
if( !$this->CheckPermission('Manage Stylesheets') ) return;

$this->SetCurrentTab('stylesheets');
if( !isset($params['css']) ) {
  $this->SetError($this->Lang('error_missingparam'));
  $this->RedirectToAdminTab();
}
if( isset($params['cancel']) ) {
  $this->SetMessage($this->Lang('msg_cancelled'));
  $this->RedirectToAdminTab();
}

try {
  $orig_css = CmsLayoutStylesheet::load($params['css']);
  if( isset($params['submit']) || isset($params['submitandedit']) ) {
    try {
			$new_css = clone($orig_css);
			$new_css->set_name(trim($params['new_name']));
			$new_css->set_designs(array());
			$new_css->save();

      if( isset($params['submitandedit']) ) {
				$this->SetMessage($this->Lang('msg_stylesheet_copied_edit'));
				$this->Redirect($id,'admin_edit_css',$returnid,array('css'=>$new_css->get_id()));
			}
			else {
				$this->SetMessage($this->Lang('msg_stylesheet_copied'));
				$this->RedirectToAdminTab();
			}
    }
    catch( Exception $e ) {
      echo $this->ShowErrors($e->GetMessage());
    }
  }

  // build a display
	$designs = CmsLayoutCollection::get_all();
	if( count($designs) ) {
		$tmp = array();
		for( $i = 0; $i < count($designs); $i++ ) {
			$tmp2[$designs[$i]->get_id()] = $designs[$i]->get_name();
		}
		$smarty->assign('design_names',$tmp2);
	}

  $smarty->assign('css',$orig_css);
  echo $this->ProcessTemplate('admin_copy_css.tpl');
}
catch( CmsException $e ) {
  $this->SetError($e->GetMessage());
  $this->RedirectToAdminTab();
}

#
# EOF
#
?>