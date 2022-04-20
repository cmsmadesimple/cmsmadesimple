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
if( isset($params['cancel']) ) {
  if( $params['cancel'] == $this->Lang('cancel') ) {
    $this->SetMessage($this->Lang('msg_cancelled'));
  }
  $this->RedirectToAdminTab();
}

try {
  if( !isset($params['css']) ) throw new CmsException($this->Lang('error_missingparam'));

  $css_ob = CmsLayoutStylesheet::load($params['css']);

  if( isset($params['submit']) ) {
    if( !isset($params['check1']) || !isset($params['check2']) ) {
      echo $this->ShowErrors($this->Lang('error_notconfirmed'));
    }
    else {
      $css_ob->delete();
      $this->SetMessage($this->Lang('msg_stylesheet_deleted'));
      $this->RedirectToAdminTab();
    }
  }

  $smarty->assign('css',$css_ob);
  echo $this->ProcessTemplate('admin_delete_css.tpl');
}
catch( CmsException $e ) {
  $this->SetError($e->GetMessage());
  $this->RedirectToAdminTab();
}

#
# EOF
#
?>