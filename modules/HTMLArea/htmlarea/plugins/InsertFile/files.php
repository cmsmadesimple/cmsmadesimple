<?php
/***********************************************************************
** Title.........:    Insert File Dialog, File Manager
** Version.......:    1.1
** Authors.......:    Al Rashid <alrashid@klokan.sk>
**                    Xiang Wei ZHUO <wei@zhuo.org>
** URL...........:    http://alrashid.klokan.sk/insFile/
** Filename......:    files.php
** Last changed..:    23 July 2004
***********************************************************************/

require('config.php');
require('functions.php');
//print_r($_REQUEST);
//print_r($_FILES);

$refresh_dirs = false;
$clear_upload = false;
$err = false;


if (isset($_REQUEST['refresh'])) {
	$refresh_dirs = true;
}
if (isset($_REQUEST['path'])) {
	//$path = $_REQUEST['path'];
	$path = checkName($_REQUEST['path']);
	$path = unsanitize($path);
	$path = pathSlashes($path);
} else {
	$path = '/';
}
		
$MY_PATH = $path;
$MY_UP_PATH = substr($MY_PATH,0,strrpos(substr($MY_PATH,0,strlen($MY_PATH)-1),'/'))."/";
//echo "PATH:".$MY_PATH;
//echo "<br>UPP:".$MY_UP_PATH;

if (isset($_REQUEST['action'])) {
	if ('delete' == $_REQUEST['action'])  		$err = deleteFile();
	if ('rename' == $_REQUEST['action'])  		$err = renameFile();
	if ('move' == $_REQUEST['action'])  		$err = moveFile();
	if ('createFolder' == $_REQUEST['action'])  $err = createFolder();
}
if (isset($_FILES['uploadFile']) && is_array($_FILES['uploadFile']))	$err = uploadFile();



function createFolder() {
	global $MY_ALLOW_CREATE, $MY_MESSAGES, $MY_DOCUMENT_ROOT, $refresh_dirs;
	global $MY_PATH;
	if (!$MY_ALLOW_CREATE) return ($MY_MESSAGES['nopermtocreatefolder']);
	if (!(is_dir($MY_DOCUMENT_ROOT.$MY_PATH))) return ($MY_MESSAGES['pathnotfound']);
	if ( !isset($_REQUEST['file'])) return ($MY_MESSAGES['foldernamemissing']);
	$Folder = checkName($_REQUEST['file']);
	//$Folder = utf8RawUrlDecode($Folder);
	$newFolder = $MY_DOCUMENT_ROOT.$MY_PATH.$Folder;
	if (is_dir($newFolder)) return ($MY_MESSAGES['folderalreadyexists']);
	$newFolder = unsanitize($newFolder);
	if (!(@mkdir($newFolder,0755))) return ($MY_MESSAGES['mkdirfailed']);
	chmod($newFolder,0755);
	$refresh_dirs = true;
	return false;
}

function deleteFile() {
	$error = false;
	global $MY_ALLOW_DELETE, $MY_MESSAGES, $MY_DOCUMENT_ROOT, $MY_PATH ;
	if (!$MY_ALLOW_DELETE) return ($MY_MESSAGES['nopermtodelete']);
	if (isset($_REQUEST['folders']) && is_array($_REQUEST['folders'])) {
	    foreach ($_REQUEST['folders'] as $folder) {
			$folder = unsanitize($folder);
			deldir($MY_DOCUMENT_ROOT.$MY_PATH.$folder);
	    }
	}
	if (isset($_REQUEST['files']) && is_array($_REQUEST['files'])) {
	    foreach ($_REQUEST['files'] as $file) {
			$file = unsanitize($file);
			$delFile = $MY_DOCUMENT_ROOT.$MY_PATH.$file;
			if (is_file($delFile)) {
				if (!(unlink($delFile))) $error = $error.'\n'.alertSanitize($MY_MESSAGES['unlinkfailed'].' ('.$delFile.')');
			} else {
				$error = $error.'\n'.alertSanitize($MY_MESSAGES['filenotfound'].' ('.$delFile.')');
			}
	    }
	}
	$refresh_dirs = true;
	return $error;
}

