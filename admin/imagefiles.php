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

// in filetypes.inc.php filetypes are defined 
require_once(dirname(dirname(__FILE__))."/lib/filemanager/filetypes.inc.php");
require_once(dirname(dirname(__FILE__))."/lib/file.functions.php");
require_once(dirname(dirname(__FILE__))."/include.php");

$action_done='';

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



check_login();

$errors = "";

$dir = $config["image_uploads_path"];
$url = $config["image_uploads_url"];

$reldir = "";

if (!isset($IMConfig['thumbnail_dir'])) $IMConfig['thumbnail_dir'] = '';
if (isset($_POST['reldir'])) $reldir = $_POST['reldir'];
else if (isset($_GET['reldir'])) $reldir = $_GET['reldir'];

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
			mkdir($dir."/".$_POST['newdir']);
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
?>



	<script type="text/javascript" src="../filemanager/ImageManager/assets/dialog.js"></script>
	<script type="text/javascript" src="../filemanager/ImageManager/IMEStandalone.js"></script>
<?php echo "	<script type=\"text/javascript\" src=\"../filemanager/ImageManager/lang/{$nls['htmlarea'][$current_language]}.js\"></script>\n" ?>
	<script type="text/javascript">
    //<![CDATA[

		//Create a new Imanager Manager, needs the directory where the manager is
		//and which language translation to use.

		var manager = new ImageManager('../lib/filemanager/ImageManager','en');
			
		var thumbdir = "<?php echo $IMConfig['thumbnail_dir']; ?>";
		var base_url = "<?php echo $url; ?>";	
		//Image Manager wrapper. Simply calls the ImageManager


    //]]>
    </script>

<script type="text/javascript">
/*<![CDATA[*/



/*]]>*/
</script>

<?php echo "<h3>".lang('filemanagement')."</h3>"; ?>
<div class="ttabArea">
<a href="files.php" class="tab"><?php echo lang('filemanager')?></a> 
<a href="#" class="tab activeTab"><?php echo lang('imagemanager')?></a> 
</div>
<div class="tabPane">

<?php


$row = "row1";

$dirtext = "";
$filetext = "";




if ($errors != "")
{
	echo "<ul class=\"error\">$errors</ul>\n";
}

//echo "<h4>".lang('currentdirectory').": ".($reldir==""?"/":$reldir)."</h4>";
//echo '<table cellspacing="0" class="admintable">';
//echo "<tr><td width=\"30\">&nbsp;</td><td>".lang('filename')."</td><td width=\"10%\">".lang('filesize')."</td><td width=\"18\">&nbsp;</td></tr>";
?>
<iframe src="../lib/filemanager/ImageManager/images.php?dir=<?php echo "$reldir" ?>" name="imgManager" class="imagefilesFrame" title="Image Selection" frameborder="0"></iframe>

<?php

if ($access)
{
?>


<form enctype="multipart/form-data" action="imagefiles.php" method="post" name="uploader">
	<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $config["max_upload_size"]?>" />
	<table border="0" cellpadding="0" cellspacing="0" summary="" class="box">
		<tr>
			<td align="right" style="padding-top: 10px;"><?php echo lang('uploadfile')?>:</td>
			<td style="padding-top: 10px;"><input name="uploadfile" type="file" />
			<input type="submit" value="<?php echo lang('send')?>" /></td>
		</tr>
		<tr>
			<td align="right"><?php echo lang('createnewfolder')?>:</td>
			<td><input type="text" name="newdir" /><input type="submit" name="newdirsubmit" value="<?php echo lang('create')?>" /></td>
		</tr>
	</table>
	<input type="hidden" name="reldir" value="<?php echo $reldir?>" />
</form>
</div>
<?php
}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
