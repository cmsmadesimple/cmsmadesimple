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

require_once("../include.php");

check_login();

include_once("header.php");

if (isset($_GET["message"])) {
    echo "<p class=\"error\">".$_GET["message"]."</p>";
}

# getting variables
$type	= isset($_GET["type"])	? $_GET["type"]	: "template" ;
$id		= isset($_GET["id"])	? $_GET["id"]	: 1 ;
$name	= "";

if ($type == "template") {

	$query = "SELECT template_name FROM ".cms_db_prefix()."templates WHERE template_id = $id";
	$result = $db->Execute($query);

	if ($result) {

		$line = $result->FetchRow();
		$name = $line["template_name"];

	}
}

?>
<h3><?=$gettext->gettext("Current CSS associations")?> - <?=$type?> : <?=$name?></h3>
<?php

	$userid = get_userid();

	$modifyall = check_permission($userid, 'Edit CSS associations');

	$query = "SELECT assoc_css_id, css_name FROM ".cms_db_prefix()."css_assoc, ".cms_db_prefix()."css
		WHERE assoc_type='$type' AND assoc_to_id = '$id' AND assoc_css_id = css_id";
	$result = $db->Execute($query);

	echo '<table cellspacing="0" class="admintable">'."\n";
	echo "<tr>\n";
	echo "<td>".$gettext->gettext("Title")."</td>\n";
	echo "<td>&nbsp;</td>\n";
	echo "</tr>\n";

	if ($result) {

		$count = 1;

		$currow = "row1";

		while ($one = $result->FetchRow()) {

			echo "<tr class=\"$currow\">\n";
			echo "<td>".$one["css_name"]."</td>\n";
			echo "<td width=\"18\"><a href=\"deletecssassoc.php?id=$id&css_id=".$one["assoc_css_id"]."&type=$type\" onclick=\"return confirm('".$gettext->gettext("Are you sure you want to delete?")."');\"><img src=\"../images/cms/delete.gif\" width=\"16\" height=\"16\" border=\"0\" alt=\"".gettext("Delete")."\"></a></td>\n";
			echo "</tr>\n";

			$count++;

			($currow == "row1"?$currow="row2":$currow="row1");

		} ## foreach
	}

	echo "</table>\n";

	if (check_permission($userid, 'Edit CSS associations')) {

		$dropdown = "";

		# we generate the dropdown
		$query = "SELECT * FROM ".cms_db_prefix()."css ORDER BY css_name";
		$result = $db->Execute($query);

		if ($result) {

			$form = "<form action=\"addcssassoc.php\" method=\"post\">";
			
			$dropdown = "<select name=\"css_id\">\n";
			while ($line = $result->FetchRow())
				$dropdown .= "<option value=\"".$line["css_id"]."\">".$line["css_name"]."</option>\n";
			$dropdown .= "</select>";

			echo $form.$dropdown;
?>

<input type="hidden" name="id" value="<?=$id?>"/>
<input type="hidden" name="type" value="<?=$type?>"/>
<input type="submit" value="<?=$gettext->gettext("Add CSS")?>"/>
</form>

<?
		}
	}


include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