function deldir($dir){
 $current_dir = opendir($dir);
 while (false !== ($entryname = readdir($current_dir))) {
    if(is_dir("$dir/$entryname") and ($entryname != "." and $entryname!="..")){
       deldir("${dir}/${entryname}");
    }elseif($entryname != "." and $entryname!=".."){
       unlink("${dir}/${entryname}");
    }
 }
 closedir($current_dir);
 rmdir($dir);
}

function renameFile() {
	global $MY_ALLOW_RENAME, $MY_MESSAGES, $MY_DOCUMENT_ROOT, $MY_PATH, $refresh_dirs;
	global $MY_ALLOW_EXTENSIONS, $MY_DENY_EXTENSIONS ;
	$error = false;
	if (!$MY_ALLOW_RENAME) return ($MY_MESSAGES['nopermtorename']);
	if (isset($_REQUEST['folders']) && is_array($_REQUEST['folders'])) {
	    foreach ($_REQUEST['folders'] as $file) {
			$oldname = checkName(unsanitize($file['oldname']));
			$newname = checkName(unsanitize($file['newname']));
   			$oldFile = $MY_DOCUMENT_ROOT.$MY_PATH.$oldname;
   			$newFile = $MY_DOCUMENT_ROOT.$MY_PATH.$newname;
			if (is_dir($oldFile)) {
				if (is_dir($newFile)) {
					$error = $error.'\n'.alertSanitize($MY_MESSAGES['folderalreadyexists'].' ('.$oldFile.' -> '.$newFile.')');
				} else {
					if (!rename($oldFile, $newFile)) $error = $error.'\n'.alertSanitize($MY_MESSAGES['renamefailed'].' ('.$oldFile.' -> '.$newFile.')');
				}
			} else {
				$error = $error.'\n'.alertSanitize($MY_MESSAGES['foldernotfound'].' ('.$oldFile.')');
			}
	    }
	 }

	if (isset($_REQUEST['files']) && is_array($_REQUEST['files'])) {
	    foreach ($_REQUEST['files'] as $file) {
			$oldname = checkName(unsanitize($file['oldname']));
			$newname = checkName(unsanitize($file['newname']));
			$parts = explode('.', $newname);
			$ext = strtolower($parts[count($parts)-1]);
			if (is_array($MY_DENY_EXTENSIONS )) {
				if (in_array($ext, $MY_DENY_EXTENSIONS)) $error = $error.'\n'.$MY_MESSAGES['extnotallowed'];
			}
			if (is_array($MY_ALLOW_EXTENSIONS )) {
				if (!in_array($ext, $MY_ALLOW_EXTENSIONS)) $error = $error.'\n'.$MY_MESSAGES['extnotallowed'];
			}
   			$oldFile = $MY_DOCUMENT_ROOT.$MY_PATH.$oldname;
   			$newFile = $MY_DOCUMENT_ROOT.$MY_PATH.$newname;
			if (is_file($oldFile)) {
				if (is_file($newFile)) {
					$error = $error.'\n'.alertSanitize($MY_MESSAGES['filealreadyexists'].' ('.$oldFile.' -> '.$newFile.')');
				} else {
					if (!rename($oldFile, $newFile)) $error = $error.'\n'.alertSanitize($MY_MESSAGES['renamefailed'].' ('.$oldFile.' -> '.$newFile.')');
				}
    		} else {
				$error = $error.'\n'.alertSanitize($MY_MESSAGES['filenotfound'].' ('.$oldFile.')');
			}
	    }
	 }

	$refresh_dirs = true;
	return $error;
}

