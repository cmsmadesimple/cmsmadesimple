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
if( !$this->CheckPermission('Manage Stylesheets') ) return;

if( isset($params['allparms']) ) {
  $params = array_merge($params,unserialize(base64_decode($params['allparms'])));
}

$this->SetCurrentTab('stylesheets');

if( !isset($params['css_bulk_action']) || !isset($params['css_select']) ||
    !is_array($params['css_select']) || count($params['css_select']) == 0 ) {
  $this->SetError($this->Lang('error_missingparam'));
  $this->RedirectToAdminTab();
}
if( isset($params['cancel']) ) {
  $this->SetMessage($this->Lang('msg_cancelled'));
  $this->RedirectToAdminTab();
}

switch( $params['css_bulk_action'] ) {
 case 'delete':
   if( isset($params['submit']) ) {
     if( !isset($params['check1']) || !isset($params['check2']) ) {
       echo $this->ShowErrors($this->Lang('error_notconfirmed'));
     }
     else {
       $stylesheets = CmsLayoutStylesheet::load_bulk($params['css_select']);
       foreach( $stylesheets as $one ) {
	 if( in_array($one->get_id(),$params['css_select']) ) {
	   $one->delete();
	 }
       }

       $this->SetMessage($this->Lang('msg_bulkop_complete'));
       $this->RedirectToAdminTab();
     }
   }
   break;

 default:
   $this->SetError($this->Lang('error_missingparam'));
   $this->RedirectToAdminTab();
   break;
}

$templates = CmsLayoutStylesheet::load_bulk($params['css_select']);
$smarty->assign('bulk_op','bulk_action_delete_css');
$allparms = base64_encode(serialize(array('css_select'=>$params['css_select'],
					  'css_bulk_action'=>$params['css_bulk_action'])));
$smarty->assign('allparms',$allparms);
$smarty->assign('templates',$templates);

echo $this->ProcessTemplate('admin_bulk_css.tpl');

#
# EOF
#
?>