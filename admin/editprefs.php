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

$CMS_ADMIN_PAGE=1;

$default_cms_lang = "";
if (isset($_POST["default_cms_lang"])) $default_cms_lang = $_POST["default_cms_lang"];
$old_default_cms_lang = "";
if (isset($_POST["old_default_cms_lang"])) $old_default_cms_lang = $_POST["old_default_cms_lang"];

if ($default_cms_lang != $old_default_cms_lang && $default_cms_lang != "")
{
	$_POST['change_cms_lang'] = $default_cms_lang;
}

require_once("../include.php");

check_login();

$error = "";

$use_wysiwyg = "1";
if (isset($_POST["use_wysiwyg"])) $use_wysiwyg = $_POST["use_wysiwyg"];
//$use_javasyntax = "1";
//if (isset($_POST["use_javasyntax"]))$use_javasyntax = $_POST["use_javasyntax"];

$userid = get_userid();

if (isset($_POST["cancel"])) {
	redirect("index.php");
	return;
}

if (isset($_POST["submit_form"])) {
	set_preference($userid, 'use_wysiwyg', $use_wysiwyg);
	//set_preference($userid, 'use_javasyntax', $use_javasyntax);
	set_preference($userid, 'default_cms_language', $default_cms_lang);
	audit(-1, '', 'Edited User Preferences');
	redirect("index.php");
	return;
} else if (!isset($_POST["edituserprefs"])) {
	$use_wysiwyg = get_preference($userid, 'use_wysiwyg');
	//$use_javasyntax = get_preference($userid, 'use_javasyntax');
    //if($use_javasyntax == null)$use_javasyntax = false;
	$default_cms_lang = get_preference($userid, 'default_cms_language');
	$old_default_cms_lang = $default_cms_lang;
}

include_once("header.php");

if ($error != "") {
	echo "<ul class=\"error\">".$error."</ul>";
}

?>
<form name="prefsform" method="post" action="editprefs.php">

<div class="adminformSmall">

<h3><?php echo lang("userprefs")?></h3>

<applet code="org.CMSMadeSimple.Syntax.Hidden.class" archive="SyntaxHighlight.jar" name="hiddenApplet" width="0" height="0" style="width: 0; height: 0;">
</applet>

<?php 
/*
echo '<script language="JavaScript" type="text/javascript">'.
    'function syntaxSupport(){ '.
        'try{'.
            'document.hiddenApplet.myMethod();'.
        '}catch(err){'.
            'alert(\''.lang('nosyntax').'\');'.
            'document.prefsform.use_javasyntax.selectedIndex = 0;'.
        '}'.
    '}'.
'</script>';
*/
?>

<table border="0" align="center">

	<tr>
		<td><?php echo lang("usewysiwyg")?>:</td>
		<td>
        
        
			<select name="use_wysiwyg">
				<option value="0" <?php echo  ($use_wysiwyg=="0"?"selected":"") ?>><?php echo lang('false')?></option>
				<option value="1" <?php echo  ($use_wysiwyg=="1"?"selected":"") ?>><?php echo lang('true')?></option>
			</select>
		</td>
	</tr>
	<!--
	<tr>
		<td><?php echo lang("usejavasyntax")?>:</td>
		<td>
			<select name="use_javasyntax"  onChange="syntaxSupport()">
				<option value="0" <?php echo  ($use_javasyntax=="0"?"selected":"") ?>><?php echo lang('false')?></option>
				<option value="1" <?php echo  ($use_javasyntax=="1"?"selected":"") ?>><?php echo lang('true')?></option>
			</select>
		</td>
	</tr>
	-->
	<TR>
		<TD><?php echo lang('language')?>:</TD>
		<TD>
			<SELECT CLASS="smallselect" NAME="default_cms_lang" onChange="document.prefsform.submit();" STYLE="vertical-align: middle;">
			<option value=""><?php echo lang('nodefault') ?></option>
			<?php
				asort($nls["language"]);
				foreach ($nls["language"] as $key=>$val) {
					echo "<option value=\"$key\"";
					if ($default_cms_lang == $key) {
						echo " selected";
					}
					echo ">$val";
					if (isset($nls["englishlang"][$key]))
					{
						echo " (".$nls["englishlang"][$key].")";
					}
					echo "</option>\n";
				}
			?>
			</SELECT>
		</TD>
	</TR>
	<tr>
		<td colspan="2" align="center"><input type="hidden" name="edituserprefs" value="true"><input type="hidden" name="old_default_cms_lang" value="<?php echo $old_cms_default_lang ?>">
		<input type="submit" name="submit_form" value="<?php echo lang('submit')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'">
		<input type="submit" name="cancel" value="<?php echo lang('cancel')?>" class="button" onmouseover="this.className='buttonHover'" onmouseout="this.className='button'"></td>
	</tr>

</table>

</div>

</FORM>

<?php

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
