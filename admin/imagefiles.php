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
require_once("../filemanager/filetypes.inc.php");
require_once("../lib/file.functions.php");
require_once("../include.php");

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



	<SCRIPT TYPE="text/javascript" SRC="../filemanager/ImageManager/assets/dialog.js"></SCRIPT>
	<SCRIPT TYPE="text/javascript" SRC="../filemanager/ImageManager/IMEStandalone.js"></SCRIPT>
<?php echo "	<SCRIPT TYPE=\"text/javascript\" SRC=\"../filemanager/ImageManager/lang/{$nls['htmlarea'][$current_language]}.js\"></SCRIPT>\n" ?>
	<SCRIPT TYPE="text/javascript">
    //<![CDATA[

		//Create a new Imanager Manager, needs the directory where the manager is
		//and which language translation to use.

		var manager = new ImageManager('../filemanager/ImageManager','en');
			
		var thumbdir = "<?php echo $IMConfig['thumbnail_dir']; ?>";
		var base_url = "<?php echo $url; ?>";	
		//Image Manager wrapper. Simply calls the ImageManager


    //]]>
    </SCRIPT>

<SCRIPT TYPE="text/javascript">
/*<![CDATA[*/



/*]]>*/
</SCRIPT>

<?php echo "<h3>".lang('filemanagement')."</h3>"; ?>
<DIV CLASS="ttabArea">
<A HREF="files.php" CLASS="tab"><?php echo lang('filemanager')?></A> 
<A HREF="#" CLASS="tab activeTab"><?php echo lang('imagemanager')?></A> 
</DIV>
<DIV CLASS="tabPane">

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
<IFRAME SRC="../filemanager/ImageManager/images.php?dir=<?php echo "$reldir" ?>" NAME="imgManager" CLASS="imagefilesFrame" TITLE="Image Selection" FRAMEBORDER="0"></IFRAME>

<?php

if ($access)
{
?>


<FORM ENCTYPE="multipart/form-data" ACTION="imagefiles.php" METHOD="post" NAME="uploader">
	<INPUT TYPE="hidden" NAME="MAX_FILE_SIZE" VALUE="<?php echo $config["max_upload_size"]?>">
	<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0" SUMMARY="" CLASS="box">
		<TR>
			<TD ALIGN="right" STYLE="padding-top: 10px;"><?php echo lang('uploadfile')?>:</TD>
			<TD STYLE="padding-top: 10px;"><INPUT NAME="uploadfile" TYPE="file">
			<INPUT TYPE="submit" VALUE="<?php echo lang('send')?>"></TD>
		</TR>
		<TR>
			<TD ALIGN="right"><?php echo lang('createnewfolder')?>:</TD>
			<TD><INPUT TYPE="text" NAME="newdir"><INPUT TYPE="submit" NAME="newdirsubmit" VALUE="<?php echo lang('create')?>"></TD>
		</TR>
	</TABLE>
	<INPUT TYPE="hidden" NAME="reldir" VALUE="<?php echo $reldir?>">
</FORM>
</DIV>
<?php
}

include_once("footer.php");

# vim:ts=4 sw=4 noet
?>
