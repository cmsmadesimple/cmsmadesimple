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

require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'cmsms.api.php');

require_once(cms_join_path(ROOT_DIR,'lib','html_entity_decode_utf8.php'));
require_once("../lib/classes/class.template.inc.php");
$urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];

check_login();
$userid = get_userid();
$access = check_permission($userid, "Add Templates")
||
check_permission($userid, "Modify Templates")
||
check_permission($userid, "Remove Templates");


if (!$access) {
	die('Permission Denied');
return;
}


include_once("header.php");
$gCms = cmsms();
$db = cms_db();

if (isset($_GET["message"])) {
	$message = preg_replace('/\</','',$_GET['message']);
	echo '<div class="pagemcontainer"><p class="pagemessage">'.$message.'</p></div>';
}

?>

<form action="multitemplate.php<?php echo $urlext?>" method="post">
<div>
  <input type="hidden" name="<?php echo CMS_SECURE_PARAM_NAME ?>" value="<?php echo $_SESSION[CMS_USER_KEY] ?>" />
</div>
<div class="pagecontainer">
	<div class="pageoverflow">

<?php

	$userid	= get_userid();
	$add = check_permission($userid, 'Add Templates');
	$edit = check_permission($userid, 'Modify Templates');
	$all = check_permission($userid, 'Modify Any Page');
	$remove	= check_permission($userid, 'Remove Templates');
	
	$templateops = $gCms->GetTemplateOperations();

	if ($all && isset($_GET["action"]) && $_GET["action"] == "setallcontent") {
		if( (isset($_GET["template_id"])) && (is_numeric($_GET["template_id"])) ) {
			$thetemplate = cms_orm('CmsTemplate')->find_by_id($_GET['template_id']);
			if ($thetemplate->active == 1)
			{
				$query = "UPDATE {pages} SET template_id = ?, modified_date = ?";
				$result = $db->Execute($query, array($_GET['template_id'], $db->DBTimeStamp(time())));
				if ($result)
				{
					echo '<div class="pagemcontainer"><div class="pagemessage">' .$themeObject->DisplayImage('icons/system/accept.gif', lang('All Pages Modified'),'','','systemicon') . '&nbsp;' .  lang('All Pages Modified'). '</div></div>';
				}
				else
				{
					echo '<div class="pageerrorcontainer"><div class="pageoverflow">'.lang('Error Updating Pages').'</div></div>';
				}
			}
			else
			{
				echo '<p class="error">'.lang('Error Updating Template on All Pages').'</p>';
			}
		}
	}

	if (isset($_GET['setdefault']))
	{
		$templatelist = $templateops->LoadTemplates();
		foreach ($templatelist as $onetemplate)
		{
			if ($onetemplate->id == $_GET['setdefault'])
			{
				$onetemplate->default = 1;
				$onetemplate->active = 1;
				$onetemplate->Save();
			}
			else
			{
				$onetemplate->default = 0;
				$onetemplate->Save();
			}
		}
	}

	if (isset($_GET['setactive']) || isset($_GET['setinactive']))
	{
		$theid = '';
		if (isset($_GET['setactive']))
		{
			$theid = $_GET['setactive'];
		}
		if (isset($_GET['setinactive']))
		{
			$theid = $_GET['setinactive'];
		}
		$thetemplate = $templateops->LoadTemplateByID($theid);
		if (isset($thetemplate))
		{
			if (isset($_GET['setactive']))
			{
				$thetemplate->active = 1;
				$thetemplate->Save();
			}
			if (isset($_GET['setinactive']))
			{
				$thetemplate->active = 0;
				$thetemplate->Save();
			}
		}
	}

	$templatelist = $templateops->LoadTemplates();
	
	$page = 1;
	if (isset($_GET['page'])) $page = $_GET['page'];
	$limit = 20;
	if (count($templatelist) > $limit)
	{
		echo "<p class=\"pageshowrows\">".pagination($page, count($templatelist), $limit)."</p>";
	}
	echo $themeObject->ShowHeader(lang('Current Page Templates')).'</div>';
	if ($templatelist && count($templatelist) > 0) {

		echo '<table cellspacing="0" class="pagetable">';
		echo '<thead>';
		echo "<tr>\n";
		echo '<th class="pagew50">'.lang('Template').'</th>';
		echo "<th class=\"pagepos\">".lang('Default')."</th>\n";
		echo "<th class=\"pagepos\">".lang('Active')."</th>\n";
		if ($edit)
			echo "<th class=\"pagepos\">&nbsp;</th>\n";
		echo "<th class=\"pageicon\">&nbsp;</th>\n";
		if ($add)
			echo "<th class=\"pageicon\">&nbsp;</th>\n";
		if ($remove)
			echo "<th class=\"pageicon\">&nbsp;</th>\n";
		if ($all)
			echo "<th class=\"pageicon\">&nbsp;</th>\n";
		echo "<th class=\"pageicon\">&nbsp;</th>\n";

		echo "</tr>\n";
		echo '</thead>';
		echo '<tbody>';

		$currow = "row1";
		$counter=0;

		foreach ($templatelist as $onetemplate)
		{
			// construct true/false button images
            $image_true = "<a href=\"listtemplates.php".$urlext."&amp;setinactive=".$onetemplate->id."\">".$themeObject->DisplayImage('icons/system/true.gif', lang('Set to False'),'','','systemicon')."</a>";
            $image_false = "<a href=\"listtemplates.php".$urlext."&amp;setactive=".$onetemplate->id."\">".$themeObject->DisplayImage('icons/system/false.gif', lang('Set to True'),'','','systemicon')."</a>";
			$default_true =$themeObject->DisplayImage('icons/system/true.gif', lang('true'),'','','systemicon');
			$default_false ="<a href=\"listtemplates.php".$urlext."&amp;setdefault=".$onetemplate->id."\">".$themeObject->DisplayImage('icons/system/false.gif', lang('Set to True'),'','','systemicon')."</a>";

			if ($counter < $page*$limit && $counter >= ($page*$limit)-$limit) {
  			    echo "<tr class=\"$currow\" onmouseover=\"this.className='".$currow.'hover'."';\" onmouseout=\"this.className='".$currow."';\">\n";
				echo "<td><a href=\"edittemplate.php".$urlext."&amp;template_id=".$onetemplate->id."\">".$onetemplate->name."</a></td>\n";
				echo "<td class=\"pagepos\">".($onetemplate->default == 1?$default_true:$default_false)."</td>\n";
				if ($onetemplate->default)
					echo "<td class=\"pagepos\">".$themeObject->DisplayImage('icons/system/true.gif', lang('True'),'','','systemicon')."</td>\n";
				else
					echo "<td class=\"pagepos\">".($onetemplate->active == 1?$image_true:$image_false)."</td>\n";

				# set template to all content
				if ($all)
					echo "<td class=\"pagepos\"><a href=\"listtemplates.php".$urlext."&amp;action=setallcontent&amp;template_id=".$onetemplate->id."\" onclick=\"return confirm('".cms_html_entity_decode_utf8(lang('Are you sure you want to set all pages to use this template?'),true)."');\">".lang('Set All Pages')."</a></td>\n";

				# view css association
				echo "<td class=\"icons_wide\"><a href=\"listcssassoc.php".$urlext."&amp;type=template&amp;id=".$onetemplate->id."\">";
                echo $themeObject->DisplayImage('icons/system/css.gif', lang('Attach Stylesheets'),'','','systemicon');
                echo "</a></td>\n";

				# add new template
				if ($add)
				{
				  echo "<td class=\"icons_wide\"><a href=\"copytemplate.php".$urlext."&amp;template_id=".$onetemplate->id."&amp;template_name=".urlencode($onetemplate->name)."\">";
                    echo $themeObject->DisplayImage('icons/system/copy.gif', lang('Copy'),'','','systemicon');
                    echo "</a></td>\n";
				}

				# edit template
				if ($edit)
				{
					echo "<td class=\"icons_wide\"><a href=\"edittemplate.php".$urlext."&amp;template_id=".$onetemplate->id."\">";
                    echo $themeObject->DisplayImage('icons/system/edit.gif', lang('Edit'),'','','systemicon');
                    echo "</a></td>\n";
				}

				# remove template
				if ($remove)
				{
					echo "<td class=\"icons_wide\">";
					if ($onetemplate->default)
					{
						echo '&nbsp;';
					}
					else
					{
						echo "<a href=\"deletetemplate.php".$urlext."&amp;template_id=".$onetemplate->id."\" onclick=\"return confirm('".cms_html_entity_decode_utf8(lang('Are you sure you want to delete %s?', $onetemplate->name),true)."');\">";
						echo $themeObject->DisplayImage('icons/system/delete.gif', lang('Delete'),'','','systemicon');
						echo "</a>";
					}
					echo "</td>\n";
				}
				if ($onetemplate->default)
					echo '<td>&nbsp;</td>';
				else
					echo '<td><input type="checkbox" name="multitemplate-'.$onetemplate->id.'" /></td>';
				echo "</tr>\n";

				($currow=="row1"?$currow="row2":$currow="row1");

			}
			$counter++;
		}

		echo '</tbody>';
		echo "</table>\n";

	}

if ($add) {
?>
	<div class="pageoptions">
		<p class="pageoptions">
			<span style="float: left;">
				<a href="addtemplate.php<?php echo $urlext ?>">
					<?php 
						echo $themeObject->DisplayImage('icons/system/newobject.gif', lang('Add Template'),'','','systemicon').'</a>';
						echo ' <a class="pageoptions" href="addtemplate.php'.$urlext.'">'.lang("Add Template");
					?>
				</a>
			</span>
			<span style="margin-right: 30px; float: right; align: right">
				<?php echo lang("Selected Items"); ?>: <select name="multiaction">
				<option value="delete"><?php echo lang('Delete') ?></option>
				<option value="active"><?php echo lang('Active') ?></option>
				<option value="inactive"><?php echo lang('Inactive') ?></option>
				</select>
				<input type="submit" accesskey="s" value="<?php echo lang('Submit') ?>" />
			</span>
			<br />
		</p>		
	</div>
</div>
</form>
<p class="pageback"><a class="pageback" href="<?php echo $themeObject->BackUrl(); ?>">&#171; <?php echo lang('Back')?></a></p>

<?php
}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
