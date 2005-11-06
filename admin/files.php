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

// in filetypes.inc.php filetypes are defined 
require_once(dirname(dirname(__FILE__))."/lib/filemanager/filetypes.inc.php");
require_once(dirname(dirname(__FILE__))."/lib/file.functions.php");
require_once("../include.php");

check_login();

function deldir($dir)
{
	$handle = opendir($dir);
	while (false!==($FolderOrFile = readdir($handle)))
	{
		if($FolderOrFile != "." && $FolderOrFile != "..") 
		{  
			if(is_dir("$dir/$FolderOrFile")) 
			{
				deldir("$dir/$FolderOrFile");
			}  // recursive
			else
			{
				unlink("$dir/$FolderOrFile");
			}
		}  
	}
	closedir($handle);
	if(rmdir($dir))
	{
		$success = true;
	}
	return $success;  
} 


$errors = "";

$dir = $config["uploads_path"];
$url = $config["uploads_url"];

$reldir = "";
if (isset($_POST['reldir'])) $reldir = $_POST['reldir'];
else if (isset($_GET['reldir'])) $reldir = $_GET['reldir'];

# Check for path errors. It's a bit of a hack.
$reldir = urldecode($reldir);
$reldir = str_replace("..", "", $reldir);
$reldir = str_replace("\\", "/", $reldir);
$reldir = str_replace("//", "/", $reldir);
$reldir = ereg_replace("/^", "", $reldir);

if ($reldir != "")
    {
    $CMS_ADMIN_SUBTITLE = $reldir;
    }

if (strpos($reldir, '..') === false && strpos($reldir, '\\') === false)
{
	$dir .= $reldir;
}

$userid = get_userid();
$access = check_permission($userid, 'Modify Files');

$username = $gCms->variables["username"];

#Did we upload a file?
if (isset($_FILES) && isset($_FILES['uploadfile']) && isset($_FILES['uploadfile']['name']) && $_FILES['uploadfile']['name'] != "")
{
	if ($access)
	{
		if (!move_uploaded_file($_FILES['uploadfile']['tmp_name'], $dir."/".$_FILES['uploadfile']['name']))
		{
			$errors .= "<li>".lang('filenotuploaded')."</li>";
		}
		else
		{
			chmod($dir."/".$_FILES['uploadfile']['name'], octdec('0'.$config['default_upload_permission']));
			audit(-1, $_FILES['uploadfile']['name'], 'Uploaded File');
		}
	}
	else
	{
		$errors .= "<li>".lang('needpermissionto', array('Modify Files'))."</li>";
	}
}

#Did we create a new dir?
if (isset($_POST['newdirsubmit']))
{
	if ($access)
	{
		#Make sure it isn't an empty dir name
		if ($_POST['newdir'] == "")
		{
			$errors .= "<li>".lang('filecreatedirnoname')."</li>";
		}
		else if (ereg('\.\.',$_POST['newdir']))
		{
			$errors .= "<li>".lang('filecreatedirnodoubledot')."</li>";
		}
		else if (ereg('/', $_POST['newdir']) || strpos($_POST['newdir'], '\\') !== false)
		{
			$errors .= "<li>".lang('filecreatedirnoslash')."</li>";
		}
		else if (file_exists($dir."/".$_POST['newdir']))
		{
			$errors .= "<li>".lang('directoryexists')."</li>";
		}
		else
		{
			mkdir($dir."/".$_POST['newdir'], 0777);
			audit(-1, $_POST['newdir'], 'Created Directory');
		}
	}
	else
	{
		$errors .= "<li>".lang('needpermissionto', array('Modify Files'))."</li>";
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
				$errors .= "<li>".lang('errordeletingfile')."</li>";
			}
			else
			{
				audit(-1, $reldir . "/" . $_GET['file'], 'Deleted File');
			}
		}
		else
		{
			$errors .= "<li>".lang('norealfile')."</li>";
		}
	}
	else
	{
		$errors .= "<li>".lang('needpermissionto', array('Modify Files'))."</li>";
	}
}
else if (isset($_GET['action']) && $_GET['action'] == "deletedir")
{
	if ($access)
	{
		if (is_dir($dir . "/" . $_GET['file']))
		{
			if (!(deldir($dir . "/" . $_GET['file'])))
			{
				$errors .= "<li>".lang('errordeletingdirectory')."</li>";
			}
			else
			{
				audit(-1, $reldir . "/" . $_GET['file'], 'Deleted Directory');
			}
		}
		else
		{
			$errors .= "<li>".lang('norealdirectory')."</li>";
		}
	}
	else
	{
		$errors .= "<li>".lang('needpermissionto', array('Modify Files'))."</li>";
	}
}

include_once("header.php");

$row = "row1";

$dirtext = "";
$filetext = "";
$file = "";

if ($errors != "")
{
	echo "<div class=\"pageerrorcontainer\"><ul class=\"error\">".$errors."</ul></div>";
}

echo '<div class="pagecontainer">';
echo '<p class="pageheader">'.lang("filemanagement").'</p>';
echo '<p class="pagesubtitle">'.lang('currentdirectory').': '.($reldir==""?"/":$reldir)."</p>";
echo '<table cellspacing="0" class="pagetable">'."\n";
echo '<thead>';
echo "<tr>\n";
echo '<th class="pagew30">&nbsp;</th>';
echo '<th>'.lang('filename').'</th>';
echo '<th class="pagew10">'.lang('filesize').'</th>';
echo '<th class="pageicon">&nbsp;</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

