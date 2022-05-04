<?php
#BEGIN_LICENSE
#-------------------------------------------------------------------------
# Module: CMSContentManager (c) 2013 by Robert Campbell
#         (calguy1000@cmsmadesimple.org)
#  A module for managing content in CMSMS.
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2004 by Ted Kulp (wishy@cmsmadesimple.org)
# Visit our homepage at: http://www.cmsmadesimple.org
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# However, as a special exception to the GPL, this software is distributed
# as an addon module to CMS Made Simple.  You may not use this software
# in any Non GPL version of CMS Made simple, or in any version of CMS
# Made simple that does not indicate clearly and obviously in its admin
# section that the site was built with CMS Made simple.
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
#END_LICENSE
if( !isset($gCms) ) exit;

//
// init
//
$this->SetCurrentTab('pages');

//
// validation
//
if( !isset($params['page']) ) {
  $this->SetError($this->Lang('error_missingparam'));
  $this->RedirectToAdminTab();
}
$content_id = (int)$params['page'];
if( $content_id < 1 ) {
  $this->SetError($this->Lang('error_missingparam'));
  $this->RedirectToAdminTab();
}

//
// get the data
//
if( !$this->CanEditContent($content_id) ) {
  $this->SetError($this->Lang('error_copy_permission'));
  $this->RedirectToAdminTab();
}

$hm = cmsms()->GetHierarchyManager();
$node = $hm->find_by_tag('id',$content_id);
if( !$node ) {
  $this->SetError($this->Lang('error_invalidpageid'));
  $this->RedirectToAdminTab();
}
$from_obj = $node->GetContent(FALSE,FALSE,FALSE);
if( !$from_obj ) {
  $this->SetError($this->Lang('error_invalidpageid'));
  $this->RedirectToAdminTab();
}
$from_obj->GetAdditionalEditors();
$from_obj->HasProperty('anything'); // forces properties to be loaded.

$to_obj = clone $from_obj;
$to_obj->SetURL('');
$to_obj->SetName('Copy of '.$from_obj->Name());
$to_obj->SetMenuText('Copy of '.$from_obj->MenuText());
$to_obj->SetAlias();
$to_obj->SetDefaultContent(0);
$to_obj->SetOwner(get_userid());
$to_obj->SetLastModifiedBy(get_userid());
$_SESSION['__cms_copy_obj__'] = ['type'=>$to_obj->Type(), 'obj'=>serialize($to_obj)];
$this->Redirect($id,'admin_editcontent','',array('content_id'=>-1)); // do not resemble a new page (id == 0)

#
# EOF
#
