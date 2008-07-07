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

require_once("../include.php");
require_once("../lib/classes/class.user.inc.php");

check_login();
$userid = get_userid();
$access = check_permission($userid, 'Add Users');
$assign_group_perm = check_permission($userid,'Modify Group Assignments');

global $gCms;
$db =& $gCms->GetDb();
$groupops =& $gCms->GetGroupOperations();
$group_list = $groupops->LoadGroups();

$error = "";

$user= "";
if (isset($_POST["user"])) $user = cleanValue($_POST["user"]);

$firstname = "";
if (isset($_POST["firstname"])) $firstname = cleanValue($_POST["firstname"]);

$lastname = "";
if (isset($_POST["lastname"])) $lastname = cleanValue($_POST["lastname"]);

$password= "";
if (isset($_POST["password"])) $password = $_POST["password"];

$passwordagain= "";
if (isset($_POST["passwordagain"])) $passwordagain = $_POST["passwordagain"];

$email = "";
if (isset($_POST["email"])) $email = cleanValue($_POST["email"]);

$active = 1;
if (!isset($_POST["active"]) && isset($_POST["adduser"])) $active = 0;

$adminaccess = 1;
if (!isset($_POST["adminaccess"]) && isset($_POST["adduser"])) $adminaccess = 0;

if (isset($_POST["cancel"]) || !check_permission($userid, 'Add Users'))
{
	redirect("listusers.php");
	return;
}

if (isset($_POST["adduser"]))
{
	$validinfo = true;

	if ($user == "")
	{
		$validinfo = false;
		$error .= "<li>".lang('nofieldgiven', array(lang('username')))."</li>";
	}

	if ( !preg_match("/^[a-zA-Z0-9\._ ]+$/", $user) ) {
		$validinfo = false;
		$error .= "<li>".lang('illegalcharacters', array(lang('username')))."</li>";
	} 

	if ($password == "")
	{
		$validinfo = false;
		$error .= "<li>".lang('nofieldgiven', array(lang('password')))."</li>";
	}
	else if ($password != $passwordagain)
	{
		#We don't want to see this if no password was given
		$validinfo = false;
		$error .= "<li>".lang('nopasswordmatch')."</li>";
	}

	if ($validinfo)
	{
		#$new_user_id = $db->GenID(cms_db_prefix()."users_seq");
		#$query = "INSERT INTO ".cms_db_prefix()."users (user_id, username, password, active, create_date, modified_date) VALUES ($new_user_id, ".$db->qstr($user).", ".$db->qstr(md5($password)).", $active, '".$db->DBTimeStamp(time())."', '".$db->DBTimeStamp(time())."')";
		#$result = $db->Execute($query);

		$newuser = new User();
		$newuser->username = $user;
		$newuser->SetPassword($password);
		$newuser->active = $active;
		$newuser->firstname = $firstname;
		$newuser->lastname = $lastname;
		$newuser->email = $email;
		$newuser->adminaccess = $adminaccess;
		$newuser->SetPassword($password);

		#Perform the adduser_pre callback
		foreach($gCms->modules as $key=>$value)
		{
			if ($gCms->modules[$key]['installed'] == true &&
				$gCms->modules[$key]['active'] == true)
			{
				$gCms->modules[$key]['object']->AddUserPre($newuser);
			}
		}
		
		Events::SendEvent('Core', 'AddUserPre', array('user' => &$newuser));

		$result = $newuser->save();

		if ($result)
		{
			#Perform the adduser_post callback
			foreach($gCms->modules as $key=>$value)
			{
				if ($gCms->modules[$key]['installed'] == true &&
					$gCms->modules[$key]['active'] == true)
				{
					$gCms->modules[$key]['object']->AddUserPost($newuser);
				}
			}
			
			Events::SendEvent('Core', 'AddUserPost', array('user' => &$newuser));

			# set some default preferences, based on the user creating this user
			$adminid = get_userid();
			$userid = $newuser->id;
			set_preference($userid, 'wysiwyg', get_preference($adminid, 'wysiwyg'));
			set_preference($userid, 'default_cms_language', get_preference($adminid, 'default_cms_language'));
			set_preference($userid, 'admintheme', get_site_preference('logintheme','default'));
			set_preference($userid, 'bookmarks', get_preference($adminid, 'bookmarks'));
			set_preference($userid, 'recent', get_preference($adminid, 'recent'));

			if ($assign_group_perm && isset($_POST['groups']))
            {
            $iquery = "insert into ".cms_db_prefix().
               "user_groups (user_id,group_id) VALUES (?,?)";
            foreach($group_list as $thisGroup)
               {
               if (isset($_POST['g'.$thisGroup->id]) && $_POST['g'.$thisGroup->id] == 1)
                  {
                  $result = $db->Execute($iquery,array($userid,$thisGroup->id));
                  }
               }
            }


			audit($newuser->id, $newuser->username, 'Added User');
			redirect("listusers.php");
		}
		else
		{
			$error .= "<li>".lang('errorinsertinguser')."</li>";
		}
	}
}


