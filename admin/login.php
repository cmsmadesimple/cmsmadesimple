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

if (isset($_POST['username']) && isset($_POST['password'])) {

	$username = '';
	if (isset($_POST['username'])) $username = CmsRequest::clean_value($_POST['username']);

	$password = '';
	if (isset($_POST['password'])) $password = $_POST['password'];

	if ($username != '' && $password != '' && isset($_POST['loginsubmit']))
	{
		$oneuser = cmsms()->cms_user->find_by_username($username);

		if ($oneuser != null && $oneuser->password == md5($password))
		{
			CmsLogin::generate_user_object($oneuser->id);

			#Now call the event
			CmsEvents::SendEvent('Core', 'LoginPost', array('user' => &$oneuser));

			// redirect to upgrade if db_schema it's old
			$current_version = $CMS_SCHEMA_VERSION;
	
			$query = 'SELECT version from '.cms_db_prefix().'version';
			$row = cms_db()->GetRow($query);
			if ($row) $current_version = $row['version'];

			if ($current_version < $CMS_SCHEMA_VERSION)
			{
				redirect(CmsConfig::get('root_url') . '/install/upgrade.php');
			}
			// end of version check
			
			audit($oneuser->id, $oneuser->username, 'User Login');

			if (isset($_SESSION['redirect_url']))
			{
				$tmp = $_SESSION['redirect_url'];
				unset($_SESSION['redirect_url']);
				redirect($tmp);
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

$theme=get_site_preference('logintheme', 'default');

if (file_exists(dirname(__FILE__)."/themes/$theme/login.php"))
{
	include(dirname(__FILE__)."/themes/$theme/login.php");
}
else
{
	include(dirname(__FILE__)."/themes/default/login.php");
}
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