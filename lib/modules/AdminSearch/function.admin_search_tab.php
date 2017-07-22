<?php
#-------------------------------------------------------------------------
# Module: AdminSearch - A CMSMS addon module to provide admin side search capbilities.
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
if( !$this->VisibleToAdminUser() ) exit;

$smarty->assign('formstart',$this->CreateFormStart($id,'admin_search',$returnid));
$smarty->assign('formend',$this->CreateFormEnd());
$url = $this->create_url($id,'admin_search');
$url = str_replace('&amp;','&',$url).'&showtemplate=false';
$smarty->assign('ajax_url',$url);
$smarty->assign('js_url',$this->GetModuleURLPath().'/lib/admin_search_tab.js');

$userid = get_userid();
$tmp = get_preference($userid,$this->GetName().'saved_search');
if( $tmp ) {
  $smarty->assign('saved_search',unserialize($tmp));
}

$slaves = AdminSearch_tools::get_slave_classes();
$smarty->assign('slaves',$slaves);

echo $this->ProcessTemplate('admin_search_tab.tpl');
#
# EOF
#
?>