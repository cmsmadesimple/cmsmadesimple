<?php
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.sf.net
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

$CMS_ADMIN_PAGE=1;

require_once('../include.php');

if (isset($_SESSION['logout_user_now']))
{
	unset($_SESSION['login_user_username']);
	unset($_SESSION['logout_user_now']);
	unset($_SESSION['cms_admin_username']);
	unset($_SESSION['cmsms_user_id']);
	CmsLogin::logout();
}

if (isset($_POST['logincancel']))
{
	redirect(CmsConfig::get('root_url') . '/index.php', true);
}

$redirect_url = CmsConfig::get('root_url') . '/' . CmsConfig::get('admin_dir') . '/index.php';
$username = '';
$openid = '';
$error = '';
CmsLogin::handle_login_request($redirect_url, $username, $openid, $error, true);

/*
TODO we need this were?


CmsAdminTheme::start(true);
$themeObject = CmsAdminTheme::get_instance(true);
cmsms()->variables['admintheme'] =& $themeObject;
*/

$theme=
CmsApplication::get_preference('logintheme', 'default');

$theme_template_dir_login = dirname(dirname(__FILE__)) . '/' . CmsConfig::get('admin_dir') . '/themes/' . $theme . '/templates/';

$smarty = cms_smarty();

$smarty->assign('base_url', CmsConfig::get('root_url') . '/' . CmsConfig::get('admin_dir') . '/');

$smarty->assign('submit_text', lang('submit'));
$smarty->assign('cancel_text', lang('cancel'));

$smarty->assign('username', $username);
$smarty->assign('error', $error);
$smarty->assign('openid', $openid);

$smarty->display($theme_template_dir_login . 'login.tpl');

?>

<?php
if (CmsConfig::get('debug'))
{
	foreach ($gCms->errors as $globalerror)
	{
		echo $globalerror;
	}
}
# vim:ts=4 sw=4 noet
?>