function moveFile() {
	global $MY_ALLOW_MOVE, $MY_MESSAGES, $MY_DOCUMENT_ROOT, $MY_PATH, $refresh_dirs;
	global $MY_ALLOW_EXTENSIONS, $MY_DENY_EXTENSIONS ;
	$error = false;
	if (!$MY_ALLOW_MOVE) return ($MY_MESSAGES['nopermtomove']);
	$newPath = pathSlashes(checkName($_REQUEST['newpath']));
 	if (!(is_dir($MY_DOCUMENT_ROOT.$newPath))) return ($MY_MESSAGES['pathnotfound']);
	if (isset($_REQUEST['folders']) && is_array($_REQUEST['folders'])) {
	    foreach ($_REQUEST['folders'] as $file) {
			$name = checkName(unsanitize($file));
      		$oldFile = $MY_DOCUMENT_ROOT.$MY_PATH.$name;
      		$newFile = $MY_DOCUMENT_ROOT.$newPath.$name;
			if (is_dir($oldFile)) {
			    if (is_dir($newFile)) {
					$error = $error.'\n'.alertSanitize($MY_MESSAGES['folderalreadyexists'].' ('.$oldFile.' -> '.$newFile.')');
				} else {
					if (!rename($oldFile, $newFile)) $error = $error.'\n'.alertSanitize($MY_MESSAGES['renamefailed'].' ('.$oldFile.' -> '.$newFile.')');
				}
			} else {
				$error = $error.'\n'.alertSanitize($MY_MESSAGES['foldernotfound'].' ('.$oldFile.')');
			}
	    }
	 }
	if (isset($_REQUEST['files']) && is_array($_REQUEST['files'])) {
	    foreach ($_REQUEST['files'] as $file) {
			$name = checkName(unsanitize($file));
   			$oldFile = $MY_DOCUMENT_ROOT.$MY_PATH.$name;
   			$newFile = $MY_DOCUMENT_ROOT.$newPath.$name;
			if (is_file($oldFile)) {
			    if (is_file($newFile)) {
					$error = $error.'\n'.alertSanitize($MY_MESSAGES['filealreadyexists'].' ('.$oldFile.' -> '.$newFile.')');
				} else {
					if (!rename($oldFile, $newFile)) $error = $error.'\n'.alertSanitize($MY_MESSAGES['renamefailed'].' ('.$oldFile.' -> '.$newFile.')');
				}
			} else {
				$error = $error.'\n'.alertSanitize($MY_MESSAGES['filenotfound'].' ('.$oldFile.')');
			}
	    }
	 }
	$refresh_dirs = true;
	return $error;
}

function uploadFile() {
	global $MY_ALLOW_UPLOAD, $MY_MESSAGES, $MY_DOCUMENT_ROOT, $MY_PATH, $clear_upload;
	global $MY_ALLOW_EXTENSIONS, $MY_DENY_EXTENSIONS, $MY_MAX_FILE_SIZE ; 
	if (!$MY_ALLOW_UPLOAD) return ($MY_MESSAGES['nopermtoupload']);
	if (!(is_dir($MY_DOCUMENT_ROOT.$MY_PATH))) return ($MY_MESSAGES['pathnotfound']);
	$filename = checkName($_FILES['uploadFile']['name']);
	$newFile = $MY_DOCUMENT_ROOT.$MY_PATH.$filename;
	$parts = explode('.', $filename);
	$ext = strtolower($parts[count($parts)-1]);
	if (is_file($newFile))  return ($MY_MESSAGES['uploadfilealreadyexists']);
	if (is_array($MY_DENY_EXTENSIONS )) {
		if (in_array($ext, $MY_DENY_EXTENSIONS)) return ($MY_MESSAGES['extnotallowed']);
	}
	if (is_array($MY_ALLOW_EXTENSIONS )) {
		if (!in_array($ext, $MY_ALLOW_EXTENSIONS)) return ($MY_MESSAGES['extnotallowed']);
	}
	if ($MY_MAX_FILE_SIZE) {
		if ($_FILES['uploadFile']['size'] > $MY_MAX_FILE_SIZE) return ($MY_MESSAGES['filesizeexceedlimit'].' of '.($MY_MAX_FILE_SIZE/1024).'kB.');
	}
	if (!is_file($_FILES['uploadFile']['tmp_name']))  return ($MY_MESSAGES['filenotuploaded']);
	move_uploaded_file($_FILES['uploadFile']['tmp_name'], $newFile);
	chmod($newFile, 0666);
	$clear_upload = true;
	return false;
}


function parse_size($size) {
	if($size < 1024)
		return $size.' bytes';
	else if($size >= 1024 && $size < 1024*1024) {
		return sprintf('%01.2f',$size/1024.0).' KB';
	} else {
		return sprintf('%01.2f',$size/(1024.0*1024)).' MB';
	}
}

function parse_time($timestamp) {
	global $MY_DATETIME_FORMAT;
	return date($MY_DATETIME_FORMAT, $timestamp);
}


