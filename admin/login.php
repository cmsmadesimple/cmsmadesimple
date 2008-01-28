<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
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

$error = '';

//var_dump($_REQUEST);

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
	//redirect(CmsConfig::get('root_url') . '/index.php', true);
}

$openid_enabled = CmsOpenid::is_enabled();

if ($openid_enabled)
{
	if ((isset($_REQUEST['openid_mode']) && $_REQUEST['openid_mode'] == 'id_res') || (isset($_REQUEST['openid.mode']) && $_REQUEST['openid.mode'] == 'id_res'))
	{
		#See if the openid matches
		if (CmsOpenid::check_authentication($_REQUEST))
		{
			#Now see if the checksum actually is for a user
			$user = cms_orm()->user->find_by_checksum($_REQUEST['checksum']);
			if ($user)
			{
				#Put in a new checksum so the return url from provider can't be reused
				$checksum = CmsOpenid::generate_checksum();
				$user->checksum = $checksum;
				$user->save();

				if (CmsLogin::login_by_id($user->id))
				{
					if (isset($_SESSION['redirect_url']))
					{
						$tmp = $_SESSION['redirect_url'];
						unset($_SESSION['redirect_url']);
						CmsResponse::redirect($tmp);
					}
					else
					{
						redirect(CmsConfig::get('root_url') . '/' . CmsConfig::get('admin_dir') . '/index.php', true);
					}
				}
				else
				{
					$error .= lang('authenticationfailed 3');
				}
			}
			else
			{
				$error .= lang('authenticationfailed 2');
			}
		}
		else
		{
			$error .= lang('authenticationfailed 1');
		}
	}
}

$username = '';
if (isset($_POST['username'])) $username = CmsRequest::clean_value($_POST['username']);

$openid = '';
if (isset($_POST['openid'])) $openid = CmsRequest::clean_value($_POST['openid']);

if (isset($_POST['username']) && isset($_POST['password'])) {

	$password = '';
	if (isset($_POST['password'])) $password = $_POST['password'];

	if ($openid != '' && isset($_POST['loginsubmit']) && $openid_enabled)
	{
		#Cleanup the open id and find a user so we can set the checksum
		#before the redirect
		$clean_openid = CmsOpenid::cleanup_openid($openid);
		$user = cms_orm()->user->find_by_openid($clean_openid);
		
		if ($user)
		{
			$obj = new CmsOpenid();
			if ($obj->find_server(CmsOpenid::create_url($openid)))
			{
				#Make up a checksum and save it to the user
				$checksum = CmsOpenid::generate_checksum();
				$user->checksum = $checksum;
				$user->save();

				#All should be good.  Time to redirect out to the provider.
				$obj->do_authentication(CmsConfig::get('root_url') . '/' . CmsConfig::get('admin_dir') . '/login.php', $checksum);
			}
		}
		else
		{
			$error .= lang('usernameincorrect');
		}
	}
	else if ($username != '' && $password != '' && isset($_POST['loginsubmit']))
	{
		if (CmsLogin::login($username, $password))
		{
			// redirect to upgrade if db_schema it's old
			$current_version = $CMS_SCHEMA_VERSION;
	
			$query = 'SELECT version from '.cms_db_prefix().'version';
			$row = cms_db()->GetRow($query);
			if ($row) $current_version = $row['version'];

			if ($current_version < $CMS_SCHEMA_VERSION)
			{
				CmsResponse::redirect(CmsConfig::get('root_url') . '/install/upgrade.php');
			}
			// end of version check

			if (isset($_SESSION['redirect_url']))
			{
				$tmp = $_SESSION['redirect_url'];
				unset($_SESSION['redirect_url']);
				CmsResponse::redirect($tmp);
			}
			else
			{
				redirect(CmsConfig::get('root_url') . '/' . CmsConfig::get('admin_dir') . '/index.php', true);
			}
		}
		else
		{
			$error .= lang('usernameincorrect');
		}
	}
	else
	{
		$error .= lang('usernameincorrect');
	}
}

CmsAdminTheme::start(true);

$themeObject = CmsAdminTheme::get_instance(true);

cmsms()->variables['admintheme'] =& $themeObject;

$smarty = cms_smarty();

$smarty->assign('base_url', CmsConfig::get('root_url') . '/' . CmsConfig::get('admin_dir') . '/');
$smarty->assign('username_text', lang('username'));
$smarty->assign('password_text', lang('password'));
$smarty->assign('openid_text', lang('openid'));
$smarty->assign('logintitle_text', lang('logintitle'));
$smarty->assign('loginprompt_text', lang('loginprompt'));

$smarty->assign('submit_text', lang('submit'));
$smarty->assign('cancel_text', lang('cancel'));

$smarty->assign('username', $username);
$smarty->assign('error', $error);
$smarty->assign('openid', $openid);

$smarty->display($themeObject->theme_template_dir . 'login.tpl');

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