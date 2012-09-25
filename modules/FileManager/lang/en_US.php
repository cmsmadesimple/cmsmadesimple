<?php
$lang['invaliddestname'] = 'Destination name specified is empty or invalid';
$lang['copysuccess'] = 'Items successfully copied';
$lang['copyfailed'] = 'Copy operation failed on %s';
$lang['ie_upload_message'] = 'Drag and Drop, or multiple file upload is not available in Internet Explorer.  To enable these features use a more capable browser such as Firefox or Chrome';
$lang['confirm_unpack'] = 'Are you sure you want to unpack this archive?';
$lang['copy_destdir'] = 'Target Directory';
$lang['copy_destname'] = 'Target Filename';
$lang['itemstocopy'] = 'Copy these Items';
$lang['prompt_copy'] = 'Copy one or more Items';
$lang['movesuccess'] = 'Items successfully moved';
$lang['movefailed'] = 'Move operation failed on %s';
$lang['fileexistsdest'] = '%s already exists at the destination';
$lang['insufficientpermission'] = 'Insufficient permission to %s';
$lang['invalidmovedir'] = 'The destination directory specified is invalid';
$lang['namealreadyexists'] = 'Name already exists';
$lang['movedestdirsame'] = 'The destination directory specified is the same as the source';
$lang['prompt_move'] = 'Move Items to Another Directory';
$lang['info_move'] = 'This option will allow moving one or more items to a different directory.  Use this with caution as it can break links to files, or break other functionality on the website';
$lang['itemstomove'] = 'Move these Items';
$lang['move_destdir'] = 'Destination Directory';
$lang['nodestinationdirs'] = 'Could not find any valid destination directories for the operation';
$lang['deletesuccess'] = 'Items successfully deleted';
$lang['return'] = 'Return';
$lang['error_thumbnotwritable'] = '%s has a thumbnail that does not have write permission';
$lang['error_dirnotempty'] = 'Directory %s is not empty';
$lang['error_notwritable'] = 'No write permission to %s';
$lang['actiondelete'] = 'Delete one or more items';
$lang['thumberror'] = 'Problem creating thumbnail';
$lang['thumbsuccess'] = 'Thumbnail successfully created';
$lang['create'] = 'Create';
$lang['info_createthumb'] = 'The following thumbnail will be created';
$lang['createthumbnail'] = 'Create <em>(or recreate)</em> an image thumbnail';
$lang['info_create_thumbnails'] = 'If enabled, thumbnail files for images will be created on upload';
$lang['create_thumbnails'] = 'Create thumbnails on upload';
$lang['eventdesc_OnFileUploaded'] = 'Sent when a file is uploaded';
$lang['eventhelp_OnFileUploaded'] = <<<EOT
<h4>Parameters:</h4>
<ul>
<li>"file" - The complete file specification to the uploaded file.</li>
<li>"thumb" - If generated, the complete file specification to the generated thumbnail.</li>
</ul>
EOT;

$lang['ok'] = 'Ok';
$lang['folder'] = 'Folder';
$lang['change_working_folder'] = 'Change working folder';
$lang['prompt_dropfiles'] = 'Drop files here to upload';
$lang['refresh'] = 'refresh';
$lang['friendlyname']="File Manager";
$lang['permission']="Usage of the File Manager module";
$lang['permissionadvanced']="Advanced usage of the File Manager module";

$lang['moddescription']="Handling of files and directories in the upload-filesection of cmsms";
$lang['installed']="FileManager version %s installed";
$lang['postinstall']="FileManager module was installed";
$lang['uninstalled']="FileManager module uninstalled";
$lang['really_uninstall']="Are you sure the FileManager module should be uninstalled?";
$lang['upgraded']="FileManager module was upgraded to version %s";
$lang['fileview']="File view";
$lang["switchtofileview"]="Switch to file view";
$lang['settings']="Settings";
$lang['savesettings']="Save settings";

$lang["postletexists"]="Warning! Please removed the modules/FileManager/postlet directory completely, as this has been obsolete for many, many versions.";

