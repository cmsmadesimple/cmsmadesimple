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

$errors = "";

$dir = $config["uploads_path"];
$url = $config["uploads_url"];

$reldir = "";
if (isset($_POST['reldir'])) $reldir = $_POST['reldir'];
else if (isset($_GET['reldir'])) $reldir = $_GET['reldir'];

if (strpos($reldir, '..') === false)
{
	$dir .= $reldir;
}

$userid = get_userid();
$access = check_permission($userid, 'Modify Files');

#Did we upload a file?
if (isset($_FILES) && isset($_FILES['uploadfile']) && isset($_FILES['uploadfile']['name']) && $_FILES['uploadfile']['name'] != "")
{
	if ($access)
	{
		if (!move_uploaded_file($_FILES['uploadfile']['tmp_name'], $dir."/".$_FILES['uploadfile']['name']))
		{
			$errors .= "<li>".$gettext->gettext("File could not be uploaded.  Permissions problem?")."</li>";
		}
	}
	else
	{
		$errors .= "<li>".$gettext->gettext("You need the 'Modify Files' permission to perform that function.")."</li>";
	}
}

#Did we create a new dir?
if (isset($_POST['newdirsubmit']))
{
	if ($access)
	{
		#Make sure it isn't an empty dir name
		if ($_POST['newdir'] != "")
		{
			#Make sure no one tries to .. their way out of our little chroot
			if (strpos($_POST['newdir'], '..') === false)
			{
				mkdir($dir . "/" . $_POST['newdir']);
			}
			else
			{
				$errors .= "<li>".$gettext->gettext("Directory name cannot contain '..'")."</li>";
			}
		}
		else
		{
			$errors .= "<li>".$gettext->gettext("No directory name given")."</li>";
		}
	}
	else
	{
		$errors .= "<li>".$gettext->gettext("You need the 'Modify Files' permission to perform that function.")."</li>";
	}
}

if (isset($_GET['action']) && $_GET['action'] == "deletefile")
{
	if ($access)
	{
		if (is_file($dir . "/" . $_GET['file']))
		{
			if (!(unlink($dir . "/" . $_GET['file'])))
			{
				$errors .= "<li>".$gettext->gettext("Could not delete file.  Permissions problem?")."</li>";
			}
		}
		else
		{
			$errors .= "<li>".$gettext->gettext("No real file given")."</li>";
		}
	}
	else
	{
		$errors .= "<li>".$gettext->gettext("You need the 'Modify Files' permission to perform that function.")."</li>";
	}
}
else if (isset($_GET['action']) && $_GET['action'] == "deletedir")
{
	if ($access)
	{
		if (is_dir($dir . "/" . $_GET['file']))
		{
			if (!(@rmdir($dir . "/" . $_GET['file'])))
			{
				$errors .= "<li>".$gettext->gettext("Could not delete directory.  Directory not empty?  Permissions problem?")."</li>";
			}
		}
		else
		{
			$errors .= "<li>".$gettext->gettext("No real directory given")."</li>";
		}
	}
}

include_once("header.php");

$row = "row1";

$dirtext = "";
$filetext = "";

echo "<h3>File Management</h3>";

if ($errors != "")
{
	echo "<ul class=\"error\">$errors</ul>\n";
}

echo '<table cellspacing="0" class="admintable">';

echo "<tr><td width=\"30\">&nbsp;</td><td>".$gettext->gettext("Filename")."</td><td width=\"10%\">".$gettext->gettext("File Size")."</td><td width=\"18\">&nbsp;</td></tr>";

if ($reldir != "")
{
	$newdir = dirname($reldir.'/'.$file);
	if ($newdir == "/")
	{
		$newdir = "";
	}
	else
	{
		$newdir = "?reldir=".$newdir;
	}
	$dirtext .= "<tr class=\"$row\">";
	$dirtext .= "<td width=\"30\">[dir]</td>";
	$dirtext .= '<td><a href="files.php'.$newdir.'">..</a></td>';
	$dirtext .= "<td width=\"10%\">&nbsp;</td>";
	$dirtext .= "<td width=\"18\">&nbsp;</td>";
	$dirtext .= "</tr>";
	$row = "row2";
}

#First do dirs
$ls = dir($dir);
$dirs = array();
while (($file = $ls->read()) != "")
{
	array_push($dirs, $file);
}
sort($dirs);
foreach ($dirs as $file)
{
	if (strpos($file, ".") === false || strpos($file, ".") != 0)
	{
		if (is_dir("$dir/$file"))
		{
			$dirtext .= "<tr class=\"$row\">"; $dirtext .= "<td width=\"30\">[dir]</td>";
			$dirtext .= '<td><a href="files.php?reldir='.$reldir."/".$file.'">'.$file.'</a></td>';
			$dirtext .= "<td width=\"10%\">&nbsp;</td>";
			$dirtext .= "<td width=\"18\" align=\"center\"><a href=\"files.php?action=deletedir&reldir=".$reldir."&file=".$file."\" onclick=\"return confirm('".$gettext->gettext("Are you sure you want to delete this directory?")."');\"><img src=\"../images/delete.png\" alt=\"".$gettext->gettext("Delete")."\" border=\"0\" /></a></td>";
			$dirtext .= "</tr>";
			($row=="row1"?$row="row2":$row="row1");
		}
	}
}
echo $dirtext;

#Now do files
$ls = dir($dir);
$files = array();
while (($file = $ls->read()) != "")
{
	array_push($files, $file);
}
sort($files);
foreach ($files as $file)
{
	if (strpos($file, ".") === false || strpos($file, ".") != 0)
	{
		if (is_file("$dir/$file"))
		{
			$filetext .= "<tr class=\"$row\">";
			$filetext .= "<td width=\"30\">[file]</td>";
			$filetext .= '<td><a href="'.$url.$reldir."/".$file.'" target="_blank">'.$file.'</a></td>';
			$filetext .= "<td width=\"10%\" align=\"right\">".number_format(filesize("$dir/$file"))." Bytes</td>";
			$filetext .= "<td width=\"18\" align=\"center\"><a href=\"files.php?action=deletefile&reldir=".$reldir."&file=".$file."\" onclick=\"return confirm('".$gettext->gettext("Are you sure you want to delete this file?")."');\"><img src=\"../images/delete.png\" alt=\"".$gettext->gettext("Delete")."\" border=\"0\" /></a></td>";
			$filetext .= "</tr>";
			($row=="row1"?$row="row2":$row="row1");
		}
	}
}
echo $filetext;

if ($filetext == "" && $dirtext == "")
{
	echo "<tr class=\"row1\"><td colspan=\"4\" align=\"center\">No Files</td></tr>";
}

echo "</table>";

if ($access)
{
?>

<form enctype="multipart/form-data" action="files.php" method="post">
	<input type="hidden" name="MAX_FILE_SIZE" value="<?=$config["max_upload_size"]?>" />
	<table>
		<tr>
			<td align="right">Upload file here:</td>
			<td><input name="uploadfile" type="file" /><input type="submit" value="Send" /></td>
		</tr>
		<tr>
			<td align="right">Create new dir:</td>
			<td><input type="text" name="newdir" /><input type="submit" name="newdirsubmit" value="Create" /></td>
		</tr>
	</table>
	<input type="hidden" name="reldir" value="<?=$reldir?>" />
</form>

<?
}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
