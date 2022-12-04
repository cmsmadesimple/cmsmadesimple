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
if( !$this->CheckPermission('Modify Templates') ) return;

$this->SetCurrentTab('categories');
if( isset($params['cancel']) ) {
  $this->SetMessage($this->Lang('msg_cancelled'));
  $this->RedirectToAdminTab();
}

try {
  $category = null;
  if( !isset($params['cat']) ) {
    $category = new CmsLayoutTemplateCategory();
    //$category->set_name('New Category');
  }
  else {
    $category = CmsLayoutTemplateCategory::load(trim($params['cat']));
  }
}
catch( CmsException $e ) {
  $this->SetError($e->GetMessage());
  $this->RedirectToAdminTab();
}

try {
  if( isset($params['submit']) ) {
      $category->set_name(strip_tags($params['name']));
      $category->set_description(strip_tags($params['description']));
      $category->save();
      $this->SetMessage($this->Lang('category_saved'));
      $this->RedirectToAdminTab();
  }
}
catch( CmsException $e ) {
  $this->ShowErrors($e->GetMessage());
}

$smarty->assign('category',$category);
echo $this->ProcessTemplate('admin_edit_category.tpl');

#
# EOF
#
?>