function draw_no_results() {
	global $MY_MESSAGES;
	echo '<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0" style="background-color:Window;"> <tr>
	    <td><div align="center" style="font-size:large;font-weight:bold;color:#CCCCCC;font-family: Helvetica, sans-serif;">';
	echo $MY_MESSAGES['nofiles'];
	echo '</div></td></tr></table>';
}

function draw_no_dir() {
	global $MY_MESSAGES;
	global $MY_DOCUMENT_ROOT;
	echo '<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0" style="background-color:Window;"><tr>
   	 <td><div align="center" style="font-size:small;font-weight:bold;color:#CC0000;font-family: Helvetica, sans-serif;">';
	echo $MY_MESSAGES['configproblem']." ".$MY_DOCUMENT_ROOT;
	echo '</div></td></tr></table>';
}

?>
<html>
	<head>
		<title>File Browser</title>
		<?php
			echo '<meta http-equiv="content-language" content="'.$MY_LANG.'" />'."\n";
			echo '<meta http-equiv="Content-Type" content="text/html; charset='.$MY_CHARSET.'" />'."\n";
			echo '<meta name="author" content="AlRashid, www: http://alrashid.klokan.sk; mailto:alrashid@klokan.sk" />'."\n";
		?>

		<style type="text/css">
			<!--
			body {
				font-family:	Verdana, Helvetica, Arial, Sans-Serif;
				font:	message-box;
				background:	ThreedFace;
			}
			code {
				font-size:	1em;
			}

			a {
				color: black;
			}

			a:visited {
				color: black;
			}
			.selected a {
	background:	Highlight;
	color:		HighlightText;
			}
			.selected a:visited {
	background:	Highlight;
	color:		HighlightText;
			}


.selected {
	background:	Highlight;
	color:		HighlightText;
}


/*
table {
	border-collapse:	collapse;
	border:				1px solid ThreeDShadow;
	border:				1px solid;
	border-color:		ThreeDShadow ThreeDHighLight
						ThreeDHighLight ThreeDShadow;

	background:			Window;
}
*/
td {
	font:				icon;
	padding:			2px 5px;
	cursor:				default;
	-moz-user-select:	none;
}
			-->
		</style>
	<link type="text/css" rel="StyleSheet" href="css/sortabletable.css" />
	<script type="text/javascript" src="js/sortabletable.js"></script>
	<script type="text/javascript" src="js/selectableelements.js"></script>
	<script type="text/javascript" src="js/selectabletablerows.js"></script>


	<script language="JavaScript" type="text/JavaScript">
/*<![CDATA[*/

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_showHideLayers() { //v6.0
  var i,p,v,obj,args=MM_showHideLayers.arguments;
  for (i=0; i<(args.length-2); i+=3) if ((obj=MM_findObj(args[i],window.top.document))!=null) { v=args[i+2];
    if (obj.style) { obj=obj.style; v=(v=='show')?'visible':(v=='hide')?'hidden':v; }
    obj.visibility=v; }
}

function changeLoadingStatus(state) {
	var statusText = null;
	if(state == 'load') {
		statusText = '<?php echo $MY_MESSAGES['loading']; ?>';
	}
	else if(state == 'upload') {
		statusText = '<?php echo $MY_MESSAGES['uploading']; ?>';
	}
	if(statusText != null) {
		var obj = MM_findObj('loadingStatus', window.top.document);
		if (obj != null && obj.innerHTML != null)
			obj.innerHTML = statusText;
		MM_showHideLayers('loading','','show')
	}
}

function changeDir(nb) {
	changeLoadingStatus('load');
	var postForm2 = document.getElementById('form2');
	postForm2.elements["action"].value="changeDir";
	postForm2.elements["path"].value=postForm2.elements["path"].value+folderJSArray[nb][1];
	postForm2.submit();
}

function setSortBy(column, noclick) {
	switch (column) {
	    case 0: st2.sort(4);
	            if (noclick) st.sort(0);
			 	break;
	  	case 2: st2.sort(5);
	  	        if (noclick) st.sort(2);
				break;
		case 3: st1.sort(6);
		        st2.sort(6);
		        if (noclick) st.sort(3);
		        break;
		default: st1.sort(1);
		         st2.sort(1);
		         if (noclick) st.sort(1);
	}
	var topDoc = window.top.document.forms[0];
	topDoc.sortby.value = column;
}