if ($reldir != '')
{
	$newdir = dirname($reldir.'/'.$file);
	if ($newdir == "/" || $newdir == '\\')
	{
		$newdir = '';
	}
	else
	{
		$newdir = '?reldir='.urlencode($newdir);
	}
	$dirtext .= "<tr class=\"$row\" onmouseover=\"this.className='".$row.'hover'."';\" onmouseout=\"this.className='".$row."';\">";
	$dirtext .= "<td>";
	$dirtext .= $themeObject->DisplayImage('icons/filetypes/folder.gif', lang('directoryabove'));
    $dirtext .= "</td>";
	$dirtext .= '<td><a href="files.php'.$newdir.'">..</a></td>';
	$dirtext .= "<td>&nbsp;</td>";
	$dirtext .= "<td>&nbsp;</td>";
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
			$tmp=urlencode($reldir."/".$file);
			$dirtext .= "<tr class=\"$row\" onmouseover=\"this.className='".$row.'hover'."';\" onmouseout=\"this.className='".$row."';\">"; 
			$dirtext .= "<td>";
            $dirtext .= $themeObject->DisplayImage('icons/filetypes/folder.gif', lang('directoryabove'));
            $dirtext .= "</td>";
			$dirtext .= '<td><a href="files.php?reldir='.$tmp.'">'.$file.'</a></td>';
			$dirtext .= "<td>&nbsp;</td>";
			$dirtext .= "<td class=\"pagepos\"><a href=\"files.php?action=deletedir&amp;reldir=".$reldir."&amp;file=".$file."\" onclick=\"return confirm('".lang('confirmdeletedir')."');\">";
            $dirtext .= $themeObject->DisplayImage('icons/system/delete.gif', lang('delete'),'','','systemicon');
            $dirtext .= "</a></td>";
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
	if (display_file($file)==true){
		if (strpos($file, ".") === false || strpos($file, ".") != 0)
		{
			if (is_file("$dir/$file"))
			{
				$extension = get_file_extention($file);
				// set template vars						
				$template_vars['file']  			= $file;
				$template_vars['dir_file']				= $reldir."/".$file;
				$template_vars['url_dir_file']				= $url.$reldir."/".$file;
	
				// parse little template
				$file_links = parse_template($filetype[$extension]['link']['view'], $template_vars,0);
		//		$file_links = $filetype[$extension]['link']['view'];
				
                $image_icon = $themeObject->DisplayImage("icons/filetypes/".$filetype[$extension]['img'].".gif", $filetype[$extension]['desc']);
                //$image_icon = "<img src=\"../images/cms/icons/filetypes/".$filetype[$extension]['img'].".gif\" alt=\"".$filetype[$extension]['desc']."\" title=\"".$filetype[$extension]['desc']."\" border=\"0\" />";
	
				$filetext .= "<tr class=\"$row\" onmouseover=\"this.className='".$row.'hover'."';\" onmouseout=\"this.className='".$row."';\">";
				$filetext .= "<td>{$image_icon}</td>";
				$filetext .= '<td><a href="'.$file_links.'" rel="external">'.$file.'</a></td>';
				$filesize =  filesize("$dir/$file");
				if ($filesize >(1024*1024)) {$sizestr = number_format($filesize/(1024*1024))." MB";} else {
					if ($filesize >(1024))  {$sizestr = number_format($filesize/1024)." KB";} else {
						$sizestr = number_format($filesize)." B";
					}
				}
				$filetext .= "<td>".$sizestr."</td>";
				$filetext .= "<td><a href=\"files.php?action=deletefile&amp;reldir=".$reldir."&amp;file=".$file."\" onclick=\"return confirm('".lang('deleteconfirm')."');\">";
                $filetext .= $themeObject->DisplayImage('icons/system/delete.gif', lang('delete'),'','','systemicon');
                $filetext .= "</a></td>";
				$filetext .= "</tr>";
				($row=="row1"?$row="row2":$row="row1");
			}
		}
	}
}
echo $filetext;

if ($filetext == "" && $dirtext == "")
{
	echo "<tr class=\"row1\"><td colspan=\"4\" align=\"center\">".lang('nofiles')."</td></tr>";
}

echo '</tbody>';
echo "</table>";

if ($access)
{

?>

<form enctype="multipart/form-data" action="files.php" method="post">
	<div class="pageoverflow">
		<p class="pagetext"><?php echo lang('uploadfile')?>:</p>
		<p class="pageinput">
			<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $config["max_upload_size"]?>" />
			<input type="hidden" name="reldir" value="<?php echo $reldir?>" />
			<input name="uploadfile" type="file" /> <input class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" type="submit" value="<?php echo lang('send')?>" />
		</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext"><?php echo lang('createnewfolder')?>:</p>
		<p class="pageinput"><input type="text" name="newdir" /> <input class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" type="submit" name="newdirsubmit" value="<?php echo lang('create')?>" /></p>
	</div>
</form>

</div>

<?php
}
echo '<p class="pageback"><a class="pageback" href="'.$themeObject->BackUrl().'">&#171; '.lang('back').'</a></p>';
include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
