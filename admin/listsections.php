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

check_login($config);

include_once("header.php");

if (isset($_GET["message"])) {
	echo "<p class=\"error\">".$_GET["message"]."</p>";
}

?>
<h3><?=$gettext->gettext("Current Sections")?></h3>
<?php

	$userid = get_userid();
	$edit = check_permission($config, $userid, 'Modify Section');
	$remove = check_permission($config, $userid, 'Remove Section');

	global $sorted_sections;
	$db = $config->db;

	$query = "SELECT section_id, section_name, active, parent_id, item_order FROM ".$config->db_prefix."sections ORDER BY item_order";
	$result = $db->Execute($query);

	$totalcount = count($result);

	while($row = $result->FetchRow()) {

		# storing there all sections infos
		$tabresult[$row["section_id"]] = array(
			"section_id"	=> $row["section_id"],
			"section_name"	=> $row["section_name"],
			"active"		=> $row["active"],
			"parent_id"		=> $row["parent_id"],
			"item_order"	=> $row["item_order"]
			);

		# quick access to name from id
		$tab_id_to_name[$row["section_id"]]	= $row["section_name"];
		# quick access to parent from id
		$tab_id_to_parent[$row["section_id"]]	= $row["parent_id"];
	}
	
	if ($totalcount > 0) {
	
		echo '<table cellspacing="0" class="admintable">'."\n";
		echo "<tr>\n";
		echo "<td>".$gettext->gettext("Section")."</td>\n";
		echo "<td>".$gettext->gettext("Active")."</td>\n";
		echo "<td>".$gettext->gettext("Parent")."</td>\n";
		echo "<td align=\"center\">".$gettext->gettext("Move")."</td>\n";
		if ($edit) {
			echo "<td>&nbsp;</td>\n";
		}
		echo "</tr>\n";
		
		$params = array(
			"tabresult"			=> $tabresult,
			"tab_id_to_name"	=> $tab_id_to_name,
			"tab_id_to_parent"	=> $tab_id_to_parent
			);
		echo show_section_line($params);
		
		echo "</table>\n";
	}

if (check_permission($config, $userid, 'Add Section')) {
?>

<div class=button><a href="addsection.php"><?=$gettext->gettext("Add New Section")?></div></p>

<?php
}

include_once("footer.php");


# needed extra functions

function show_section_line($params) {

    global $gettext;

    $tabresult		= $params["tabresult"];
    $tab_id_to_name	= $params["tab_id_to_name"];
    $tab_id_to_parent	= $params["tab_id_to_parent"];
    # the current parent
    $parent		= $params["parent"] ? $params["parent"] : 0;
    # the current sublevel
    $level		= $params["level"] ? $params["level"] : 0;
    # the current level text (ie 1, 1.5, 5.3.4, etc)
    $totlevel		= $params["totlevel"] ? $params["totlevel"] : "";
    # the current row color
    $currow		= $params["currow"] ? $params["currow"] : "row1";

    $string = "";
    
    $tabchild = array_keys($tab_id_to_parent, $parent);

    if (is_array($tabchild)) {

	foreach ($tabchild as $key => $id) {

	    # we build the new level text : 1.1 => 1.1.1 for example
	    $totlevelnew = $totlevel.$tabresult[$id]["item_order"].".";

	    if (count($tabchild) > 1) {
		
		if ($key == 0 && count($tabchild)) {
		    
		    $move = "<a href=\"movesection.php?direction=down&section_id=".$id."&parent_id=".$parent."\">".
		    "<img src=\"../images/arrow-d.png\" alt=\"".$gettext->gettext("Down")."\" border=\"0\" /></a>";
		    
		} else if ($key == count($tabchild) - 1) {
		    
		    $move = "<a href=\"movesection.php?direction=up&section_id=".$id."&parent_id=".$parent."\">".
		    "<img src=\"../images/arrow-u.png\" alt=\"".$gettext->gettext("Up")."\" border=\"0\" /></a>";
		    
		} else {
		    
		    $move = "<a href=\"movesection.php?direction=down&section_id=".$id."&parent_id=".$parent."\">".
		    "<img src=\"../images/arrow-d.png\" alt=\"".$gettext->gettext("Down")."\" border=\"0\" /></a>&nbsp;".
		    "<a href=\"movesection.php?direction=up&section_id=".$id."&parent_id=".$parent."\">".
		    "<img src=\"../images/arrow-u.png\" alt=\"".$gettext->gettext("Up")."\" border=\"0\" /></a>";
		    
		}
	    }

	    # building params for selectbox
	    $boxparams = array(
		"tabresult"		=> $tabresult,
		"tab_id_to_name"	=> $tab_id_to_name,
		"tab_id_to_parent"	=> $tab_id_to_parent,
		"current_id"		=> $id,
		"current_parent"	=> $parent
		);

	    $string .= "<tr class=\"$currow\">\n";
	    
	    $string .= "<td>".$totlevelnew." ".$tab_id_to_name[$id]."</td>\n";
	    
	    $string .= "<td>".($tabresult[$id]["active"] == 1 ? $gettext->gettext("True") : $gettext->gettext("False"))."</td>\n";
	    
	    $string .= "<td><select onchange=\"location.href='movesection.php?direction=level&section_id=".$id.
		"&parent_id_ori=".$tabresult[$id]["parent_id"]."&item_order=".$tabresult[$id]["item_order"].
		"&parent_id='+this.options[this.selectedIndex].value;\"><option value=\"0\">".$gettext->gettext("None")."</option>".
		show_section_select($boxparams)."</select></td>";
	    
	    $string .= "<td align=\"center\">$move</td>\n";
	    
	    $string .= "<td><a href=\"editsection.php?section_id=".$id."&parent_id=".$parent."\">".$gettext->gettext("Edit")."</a></td>\n";
	    
	    $string .= "<td><a href=\"deletesection.php?section_id=".$id."\" onclick=\"return confirm('".
		$gettext->gettext("Are you sure you want to delete?")."');\">".$gettext->gettext("Delete")."</a></td>\n";
	    
	    $string .= "</tr>\n";

	    # updating params for next call
	    $params["parent"] = $id;
	    $params["level"] = $level+1;
	    $params["totlevel"] = $totlevelnew;
	    $params["currow"] = $currow;
	    $string .= show_section_line($params);
	}
    }

    return $string;
}

function show_section_select($boxparams) {

    # the current level text (ie 1, 1.5, 5.3.4, etc)
    $totlevel = $boxparams["totlevel"] ? $boxparams["totlevel"] : "";

    $string = "";
    
    # this gives an array containing all childs of current parent section
    $tabchild = array_keys($boxparams["tab_id_to_parent"], $boxparams["parent"] ? $boxparams["parent"] : 0);

    if (is_array($tabchild)) {

		foreach ($tabchild as $key => $id) {
			
			# we build the new level text : 1.1 => 1.1.1 for example
			$totlevelnew = $totlevel.$boxparams["tabresult"][$id]["item_order"].".";

			# because a section can not have has parent one of its own subsection
			if ($id != $boxparams["current_id"]) {

			# preparing params for next call
			$boxparams["parent"] = $id;
			$boxparams["totlevel"] = $totlevelnew;
			
			$string .= "<option value=\"$id\"".($id == $boxparams["current_parent"] ? " selected" : "").">".
				$totlevelnew." ".$boxparams["tab_id_to_name"][$id]."</option>";
			$string .= show_section_select($boxparams);
			}
		}
    }
    return $string;
}

# vim:ts=4 sw=4 noet
?>
