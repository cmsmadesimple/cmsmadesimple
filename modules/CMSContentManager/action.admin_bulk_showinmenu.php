<?php
#---------------------------------------------------------------------------
# CMS Made Simple - Power for the professional, Simplicity for the end user.
# (c) 2004 - 2011 by Ted Kulp
# (c) 2011 - 2018 by the CMS Made Simple Development Team
# (c) 2018 and beyond by the CMS Made Simple Foundation
# This project's homepage is: https://www.cmsmadesimple.org
#---------------------------------------------------------------------------
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
#---------------------------------------------------------------------------

if( !isset($gCms) ) exit;

$this->SetCurrentTab('pages');
if( !$this->CheckPermission('Manage All Content') ) {
  $this->SetError($this->Lang('error_bulk_permission'));
  $this->RedirectToAdminTab();
}
if( !isset($params['multicontent']) ) {
  $this->SetError($this->Lang('error_missingparam'));
  $this->RedirectToAdminTab();
}

$showinmenu = 1;
if( isset($params['showinmenu']) ) $showinmenu = (int)$params['showinmenu'];

$multicontent = unserialize(base64_decode($params['multicontent']));
if( count($multicontent) == 0 ) {
  $this->SetError($this->Lang('error_missingparam'));
  $this->RedirectToAdminTab();
}

// do the real work
try {
  ContentOperations::get_instance()->LoadChildren(-1,FALSE,TRUE,$multicontent);
  $hm = cmsms()->GetHierarchyManager();

  foreach( $multicontent as $pid ) {
    $node = $hm->find_by_tag('id',$pid);
    if( !$node ) continue;
    $content = $node->getContent(FALSE,FALSE,TRUE);
    if( !is_object($content) ) continue;
    $content->SetShowInMenu($showinmenu);
    $content->SetLastModifiedBy(get_userid());
    $content->Save();
  }
  audit('','Core','Changed show-in-menu status on '.count($multicontent).' pages');
  $this->SetMessage($this->Lang('msg_bulk_successful'));
}
catch( Exception $e ) {
  $this->SetError($e->GetMessage());
}
$this->RedirectToAdminTab();

#
# EOF
#