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

if (!isset($gCms)) exit;
if( !$this->CheckPermission( 'Modify Site Preferences' ) ) return;

$this->SetPreference('default_category', $params['default_category']);
$this->SetPreference('formsubmit_emailaddress', $params['formsubmit_emailaddress']);
$this->SetPreference('email_subject',trim($params['email_subject']));
$this->SetTemplate('email_template',$params['email_template']);
$this->SetPreference('allowed_upload_types', $params['allowed_upload_types']);
$this->SetPreference('hide_summary_field', (isset($params['hide_summary_field'])?'1':'0'));
$this->SetPreference('allow_summary_wysiwyg', (isset($params['allow_summary_wysiwyg'])?'1':'0'));
$this->SetPreference('expired_searchable', (isset($params['expired_searchable'])?'1':'0'));
$this->SetPreference('expired_viewable', (isset($params['expired_viewable'])?'1':'0'));
$this->SetPreference('expiry_interval', $params['expiry_interval']);
$this->SetPreference('fesubmit_status', $params['fesubmit_status']);
$this->SetPreference('fesubmit_redirect', trim($params['fesubmit_redirect']));
$this->SetPreference('detail_returnid',(int)$params['detail_returnid']);
$this->SetPreference('allow_fesubmit',(int)$params['allow_fesubmit']);
$this->SetPreference('alert_drafts',(int)$params['alert_drafts']);
$this->SetPreference('url_prefix', ( !empty($params['url_prefix'] ) ? trim($params['url_prefix']) : 'news'));

$this->CreateStaticRoutes();
$params = array('tab_message'=> 'optionsupdated', 'active_tab' => 'options');
$this->SetMessage($this->Lang('optionsupdated'));
$this->RedirectToAdminTab('options','','admin_settings');

#
# EOF
#