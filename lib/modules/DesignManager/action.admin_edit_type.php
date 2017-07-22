<?php
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

$this->SetCurrentTab('types');

if( isset($params['cancel']) ) {
  $this->SetMessage($this->Lang('msg_cancelled'));
  $this->RedirectToAdminTab();
}

if( !isset($params['type']) ) {
  $this->SetError($this->Lang('error_missingparam'));
  $this->RedirectToAdminTab();
}

try {
  $type = CmsLayoutTemplateType::load($params['type']);

  if( isset($params['reset']) ) {
      $type->reset_content_to_factory();
      $type->save();
  }
  else if( isset($params['submit']) ) {
    if( isset($params['dflt_contents']) ) {
      $type->set_dflt_contents($params['dflt_contents']);
    }
    $type->set_description($params['description']);
    $type->save();

    $this->SetMessage($this->Lang('msg_type_saved'));
    $this->RedirectToAdminTab();
  }
  $smarty->assign('type',$type);
  echo $this->ProcessTemplate('admin_edit_type.tpl');
}
catch( CmsException $e ) {
  $this->SetError($e->GetMessage());
  $this->RedirectToAdminTab();
}

#
# EOF
#
?>