$lang['filename']="Filename";
$lang['fileinfo']="Fileinfo";
$lang['fileowner']="Fileowner";
$lang['fileperms']="Permissions";
$lang['filesize']="Size";
$lang['filedate']="Date";
$lang['actions']="Actions";
$lang["newdir"]="New directory";
$lang["createnewdir"]="Create new directory";
$lang["rename"]="Rename";
$lang["renameerror"]="Error! Item could not be renamed";
$lang['delete']="Delete";
$lang['thumbnail'] = 'Create Thumbnail';
$lang['copy']="Copy";
$lang['move']="Move";
$lang['unpack']="Unpack";
$lang["notwritable"]="Not writable";

$lang["afileistoobig"]="A file is too big. Upload cancelled.";
$lang["unknown"]="Unknown";
$lang["fileoutsideuploads"]="You are not allowed to modify files outside the uploads-dir! (That requires the Advanced File Management permission)";

$lang["writable"]="Writable";
$lang["writeprotected"]="Write protected";

$lang['bytes']="bytes";
$lang['kb']="kb";
$lang['mb']="mb";

$lang["files"]="Files:";

$lang['bytesin']="bytes in";
$lang['in']="in";
$lang['files']="files";
$lang['file']="file";
$lang['fileno']="File no.";
$lang['subdirs']="subdirectories";
$lang['subdir']="subdirectory";
$lang['and']="and";

$lang['unknownfileaction']="Internal error: unknown file action";

$lang['confirmsingledelete']="Are you sure?";
$lang['confirmsingledirdelete']="Are you sure this directory should be deleted?";
$lang["dirtreedeletesuccess"]="The dir including content was successfully deleted.";
$lang["dirtreedeletefail"]="An error occured when deleting this dir. Some of the contents may have been deleted, however.";
$lang['singlefiledeletesuccess']="The file was successfully deleted";
$lang['filedeletesuccess']="was successfully deleted";
$lang['singlefiledeletefail']="An error occurred when trying to delete the file";
$lang['filedeletefail']=" was not deleted due to an error";
$lang['singledirdeletesuccess']="The directory  was successfully deleted";
$lang['singledirdeletefail']="An error occurred when trying to delete the directory (is it empty?)";
$lang['deleteselected']="Delete the selected files/directories";
$lang['confirmselected']="Are you sure?";
$lang["confirmdeleteselected"]="Are you sure the selected files should be deleted?";
$lang["deleteselectedcancelled"]="Deletion of selected files cancelled";
$lang["recursetext"]="Recurse into subdirs";
$lang["nofilesselected"]="No files selected";
$lang["morethanonefiledirselected"]="Only one file or directory should be marked for this action.";
$lang['moveselected']="Move the selected files";
$lang['copyselected']="Copy the selected files";
$lang["selecttargetdir"]="Select target dir for move/copy";

$lang["movedto"]="moved to";
$lang["copiedto"]="copied to";
$lang["couldnotmove"]="could not be moved";
$lang["couldnotcopy"]="could not copied";

$lang["filesdeletedsuccessfully"]="%s file(s) was successfully deleted";
$lang["filesdeletedfiled"]="%s file(s) failed being deleted";

$lang["filesmovedsuccessfully"]="%s file(s) was successfully moved";
$lang["filesmovedfailed"]="%s file(s) failed being moved";
$lang["filescopiedsuccessfully"]="%s file(s) was successfully copied";
$lang["filescopiedfailed"]="%s file(s) failed being copied";

$lang["internalerror"]="Internal error (meaning something didn't make sense at all, please report what you did)";


$lang["newname"]="New name:";
$lang["renamesuccess"]="Item successfully renamed";
$lang["renamefailure"]="Renaming of file failed";
$lang["renamecancelled"]="Renaming cancelled";

$lang["owner"]="Owner";
$lang["group"]="Group";
$lang["others"]="Others";
$lang["newpermissions"]="New permissions";
$lang["setpermissions"]="Set permissions";

