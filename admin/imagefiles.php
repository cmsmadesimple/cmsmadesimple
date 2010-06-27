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

$urlext = get_secure_param();
check_login();

$action_done='';

function deldir($dir)
{
	$handle = opendir($dir);
	while (false!==($FolderOrFile = readdir($handle)))
	{
		if($FolderOrFile != "." && $FolderOrFile != "..") 
		{  
			if(@is_dir("$dir/$FolderOrFile")) 
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
		if (@is_dir($dir . "/" . $_GET['file']))
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
$current_language = cms_admin_current_language();
?>



	<script type="text/javascript" src="../lib/filemanager/ImageManager/assets/dialog.js"></script>
	<script type="text/javascript" src="../lib/filemanager/ImageManager/IMEStandalone.js"></script>
<?php echo "	<script type=\"text/javascript\" src=\"../lib/filemanager/ImageManager/lang/{$nls['htmlarea'][$current_language]}.js\"></script>\n" ?>
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

<?php


$row = "row1";

$dirtext = "";
$filetext = "";
$file = "";

if ($errors != "")
{
	// echo "<div class=\"pageerrorcontainer\"><ul class=\"error\">".$errors."</ul></div>";
	echo $themeObject->ShowErrors('<ul class="error">'.$errors.'</ul>');
}

echo '<div class="pagecontainer">';
echo $themeObject->ShowHeader('imagemanagement');

?>
<iframe class="imageframe" src="../lib/filemanager/ImageManager/images.php<?php echo $urlext ?>&dir=<?php echo "$reldir" ?>" name="imgManager" title="Image Selection"></iframe>

<?php

if ($access)
{
?>

<form enctype="multipart/form-data" action="imagefiles.php<?php echo $urlext ?>" method="post" name="uploader">
        <div>
          <input type="hidden" name="<?php echo CMS_SECURE_PARAM_NAME ?>" value="<?php echo $_SESSION[CMS_USER_KEY] ?>" />
        </div>
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

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
