<?php
/***********************************************************************
** Title.........:    Insert File Dialog, File Manager
** Version.......:    1.1
** Authors.......:    Al Rashid <alrashid@klokan.sk>
**                    Xiang Wei ZHUO <wei@zhuo.org>
** Filename......:    insert_file.php
** URL...........:    http://alrashid.klokan.sk/insFile/
** Last changed..:    23 July 2004
***********************************************************************/

require('config.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
	<title>Insert File</title>
	<?php
		echo '<meta http-equiv="content-language" content="'.$MY_LANG.'" />'."\n";
		echo '<meta http-equiv="Content-Type" content="text/html; charset='.$MY_CHARSET.'" />'."\n";
		echo '<meta name="author" content="AlRashid, www: http://alrashid.klokan.sk; mailto:alrashid@klokan.sk" />'."\n";
//	<script type="text/javascript" src="../../popups/popup.js"></script>
//	<script type="text/javascript" src="../../dialog.js"></script>
	?>
	<script type="text/javascript" src="js/popup.js"></script>
	<script type="text/javascript" src="js/dialog.js"></script>
	<style type="text/css">
		html, body {
		  background: ButtonFace;
		  color: ButtonText;
		  font: 11px Tahoma,Verdana,sans-serif;
		  margin: 0px;
		  padding: 0px;
		}
		body { padding: 5px; }
		table {
		  font: 11px Tahoma,Verdana,sans-serif;
		}
		form p {
		  margin-top: 5px;
		  margin-bottom: 5px;
		}
		fieldset { padding: 0px 10px 5px 5px; }
		select, input, button { font: 11px Tahoma,Verdana,sans-serif; }
		button { width: 70px; }

		.title { background: #ddf; color: #000; font-weight: bold; font-size: 120%; padding: 3px 10px; margin-bottom: 10px;
		border-bottom: 1px solid black; letter-spacing: 2px;
		}
		form { padding: 0px; margin: 0px; }
		a { padding: 2px; border: 1px solid ButtonFace;	}
		a img	{ border: 0px; vertical-align:bottom; }
		a:hover { border-color: ButtonHighlight ButtonShadow ButtonShadow ButtonHighlight; }
	</style>

	<script language="JavaScript" type="text/JavaScript">
	/*<![CDATA[*/
		var preview_window = null;
		var resize_iframe_constant = 150;
		<?php
		if (is_array($MY_DENY_EXTENSIONS)) {
			echo 'var DenyExtensions = [';
			foreach($MY_DENY_EXTENSIONS as $value) echo '"'.$value.'", ';
			echo '""];
			';
		}
		if (is_array($MY_ALLOW_EXTENSIONS)) {
			echo 'var AllowExtensions = [';
			foreach($MY_ALLOW_EXTENSIONS as $value) echo '"'.$value.'", ';
			echo '""];
			';
		}
		?>

		function Init() {
			window.onresize=resize_iframe;
			window.resizeTo(750, 470);
			__dlg_init();
			window.resizeTo(750, 470);
   			resize_iframe;
		}

		function onOK() {
			var myPath = fileManager.document.getElementById('form2').elements["path"].value;
			var folderItems = fileManager.sta.getSelectedItems();
			var folderItemsLength = folderItems.length;
			var fileItems = fileManager.stb.getSelectedItems();
			var fileItemsLength = fileItems.length;
			var returnFolders = new Array();
			var returnFiles = new Array();

			for (var i=0; i<folderItemsLength; i++) {
					var strId = folderItems[i].getAttribute("id").toString();
					var trId = parseInt(strId.substring(1, strId.length));
					returnFolders[i] = new Array();
					returnFolders[i][0] = fileManager.folderJSArray[trId][0];
					returnFolders[i][1] = fileManager.folderJSArray[trId][1];
					returnFolders[i][2] = fileManager.folderJSArray[trId][2];
					returnFolders[i][3] = fileManager.folderJSArray[trId][3];
			}
			for (var i=0; i<fileItemsLength; i++) {
					var strId = fileItems[i].getAttribute("id").toString();
					var trId = parseInt(strId.substring(1, strId.length));
					returnFiles[i] = new Array();
					returnFiles[i][0] = fileManager.fileJSArray[trId][0];
					returnFiles[i][1] = fileManager.fileJSArray[trId][1];
					returnFiles[i][2] = fileManager.fileJSArray[trId][2];
					returnFiles[i][3] = fileManager.fileJSArray[trId][3];
			}
			var param = new Object();
			param["format"] = '<?php echo $MY_LINK_FORMAT; ?>';
			param["folders"] = returnFolders;
			param["files"] = returnFiles;
			param["path"] = myPath;

			if (preview_window) {
		  		preview_window.close();
		  	}
			__dlg_close(param);
			return false;
		}

		function onCancel() {
		  if (preview_window) {
		    preview_window.close();
		  }
		  __dlg_close(null);
		  return false;
		}
		
		function changeDir(selection) {
			changeLoadingStatus('load');
			var newDir = selection.options[selection.selectedIndex].value;
			var postForm2 = fileManager.document.getElementById('form2');
			postForm2.elements["action"].value="changeDir";
			postForm2.elements["path"].value=newDir;
			postForm2.submit();
		}

		function goUpDir() {
			var selection = document.forms[0].path;
			var dir = selection.options[selection.selectedIndex].value;
			if(dir != '/'){
			    changeLoadingStatus('load');
				var postForm2 = fileManager.document.getElementById('form2');
				postForm2.elements["action"].value="changeDir";
				postForm2.elements["path"].value=postForm2.elements["uppath"].value;
				postForm2.submit();
			}
		}

		function newFolder() {
			var selection = document.forms[0].path;
			var path = selection.options[selection.selectedIndex].value;
			var folder = prompt('<?php echo $MY_MESSAGES['newfolder']; ?>','');
			if (folder) {
			    changeLoadingStatus('load');
				var postForm2 = fileManager.document.getElementById('form2');
				postForm2.elements["action"].value="createFolder";
				postForm2.elements["file"].value=folder;
				postForm2.submit();
			}
			return false
		}

		function deleteFile() {
			var folderItems = fileManager.sta.getSelectedItems();
			var folderItemsLength = folderItems.length;
			var fileItems = fileManager.stb.getSelectedItems();
			var fileItemsLength = fileItems.length;
			var message = "<?php echo $MY_MESSAGES['delete']; ?>";
            if ((folderItemsLength == 0) && (fileItemsLength == 0)) return false;
			if (folderItemsLength > 0) {
				message = message + " " + folderItemsLength + " " + "<?php echo $MY_MESSAGES['folders']; ?>";
			}
			if (fileItemsLength > 0) {
				message = message + " " + fileItemsLength + " " + "<?php echo $MY_MESSAGES['files']; ?>";
			}
			if (confirm(message+" ?")) {
				var postForm2 = fileManager.document.getElementById('form2');
				for (var i=0; i<folderItemsLength; i++) {
					var strId = folderItems[i].getAttribute("id").toString();
					var trId = parseInt(strId.substring(1, strId.length));
			   		var i_field = fileManager.document.createElement('INPUT');
					i_field.type = 'hidden';
					i_field.name = 'folders[' + i.toString() + ']';
			  		i_field.value = fileManager.folderJSArray[trId][1];
					postForm2.appendChild(i_field);
				}
				for (var i=0; i<fileItemsLength; i++) {
					var strId = fileItems[i].getAttribute("id").toString();
					var trId = parseInt(strId.substring(1, strId.length));
			   		var i_field = fileManager.document.createElement('INPUT');
					i_field.type = 'hidden';
					i_field.name = 'files[' + i.toString() + ']';
			  		i_field.value = fileManager.fileJSArray[trId][1];
					postForm2.appendChild(i_field);
				}
				changeLoadingStatus('load');
				postForm2.elements["action"].value="delete";
				postForm2.submit();
			}
		}

		function renameFile() {
			var folderItems = fileManager.sta.getSelectedItems();
			var folderItemsLength = folderItems.length;
			var fileItems = fileManager.stb.getSelectedItems();
			var fileItemsLength = fileItems.length;
			var postForm2 = fileManager.document.getElementById('form2');
			if ((folderItemsLength == 0) && (fileItemsLength == 0)) return false;
			if (!confirm('<?php echo $MY_MESSAGES['renamewarning']; ?>')) return false;
			for (var i=0; i<folderItemsLength; i++) {
				var strId = folderItems[i].getAttribute("id").toString();
				var trId = parseInt(strId.substring(1, strId.length));
                var newname = prompt('<?php echo $MY_MESSAGES['renamefolder']; ?>', fileManager.folderJSArray[trId][1]);
				if (!newname) continue;
				if (!newname == fileManager.folderJSArray[trId][1]) continue;
				var i_field = fileManager.document.createElement('INPUT');
				i_field.type = 'hidden';
				i_field.name = 'folders[' + i.toString() + '][oldname]';
		  		i_field.value = fileManager.folderJSArray[trId][1];
				postForm2.appendChild(i_field);
				var ii_field = fileManager.document.createElement('INPUT');
				ii_field.type = 'hidden';
				ii_field.name = 'folders[' + i.toString() + '][newname]';
		  		ii_field.value = newname;
				postForm2.appendChild(ii_field);
			}
			for (var i=0; i<fileItemsLength; i++) {
				var strId = fileItems[i].getAttribute("id").toString();
				var trId = parseInt(strId.substring(1, strId.length));
				var	newname = getNewFileName(fileManager.fileJSArray[trId][1]);
				if (!newname) continue;
				if (newname == fileManager.fileJSArray[trId][1]) continue;
		   		var i_field = fileManager.document.createElement('INPUT');
				i_field.type = 'hidden';
				i_field.name = 'files[' + i.toString() + '][oldname]';
		  		i_field.value = fileManager.fileJSArray[trId][1];
				postForm2.appendChild(i_field);
				var ii_field = fileManager.document.createElement('INPUT');
				ii_field.type = 'hidden';
				ii_field.name = 'files[' + i.toString() + '][newname]';
		  		ii_field.value = newname;
				postForm2.appendChild(ii_field);
			}
			changeLoadingStatus('load');
			postForm2.elements["action"].value="rename";
			postForm2.submit();
   		}

		function moveFile() {
			var folderItems = fileManager.sta.getSelectedItems();
			var folderItemsLength = folderItems.length;
			var fileItems = fileManager.stb.getSelectedItems();
			var fileItemsLength = fileItems.length;
			var postForm2 = fileManager.document.getElementById('form2');
			if ((folderItemsLength == 0) && (fileItemsLength == 0)) return false;
			if (!confirm('<?php echo $MY_MESSAGES['renamewarning']; ?>')) return false;
			var postForm2 = fileManager.document.getElementById('form2');
			Dialog("move.php", function(param) {
				if (!param) // user must have pressed Cancel
					return false;
				else {
    				postForm2.elements["newpath"].value=param['newpath'];
    				moveFiles();
				}
			}, null);
		}

	function moveFiles() {
			var folderItems = fileManager.sta.getSelectedItems();
			var folderItemsLength = folderItems.length;
			var fileItems = fileManager.stb.getSelectedItems();
			var fileItemsLength = fileItems.length;
			var postForm2 = fileManager.document.getElementById('form2');
			for (var i=0; i<folderItemsLength; i++) {
				var strId = folderItems[i].getAttribute("id").toString();
				var trId = parseInt(strId.substring(1, strId.length));
		   		var i_field = fileManager.document.createElement('INPUT');
				i_field.type = 'hidden';
				i_field.name = 'folders[' + i.toString() + ']';
		  		i_field.value = fileManager.folderJSArray[trId][1];
				postForm2.appendChild(i_field);
			}
			for (var i=0; i<fileItemsLength; i++) {
				var strId = fileItems[i].getAttribute("id").toString();
				var trId = parseInt(strId.substring(1, strId.length));
				var i_field = fileManager.document.createElement('INPUT');
				i_field.type = 'hidden';
				i_field.name = 'files[' + i.toString() + ']';
			  	i_field.value = fileManager.fileJSArray[trId][1];
				postForm2.appendChild(i_field);
			}
			changeLoadingStatus('load');
			postForm2.elements["action"].value="move";
			postForm2.submit();
		}

		function openFile() {
			var urlPrefix = "<?php echo $MY_URL_TO_OPEN_FILE; ?>";
			var myPath = fileManager.document.getElementById('form2').elements["path"].value;
			var folderItems = fileManager.sta.getSelectedItems();
			var folderItemsLength = folderItems.length;
			var fileItems = fileManager.stb.getSelectedItems();
			var fileItemsLength = fileItems.length;

			for (var i=0; i<folderItemsLength; i++) {
				var strId = folderItems[i].getAttribute("id").toString();
				var trId = parseInt(strId.substring(1, strId.length));
			    window.open(urlPrefix+myPath+fileManager.folderJSArray[trId][1],'','');
		  	}
			for (var i=0; i<fileItemsLength; i++) {
				var strId = fileItems[i].getAttribute("id").toString();
				var trId = parseInt(strId.substring(1, strId.length));
			  	window.open(urlPrefix+myPath+fileManager.fileJSArray[trId][1],'','');
			}
		}

		function doUpload() {
			var isOK = 1;
			var fileObj = document.forms[0].uploadFile;
			if (fileObj == null) return false;

			newname = fileObj.value;
			isOK = checkExtension(newname);
			if (isOK == -2) {
				 alert('<?php echo $MY_MESSAGES['extnotallowed']; ?>');
				 return false;
			}
			if (isOK == -1) {
				alert('<?php echo $MY_MESSAGES['extmissing']; ?>');
				return false;
			}
			changeLoadingStatus('upload');
		}

		function checkExtension(name) {
			var regexp = /\/|\\/;
			var parts = name.split(regexp);
			var filename = parts[parts.length-1].split(".");
			if (filename.length <= 1) {
				return(-1);
			}
			var ext = filename[filename.length-1].toLowerCase();
			
			for (i=0; i<DenyExtensions.length; i++) {
				if (ext == DenyExtensions[i]) return(-2);
			}
			for (i=0; i<AllowExtensions.length; i++) {
				if (ext == AllowExtensions[i])	return(1);
			}
			return(-2);
		}

		function getNewFileName(name) {
			var isOK = 1;
			var newname='';
			do {
				newname = prompt('<?php echo $MY_MESSAGES['renamefile']; ?>', name);
				if (!newname) return false;
				isOK = checkExtension(newname);
				if (isOK == -2) alert('<?php echo $MY_MESSAGES['extnotallowed']; ?>');
				if (isOK == -1) alert('<?php echo $MY_MESSAGES['extmissing']; ?>');
			} while (isOK != 1);
  			return(newname);
		}
		
		function selectFolder() {
			Dialog("move.php", function(param) {
				if (!param) // user must have pressed Cancel
					return false;
				else {
					var postForm2 = fileManager.document.getElementById('form2');
					postForm2.elements["newpath"].value=param['newpath'];
				}
			}, null);
		
		}
		
		function refreshPath(){
			var selection = document.forms[0].path;
			changeDir(selection);
		}

		function winH() {
		   if (window.innerHeight)
		      return window.innerHeight;
		   else if
		   (document.documentElement &&
		   document.documentElement.clientHeight)
		      return document.documentElement.clientHeight;
		   else if
		   (document.body && document.body.clientHeight)
		      return document.body.clientHeight;
		   else
		      return null;
		}

		function resize_iframe() {
			document.getElementById("fileManager").height=winH()-resize_iframe_constant;//resize the iframe according to the size of the window
		}

		function MM_findObj(n, d) { //v4.01
		  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
		    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
		  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
		  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
		  if(!x && d.getElementById) x=d.getElementById(n); return x;
		}

		function MM_showHideLayers() { //v6.0
		  var i,p,v,obj,args=MM_showHideLayers.arguments;
		  for (i=0; i<(args.length-2); i+=3) if ((obj=MM_findObj(args[i]))!=null) { v=args[i+2];
		    if (obj.style) { obj=obj.style; v=(v=='show')?'visible':(v=='hide')?'hidden':v; }
		    obj.visibility=v; }
		}

		function changeLoadingStatus(state) {
			var statusText = null;
			if(state == 'load') {
				statusText = '<?php echo $MY_MESSAGES['loading']; ?> ';
			}
			else if(state == 'upload') {
				statusText = '<?php echo $MY_MESSAGES['uploading']; ?>';
			}
			if(statusText != null) {
				var obj = MM_findObj('loadingStatus');
				if (obj != null && obj.innerHTML != null)
					obj.innerHTML = statusText;
				MM_showHideLayers('loading','','show')
			}
		}

 	/*]]>*/
	</script>
</head>
<body onload="Init();">
		<div class="title">
			<?php echo $MY_MESSAGES['insertfile']; ?>
		</div>
		<form action="files.php?dialogname=<?php echo $MY_NAME; ?>" name="form1" method="post" target="fileManager" enctype="multipart/form-data">
			<div id="loading" style="position:absolute; left:200px; top:130px; width:184px; height:48px; z-index:1" class="statusLayer">
				<div id= "loadingStatus" align="center" style="font-size:large;font-weight:bold;color:#CCCCCC;font-family: Helvetica, sans-serif; z-index:2;  ">
			        <?php echo $MY_MESSAGES['loading']; ?>
				</div>
			</div>
		  	<fieldset>
				<legend>
					<?php
					echo $MY_MESSAGES['filemanager'];
					echo '<span style="font-size:x-small; "> - '.$MY_MESSAGES['ctrlshift'].'</span>';
					?>
				</legend>
				<div style="margin:5px;">
					<label for="path">
						<?php echo $MY_MESSAGES['directory']; ?>
					</label>
			  		<select name="path" id="path" style="width:35em" onChange="changeDir(this)">
						  <option value="/">/</option>
					</select>
		 	
					<?php
						echo '<a href="#" onClick="javascript:goUpDir();"><img src="img/btn_up.gif" width="18" height="18" border="0" title="'.$MY_MESSAGES['up'].'" /></a>';
						if ($MY_ALLOW_CREATE) {
							echo '<a href="#" onClick="javascript:newFolder();"><img src="img/btn_create.gif"  width="18" height="18" border="0" title="'.$MY_MESSAGES['newfolder'].'" /></a>';
						}
						if ($MY_ALLOW_DELETE) {
							echo '<a href="#" onClick="javascript:deleteFile();"><img src="img/btn_delete.gif" width="18" height="18" border="0" title="'.$MY_MESSAGES['delete'].'" /></a>';
						}
						if ($MY_ALLOW_RENAME) {
							echo '<a href="#" onClick="javascript:renameFile();"><img src="img/btn_rename.gif" width="18" height="18" border="0" title="'.$MY_MESSAGES['rename'].'" /></a>';
						}
						if ($MY_ALLOW_MOVE) {
							echo '<a href="#" onClick="javascript:moveFile();"><img src="img/btn_move.gif" width="18" height="18" border="0" title="'.$MY_MESSAGES['move'].'" /></a>';
						}
						echo '<a href="#" onClick="javascript:openFile();"><img src="img/btn_open.gif"  width="18" height="18" border="0" title="'.$MY_MESSAGES['openfile'].'" /></a>';
 
    				 ?>

							<input id="sortby" type="hidden" value="0" />
				</div>

				<div style="margin:5px;">
		        	<!--
					<iframe src="files.php?dialogname=<?php echo $MY_NAME; ?>&amp;refresh=1" name="fileManager" id="fileManager" background: Window;" marginwidth="0" marginheight="0" align="top" scrolling="no" frameborder="0" hspace="0" vspace="0" width="100%"></iframe>
					-->
           			<iframe src="files.php?dialogname=<?php echo $MY_NAME; ?>&amp;refresh=1" name="fileManager" id="fileManager" background="Window" marginwidth="0" marginheight="0" valign:"top" scrolling="no" frameborder="0" hspace="0" vspace="0" width="100%" style="background-color: Window; margin:0px; padding:0px; border:0px; vertical-align:top;"></iframe>
				</div>
				<div style="text-align:center; padding:2px;">
		    <?php
				if ($MY_ALLOW_UPLOAD) {
			?>
					<label for="uploadFile">
					<?php echo $MY_MESSAGES['upload']; ?>
					</label>
				   	<input name="uploadFile" type="file" id="uploadFile" size="72" />
		            <input type="submit" style="width:5em" value="<?php echo $MY_MESSAGES['upload']; ?>" onClick="javascript:return doUpload();" />
    		<?php
				 }
			?>
				</div>

		    </fieldset>
		   
			 <div style="text-align: right; margin-top:5px;">
				  <button type="button" name="refresh" onclick="return refreshPath();"><?php echo $MY_MESSAGES['refresh']; ?></button>
					<span> &nbsp;&nbsp;&nbsp;&nbsp; </span>
				  <button type="button" name="cancel" onclick="return onCancel();"><?php echo $MY_MESSAGES['cancel']; ?></button>
		          <button type="button" name="ok" onclick="return onOK();"><?php echo $MY_MESSAGES['ok']; ?></button>
		     </div>
		     <div style="position:absolute; bottom:-5px; right:-3px;">
			 	<img src="img/btn_Corner.gif" width="14" height="14" border="0" alt="" />
   			</div>
		</form>
	</body>
</html>
