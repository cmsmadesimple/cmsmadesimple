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

$errors = array();

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

$gCms = cmsms();
$username = $gCms->variables["username"];

#Did we upload a file?
if (isset($_FILES) && isset($_FILES['uploadfile']) && isset($_FILES['uploadfile']['name']) && $_FILES['uploadfile']['name'] != "")
{
	if ($access)
	{
		if (!move_uploaded_file($_FILES['uploadfile']['tmp_name'], $dir."/".$_FILES['uploadfile']['name']))
		{
		  $errors[] = lang('filenotuploaded');
		}
		else
		{
			chmod($dir."/".$_FILES['uploadfile']['name'], octdec('0'.$config['default_upload_permission']));
			audit(-1, $_FILES['uploadfile']['name'], lang_en('uploaded_file'));
		}
	}
	else
	{
	  $errors[] = lang('needpermissions',array('Modify Files'));
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
		  $errors[] = lang('filecreatedirnoname');
		}
		else if (preg_match('@\.\.@',$_POST['newdir']))
		{
		  $errors[] = lang('filecreatedirnodoubledot');
		}
		else if (preg_match('@/@', $_POST['newdir']) || strpos($_POST['newdir'], '\\') !== false)
		{
		  $errors[] = lang('filecreatedirnoslash');
		}
		else if (preg_match('/[^0-9a-zA-Z\._\-]/i',$_POST['newdir']))
		  {
		    $errors[] = lang('filecreatedirbadchars');
		  }
		else if (file_exists($dir."/".$_POST['newdir']))
		{
		  $errors[] = lang('directoryexists');
		}
		else
		{
			mkdir($dir."/".$_POST['newdir'], 0777);
			// put mention into the admin log
			audit(-1, lang('imagemanager'), "Created new directory");
		}
	}
	else
	{
	  $errors[] = lang('needpermissionto', array('Modify Files'));
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
			  $errors[] = lang('errordeletingfile');
			}
			else
			{
				audit(-1, $reldir . "/" . $_GET['file'], lang_en('deleted_file'));
			}
		}
		else
		{
		  $errors[] = lang('norealfile');
		}
	}
	else
	{
	  $errors[] = lang('needpermissionto', array('Modify Files'));
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
			  $errors[] = lang('errordeletingdirectory');
			}
			else
			{
				audit(-1, $reldir . "/" . $_GET['file'], lang_en('deleted_directory'));
			}
		}
		else
		{
		  $errors[] = lang('norealdirectory');
		}
	}
	else
	{
	  $errors[] = lang('needpermissionto', array('Modify Files'));
	}
}

include_once("header.php");
$current_language = cms_admin_current_language();
$gCms = cmsms();
$nls =& $gCms->nls;
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

if (count($errors) )
{
  echo $themeObject->ShowErrors($errors);
}

echo '<div class="pagecontainer">';
echo $themeObject->ShowHeader('imagemanagement');

?>
<iframe class="imageframe" src="../lib/filemanager/ImageManager/images.php<?php echo $urlext ?>&amp;dir=<?php echo "$reldir" ?>" name="imgManager" title="Image Selection"></iframe>

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
			<input name="uploadfile" type="file" /> <input class="pagebutton" type="submit" value="<?php echo lang('send')?>" />
		</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext"><?php echo lang('createnewfolder')?>:</p>
		<p class="pageinput"><input type="text" name="newdir" /> <input class="pagebutton" type="submit" name="newdirsubmit" value="<?php echo lang('create')?>" /></p>
	</div>
</form>

</div>

<?php
}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