function getSortBy() {
	var topDoc = window.top.document.forms[0];
	return (topDoc.sortby.value);
}

function fileSelected(filename, caption, icon, size, date) {
	var topDoc = window.top.document.forms[0];
	topDoc.f_url.value = filename;
	topDoc.f_caption.value = caption;
	topDoc.f_icon.value = icon;
	topDoc.f_size.value = size;
	topDoc.f_date.value = date;
}

function updateDir() {
	var newPath = "<?php echo $MY_PATH; ?>";
	if(window.top.document.forms[0] != null) {
		var allPaths = window.top.document.forms[0].path.options;
		for(i=0; i<allPaths.length; i++)  {
			allPaths.item(i).selected = false;
			if((allPaths.item(i).value)==newPath) {
				allPaths.item(i).selected = true;
			}
		}
	}
}

<?php
 if($clear_upload) {
	echo '
		var topDoc = window.top.document.forms[0];
		topDoc.uploadFile.value = null;
		';
}
if ($refresh_dirs) { ?>
function refreshDirs() {
	var allPaths = window.top.document.forms[0].path.options;
	var fields = ['/' <?php dirs($MY_DOCUMENT_ROOT,'');?>];
	var newPath = '<?php echo sanitize($MY_PATH); ?>';
 	for(i = allPaths.length; i > 0; i--) {
			allPaths[i-1]=null;
		}

	for(i=0; i<fields.length; i++) {
		var newElem =	document.createElement("OPTION");
		var newValue = fields[i];
		newElem.text = newValue;
		newElem.value = newValue;
		if(newValue == newPath)
			newElem.selected = true;
		else
			newElem.selected = false;
		allPaths.add(newElem);
	}
}
refreshDirs();
<?php
}
if ($err) {
	echo 'alert(\''.$err.'\');';
}
 ?>


	/*]]>*/
</script>
</head>
<body onload="updateDir();">
<form action="files.php?dialogname=<?php echo $MY_NAME; ?>&refresh=1" id="form2" name="form2" method="post" enctype="multipart/form-data">
 <input type="hidden" name="action" id="action" value="" />
 <input type="hidden" name="path" id="path" value="<?php echo $MY_PATH; ?>" />
 <input type="hidden" name="uppath" id="uppath" value="<?php echo $MY_UP_PATH; ?>" />
 <input type="hidden" name="newpath" id="newpath" value="" />
 <input type="hidden" name="file" id="file" value="" />
</form>