include_once("header.php");

if ($error != "")
{
	echo "<div class=\"pageerrorcontainer\"><ul class=\"error\">".$error."</ul></div>";
}

if (!$access) {
	echo "<div class=\"pageerrorcontainer\"><p class=\"pageerror\">".lang('noaccessto', array(lang('adduser')))."</p></div>";	
}
else {
?>

<div class="pagecontainer">
	<?php echo $themeObject->ShowHeader('adduser'); ?>
	<form method="post" action="adduser.php">		
		<div class="pageoverflow">
			<p class="pagetext">*<?php echo lang('name')?>:</p>
			<p class="pageinput"><input type="text" name="user" maxlength="255" value="<?php echo $user?>" /></p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">*<?php echo lang('password')?>:</p>
			<p class="pageinput"><input type="password" name="password" maxlength="25" value="" /></p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext">*<?php echo lang('passwordagain')?>:</p>
			<p class="pageinput"><input type="password" name="passwordagain" maxlength="25" value="" /></p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('firstname')?>:</p>
			<p class="pageinput"><input type="text" name="firstname" maxlength="50" value="<?php echo $firstname ?>" /></p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('lastname')?>:</p>
			<p class="pageinput"><input type="text" name="lastname" maxlength="50" value="<?php echo $lastname ?>" /></p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('email')?>:</p>
			<p class="pageinput"><input type="text" name="email" maxlength="255" value="<?php echo $email ?>" /></p>
		</div>
		<div class="pageoverflow">
			<p class="pagetext"><?php echo lang('active')?>:</p>
			<p class="pageinput"><input class="pagecheckbox" type="checkbox" name="active" <?php echo ($active == 1?"checked=\"checked\"":"")?> /></p>
		</div>
		<?php
      if ($assign_group_perm)
      {
      ?>
		<div class="pageoverflow">
			<div class="pagetext"><?php  echo $themeObject->DisplayImage('icons/system/permissions.gif', lang('permissions'),'','','icon-extra');?><b><?php echo lang('groups')?>:</b></div>
			<div class="pageinput">
      <?php
	     echo '<div class="group_memberships clear"><input type="hidden" name="groups" value="1" />';
        foreach($group_list as $thisGroup)
            {
            echo '<div class="group"><input type="checkbox" name="g'.$thisGroup->id.'" id="g'.$thisGroup->id.
               '" value="1" /><label for="g'.$thisGroup->id.'">'.$thisGroup->name.'</label></div>';
            }
        echo '</div>';
      ?>
         </div>
		</div>

      <?php
      }
      ?>
		
		<div class="pageoverflow">
			<p class="pagetext">&nbsp;</p>
			<p class="pageinput">
				<input type="hidden" name="adduser" value="true" />
				<input class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" type="submit" value="<?php echo lang('submit')?>" />
				<input class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" type="submit" name="cancel" value="<?php echo lang('cancel')?>" />
			</p>
		</div>
	</form>
</div>

<?php
}

echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
