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

check_login();
global $gCms;
$db =& $gCms->GetDb();

$smarty = cms_smarty();
$smarty->assign('action', 'adduserplugin.php');

$error = array();

if (isset($_POST["cancel"])) {
	redirect("listusertags.php");
	return;
}

$userid = get_userid();
$access = check_permission($userid, 'Modify User-defined Tags');

global $gCms;
$db =& $gCms->GetDb();

$submit = array_key_exists('submitbutton', $_POST);

function &get_udt_object()
{
	$udt_object = new CmsUserTag();
	if (isset($_REQUEST['udt']))
		$udt_object->update_parameters($_REQUEST['udt']);
	return $udt_object;
}
$udt_object = get_udt_object();

// Handle form submission
if ($access)
{
	if($submit)
	{
		if ($udt_object->save())
		{
			audit($udt_object->id, $udt_object->name, 'Added User Defined Tag');
			redirect("listusertags.php");
		}
	}
}


include_once("header.php");

// Assign the header
$smarty->assign('header_name', $themeObject->ShowHeader('addusertag'));

// Assign the Object
$smarty->assign('udt_object', $udt_object);

// Display the template
$smarty->display('adduserplugin.tpl');

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>



<?php
/*
$CMS_ADMIN_PAGE=1;

require_once("../include.php");

check_login();
global $gCms;
$db =& $gCms->GetDb();
$error = array();

$plugin_name= "";
if (isset($_POST["plugin_name"])) $plugin_name = $_POST["plugin_name"];

$code= "";
if (isset($_POST["code"])) $code = $_POST["code"];

if (isset($_POST["cancel"])) {
	redirect("listusertags.php");
	return;
}

$userid = get_userid();
$access = check_permission($userid, 'Modify User-defined Tags');

global $gCms;
$db =& $gCms->GetDb();

if ($access) {
	if (isset($_POST["addplugin"])) {

		$validinfo = true;
		if ($plugin_name == "") {
			$error[] = lang('nofieldgiven',array(lang('name')));
			$validinfo = false;
		}
		else
		{
			if (in_array($plugin_name, $gCms->cmsplugins))
			{
				$error[] = lang('usertagexists');
				$validinfo = false;
			}
		}
		// Make sure no spaces are put into plugin name.
		$without_spaces = str_replace(' ', '', $plugin_name);
		if ($plugin_name != $without_spaces)
		{
			$error[] = lang('error_udt_name_whitespace');
			$validinfo = false;
		}	
		if ($code == "") {
			$error[] = lang('nofieldgiven',array(lang('code')));
			$validinfo = false;
		}
		else if (strrpos($code, '{') !== FALSE)
		{
			$lastopenbrace = strrpos($code, '{');
			$lastclosebrace = strrpos($code, '}');
			if ($lastopenbrace > $lastclosebrace)
			{
				$error[] = lang('invalidcode');
				$validinfo = false;
			}
		}
		
		if ($validinfo)
		{
			srand();
			ob_start();
			if (eval('function testfunction'.rand().'() {'.$code.'}') === FALSE)
			{
				$error[] = lang('invalidcode');
                //catch the error
                //eval('function testfunction'.rand().'() {'.$code.'}');
                $buffer = ob_get_clean();
                //add error
                $error[] = preg_replace('/<br \/>/', '', $buffer ); 
				$validinfo = false;
			}
			else
			{
				ob_end_clean();
			}
		}

		if ($validinfo) {
			$new_usertag_id = $db->GenID(cms_db_prefix()."userplugins_seq");
			Events::SendEvent('Core', 'AddUserDefinedTagPre', array('id' => $new_usertag_id, 'name' => &$plugin_name, 'code' => &$code));
			$query = "INSERT INTO ".cms_db_prefix()."userplugins (userplugin_id, userplugin_name, code, create_date, modified_date) VALUES ($new_usertag_id, ".$db->qstr($plugin_name).", ".$db->qstr($code).", ".$db->DBTimeStamp(time()).", ".$db->DBTimeStamp(time()).")";
			$result = $db->Execute($query);
			if ($result) {
				Events::SendEvent('Core', 'AddUserDefinedTagPost', array('id' => $new_usertag_id, 'name' => &$plugin_name, 'code' => &$code));
				audit($new_usertag_id, $plugin_name, 'Added User Defined Tag');
				redirect("listusertags.php?message=usertagadded");
				return;
			}
			else {
				$error .= lang('errorinsertingtag');
			}
		}
	}
}

include_once("header.php");

if (!$access) {
	echo '<div class=\"pageerrorcontainer\"><p class="pageerror">'.lang('noaccessto', array(lang('addusertag'))).'</p></div>';
}
else {
    if (FALSE == empty($error)) {
        echo $themeObject->ShowErrors($error);
    }
?>

<div class="pagecontainer">
	<?php echo $themeObject->ShowHeader('addusertag'); ?>
	<form enctype="multipart/form-data" action="adduserplugin.php" method="post">
		<div class="row">
			<label>*<?php echo lang('name')?>:</label>
			<input type="text" name="plugin_name" maxlength="255" value="<?php echo $plugin_name?>" />
		</div>
		<div class="row">
			<label>*<?php echo lang('code')?></label>
			<?php echo create_textarea(false, $code, 'code', 'pagebigtextarea', 'code', '', '', '80', '15','','php')?>
		</div>
		<div class="submitrow">
			<input type="hidden" name="addplugin" value="true" />
			<input type="submit" class="pagebutton" value="<?php echo lang('submit')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
			<input type="submit" class="pagebutton" name="cancel" value="<?php echo lang('cancel')?>" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
		</div>
	</form>
</div>

<?php
}

include_once("footer.php");

# vim:ts=4 sw=4 noet
*/
?>