<?php
$d = @dir($MY_DOCUMENT_ROOT.$MY_PATH);
if($d) {
	$t_header = '<table class="sort-table" id="tableHeader" cellspacing="0" width="100%"  border="0" >
	<col />
	<col />
	<col style="text-align: right" />
	<col />
	<thead>
		<tr>
			<td width="4%" id="sortmefirst" onclick="setSortBy(0, false);">'.$MY_MESSAGES['type'].'</td>
			<td width="50%" title="CaseInsensitiveString" onclick="setSortBy(1, false);">'.$MY_MESSAGES['name'].'</td>
			<td width="13%" onclick="setSortBy(2, false);">'.$MY_MESSAGES['size'].'</td>
			<td width="25%" onclick="setSortBy(3, false);">'.$MY_MESSAGES['datemodified'].'</td>
		</tr>
	</thead>
	<tbody style="display: none;"  >
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
	</tbody>
</table>
';
$t_folders = '<table class="sort-table" id="tableFolders" onselectstart="return false" cellspacing="0" width="100%" border="0" >
	<col />
	<col />
	<col style="text-align: right" />
	<col />
	<col />
	<col />
	<col />
	<thead style="display: none;">
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
	</thead>
	<tbody>
';
	$t_files='<table class="sort-table" id="tableFiles" onselectstart="return false" cellspacing="0" width="100%" border="0" >
	<col />
	<col />
	<col style="text-align: right" />
	<col />
	<col />
	<col />
	<col />
	<thead style="display: none;">
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
	</thead>
	<tbody>';

	
	$entries_cnt = 0;
	$fileNb=0;
	$folderNb=0;
	$fileJSArray='var fileJSArray = [';
	$folderJSArray='var folderJSArray = [';
	while (false !== ($entry = $d->read())) {
		if(substr($entry,0,1) != '.') {
			$relativePath = $MY_PATH.$entry;
			$absolutePath = $MY_DOCUMENT_ROOT.$relativePath;
			if (is_dir($absolutePath)) {
				$entries_cnt++;
				$time = filemtime($absolutePath);
				$parsed_time = parse_time($time);
				$t_folders .= '<tr id="D'.$folderNb.'">
				<td width="4%"><img src="img/ext/folder_small.gif" width="16" height="16" border="0" alt="'.$entry.'" /></td>
				<td width="50%"><div style="height:15px; overflow:hidden;"><a href="javascript:changeDir('.$folderNb.');" title="'.$entry.'">'.$entry.'</a></div></td>
				<td width="18%" align="right">'.$MY_MESSAGES['folder'].'</td>
				<td width="25%">'.$parsed_time.'</td>
				<td width="0px" style="display: none;">&nbsp;</td>
				<td width="0px" style="display: none;">&nbsp;</td>
    			<td width="0px" style="display: none;">'.$time.'</td>
				</tr>';
				$folderJSArray .= "['images/ext/folder_small.gif', '".sanitize($entry)."', '".$MY_MESSAGES['folder']."', '".$parsed_time."'],\n";
                $folderNb++;
			} else {
				$entries_cnt++;
				$ext = substr(strrchr($entry, '.'), 1);
				if (is_array($MY_LIST_EXTENSIONS)) {
						if (!in_array(strtolower($ext), $MY_LIST_EXTENSIONS)) continue;
				}
				$size = filesize($absolutePath);
				$time = filemtime($absolutePath);
				$parsed_size = parse_size($size);
				$parsed_time = parse_time($time);
				$parsed_icon = 'img/ext/'.parse_icon($ext);
				$t_files .= '<tr id="F'.$fileNb++.'">
				<td width="4%"><img src="'.$parsed_icon.'" width="16" height="16" border="0" alt="'.$entry.'" /></td>
				<td width="50%"><div style="height:15px; overflow:hidden;">'.$entry.'</div></td>
				<td width="18%" align="right">'.$parsed_size.'</td>
				<td width="25%">'.$parsed_time.'</td>
				<td width="0px" style="display: none;">'.$ext.'</td>
				<td width="0px" style="display: none;">'.$size.'</td>
				<td width="0px" style="display: none;">'.$time.'</td>
				</tr>';
				$fileJSArray .= "['".$parsed_icon."', '".sanitize($entry)."', '".$parsed_size."', '".$parsed_time."'],\n";
			}
		}
	}
	$d->close();
	$folderJSArray .= "['', '', '', '']];\n";
	$fileJSArray .= "['', '', '', '']];\n";
	

	$t_folders .= '</tbody> </table>';
	$t_files .= '</tbody> </table>';


	if ($entries_cnt) {
		echo $t_header."\n<div style=\"height:90%; overflow: auto; overflow-y: scroll; background-color:window;\">".$t_folders."\n".$t_files."</div>"."\n";
?>

<script type="text/javascript">
	/*<![CDATA[*/
	var st = new SortableTable(document.getElementById("tableHeader"), ["CaseInsensitiveString", "CaseInsensitiveString", "Number", "Number"]);
	var st1 = new SortableTable(document.getElementById("tableFolders"), ["None", "CaseInsensitiveString", "None", "None", "CaseInsensitiveString", "Number", "Number"]);
	var st2 = new SortableTable(document.getElementById("tableFiles"), ["None", "CaseInsensitiveString", "None", "None", "CaseInsensitiveString", "Number", "Number"]);
	var sta = new SelectableTableRows(document.getElementById("tableFolders"), true);
	var stb = new SelectableTableRows(document.getElementById("tableFiles"), true);

	var colmn = parseInt(getSortBy());
	setSortBy(colmn, true);
	<?php
		echo $folderJSArray;
		echo $fileJSArray;
 	?>
	/*]]>*/
</script>
<?php
	} else {
	draw_no_results();
	}
}
else
{
 draw_no_dir();
}

?>

<script language="JavaScript" type="text/JavaScript">
/*<![CDATA[*/
	MM_showHideLayers('loading','','hide')
/*]]>*/
</script>
</body>
</html>