$lang["permissionstyle"]="Permission style";
$lang["rwxstyle"]="rwx style";
$lang["755style"]="755 style";
$lang["quickmode"]="Quick chmod (like 777)";
$lang["chmod"]="Change permissions";
$lang["chmodselected"]="Change permissions on selected files";
$lang["newpermissions"]="New permissions:";
$lang["chmodsuccess"]="File successfully got new permissions";
$lang["chmodfailure"]="Changing permissions of file failed";
$lang["chmodcancelled"]="Changing of permissions cancelled";

$lang["newuploadfailed"]="%s file(s) failed to upload (or possibly unpack) successfully";
$lang["newuploadsuccess"]="%s file(s) was successfully uploaded (and unpacked if chosen)";

$lang["dirchmodfailmulti"]="Changing permissions on the directory failed, some of it's content may have gotten new permissions, though.";
$lang["dirchmodsuccessmulti"]="Changing permissions on the directory and it's content was successful";
$lang["dirchmodsuccess"]="The directory successfully got new permissions";
$lang["dirchmodfailure"]="Changing permissions of the directory failed";

$lang["dirnotemptyconfirm"]="This dir is not empty. Do you really want to delete it and all content, including subdirs?";
$lang["dirtreedeletecancelled"]="Deletion of dir cancelled";
$lang["imsure"]="I'm sure";
$lang["cancel"]="Cancel";
$lang["ok"]="OK";
$lang['uploadnewfile']="Upload new file(s):";
$lang['unpackafterupload']="Try to unpack file after upload (only tgz and most zip-files)?";
$lang['uploadsuccess']="was uploaded successfully";
$lang['uploadfail']="failed to upload successfully";
$lang['unpacksuccess']="was successfully unpacked";
$lang['unpackfail']=" failed with this error: ";
$lang["packfileopenfail"]="Could not open the packed file for unpacking (non-supported format?)";
$lang["packfilewritefail"]="Could not open the file %s for writing";
//$lang["containsillegalchars"]="Filename contains one or more illegal characters (',\",+,*,\\,/,&,$)";
$lang["afilecontainsillegalchars"]="A filename contains one or more illegal characters (',\",+,*,\\,/,&,$). Upload cancelled.";

$lang['filenotfound'] = 'File not found';
$lang["currentpath"]="Current path:";
$lang['newdirname']="Create new directory:";
$lang['newdirsuccess']="The directory was created successfully";
$lang['newdirfail']="An error occurred while trying to create the directory";
$lang['direxists']="The directory already exists";
$lang['invalidnewdir']="Name of new directory cannot contain chars like /, \\ and '..' ";
$lang["filedateformat"]="m/d/Y H:m:s";
$lang["iconsize"]="Icon size";
$lang["smallicons"]="Small icons";
$lang["largeicons"]="Large icons";
$lang["thousanddelimiter"]="Thousand delimiter";
$lang["none"]="None";
$lang["space"]="space";
$lang["showthumbnails"]="Show thumbnails";
$lang["showthumbnailshelp"]="Enabling this will show thumbnails of image-files if they exist, and will hide the actual thumbnail-file. Disabling will simply show all files.";

$lang["uploadfilesto"]="Uploading files to:";
$lang["uploadview"]="Upload files";
$lang["uploadboxes"]="Number of uploadboxes";
$lang["nothinguploaded"]="Nothing uploaded";
//$lang["newunsupportedarchive"]="Unsupported archive format";
$lang["newunsupportedarchive"]="%s is an unsupported archive format";
$lang["uploadmethod"]="Upload method";
$lang["uploaderstandard"]="Standard html input-method (allows unpacking)";
$lang["enableadvanced"]="Enable advanced mode?";
$lang["advancedhelp"]="Lets you gain access to whole cmsms filesystem, not just /uploads/ (if allowed)";
$lang["showhiddenfileshelp"]="Only effective with advanced mode on";
$lang["showhiddenfiles"]="Show hidden files";
$lang["settingsconfirmsingledelete"]="Confirm deletion of a single file?";
$lang["settingssaved"]="Settings saved";

$lang['help']=<<<EOF
		<h3>What does this do?</h3>
		<p>This module provides file management functions</p>
		<h3>How do I use it?</h3>
		<p>Select it from the content-menu from in the admin. </p>
EOF;

?>