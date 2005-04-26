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
$CMS_TOP_MENU='layout';
$CMS_ADMIN_TITLE='currenttemplates';

require_once("../include.php");
require_once("../lib/classes/class.template.inc.php");

check_login();

include_once("header.php");

if (isset($_GET["message"])) {
	echo "<p class=\"error\">".$_GET["message"]."</p>";
}

?>
<h3><?php echo lang('currenttemplates')?></h3>

<p><a href="toplayout.php"><?php echo lang('back')?></a></p>

<?php

	$userid	= get_userid();
	$add = check_permission($userid, 'Add Templates');
	$edit = check_permission($userid, 'Modify Templates');
	$all = check_permission($userid, 'Modify Any Page');
	$remove	= check_permission($userid, 'Remove Templates');

	if ($all && isset($_GET["action"]) && $_GET["action"] == "setallcontent") {
		if (isset($_GET["template_id"])) {
			$query = "UPDATE ".cms_db_prefix()."content SET template_id = ".$_GET["template_id"];
			$result = $db->Execute($query);
			if ($result) {
				$query = "UPDATE ".cms_db_prefix()."content SET modified_date = '".$db->DBTimeStamp(time()) . "'";
				$db->Execute($query);
				echo '<p>All Pages Modified!</p>';
			} else {
				echo '<p class="error">Error updating pages</p>';
			}
		}
	}

	if (isset($_GET['setdefault']))
	{
		$templatelist = TemplateOperations::LoadTemplates();
		foreach ($templatelist as $onetemplate)
		{
			if ($onetemplate->id == $_GET['setdefault'])
			{
				$onetemplate->default = 1;
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
		$thetemplate = TemplateOperations::LoadTemplateByID($theid);
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

	$templatelist = TemplateOperations::LoadTemplates();
	
	$page = 1;
	if (isset($_GET['page']))$page = $_GET['page'];
	$limit = 20;
	if (count($templatelist) > $limit)
	{
		echo "<div align=\"right\" class=\"clearbox\">".pagination($page, count($templatelist), $limit)."</div>";
	}

	if ($templatelist && count($templatelist) > 0) {

		echo "<table cellspacing=\"0\" class=\"AdminTable\" style=\"width: 500px;\">"."\n";
		echo '<thead>';
		echo "<tr>\n";
		echo "<th width=\"50%\">".lang('template')."</th>\n";
		echo "<th>".lang('default')."</th>\n";
		echo "<th>".lang('active')."</th>\n";
		if ($edit)
			echo "<th>&nbsp;</th>\n";
		echo "<th width=\"16\">&nbsp;</th>\n";
		if ($add)
			echo "<th width=\"16\">&nbsp;</th>\n";
		if ($remove)
			echo "<th width=\"16\">&nbsp;</th>\n";
		if ($all)
			echo "<th width=\"16\">&nbsp;</th>\n";

		echo "</tr>\n";
		echo '</thead>';
		echo '<tbody>';

		$currow = "row1";
		$counter=0;

		foreach ($templatelist as $onetemplate)
		{
			// construct true/false button images
            $image_true = "<a href=\"listtemplates.php?setinactive=".$onetemplate->id."\">".$themeObject->DisplayImage('true.gif', lang('true'))."</a>";
            $image_false = "<a href=\"listtemplates.php?setactive=".$onetemplate->id."\">".$themeObject->DisplayImage('false.gif', lang('false'))."</a>";
			$default_true =$themeObject->DisplayImage('true.gif', lang('true'));
			$default_false ="<a href=\"listtemplates.php?setdefault=".$onetemplate->id."\">".$themeObject->DisplayImage('false.gif', lang('false'))."</a>";

			if ($counter < $page*$limit && $counter >= ($page*$limit)-$limit) {
				echo "<tr class=\"$currow\">\n";
				echo "<td><a href=\"edittemplate.php?template_id=".$onetemplate->id."\">".$onetemplate->name."</a></td>\n";
				echo "<td align=\"center\">".($onetemplate->default == 1?$default_true:$default_false)."</td>\n";
				echo "<td align=\"center\">".($onetemplate->active == 1?$image_true:$image_false)."</td>\n";

				# set template to all content
				if ($all)
					echo "<td align=\"center\"><a href=\"listtemplates.php?action=setallcontent&amp;template_id=".$onetemplate->id."\" onclick=\"return confirm('".lang('setallcontentconfirm')."');\">".lang('setallcontent')."</a></td>\n";

				# view css association
				echo "<td width=\"16\"><a href=\"listcssassoc.php?type=template&amp;id=".$onetemplate->id."\">";
                echo $themeObject->DisplayImage('css.gif', lang('attachstylesheets'));
                echo "</a></td>\n";

				# add new template
				if ($add)
				    {
					echo "<td width=\"16\"><a href=\"copytemplate.php?template_id=".$onetemplate->id."\">";
                    echo $themeObject->DisplayImage('copy.gif', lang('copy'));
                    echo "</a></td>\n";
                    }

				# edit template
				if ($edit)
				    {
					echo "<td width=\"16\"><a href=\"edittemplate.php?template_id=".$onetemplate->id."\">";
                    echo $themeObject->DisplayImage('edit.gif', lang('edit'));
                    echo "</a></td>\n";
                    }

				# remove template
				if ($remove)
				    {
					echo "<td width=\"16\"><a href=\"deletetemplate.php?template_id=".$onetemplate->id."\" onclick=\"return confirm('".lang('deleteconfirm')."');\">";
                    echo $themeObject->DisplayImage('delete.gif', lang('delete'));
                    echo "</a></td>\n";
        }
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

<div class="button"><a href="addtemplate.php"><?php echo lang('addtemplate')?></a></div><br />

<?php
}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
