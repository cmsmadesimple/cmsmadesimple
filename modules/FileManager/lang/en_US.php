<?php
$lang['755style'] = '755 style';

// A
$lang['actiondelete'] = 'Delete one or more items';
$lang['actions']= 'Actions';
$lang['advancedhelp'] = 'Lets you gain access to whole CMSMS filesystem, not just /uploads/ (if allowed)';
$lang['afilecontainsillegalchars'] = 'A filename contains one or more illegal characters (\',",+,*,\\,/,&,$). Upload cancelled.';
$lang['afileistoobig'] = 'A file is too big. Upload cancelled.';
$lang['and'] = 'and';
$lang['angle'] = 'Angle';

// B
$lang['bytes'] = 'bytes';
$lang['bytesin'] = 'bytes in';

// C
$lang['cancel'] = 'Cancel';
$lang['change_working_folder'] = 'Change working folder';
$lang['chmod'] = 'Change permissions';
$lang['chmodcancelled'] = 'Changing of permissions cancelled';
$lang['chmodfailure'] = 'Changing permissions of file failed';
$lang['chmodselected'] = 'Change permissions on selected files';
$lang['chmodsuccess'] = 'File successfully got new permissions';
$lang['confirmdeleteselected'] = 'Are you sure the selected files should be deleted?';
$lang['confirmselected'] = 'Are you sure?';
$lang['confirmsingledelete'] = 'Are you sure?';
$lang['confirmsingledirdelete'] = 'Are you sure this directory should be deleted?';
$lang['confirm_unpack'] = 'Are you sure you want to unpack this archive?';
$lang['copiedto'] = 'copied to';
$lang['copy'] = 'Copy';
$lang['copyfailed'] = 'Copy operation failed on %s';
$lang['copyselected'] = 'Copy the selected files';
$lang['copysuccess'] = 'Items successfully copied';
$lang['copy_destdir'] = 'Target Directory';
$lang['copy_destname'] = 'Target Filename';
$lang['couldnotcopy'] = 'could not copied';
$lang['couldnotmove'] = 'could not be moved';
$lang['create'] = 'Create';
$lang['createnewdir'] = 'Create new directory';
$lang['createthumbnail'] = 'Create <em>(or recreate)</em> an image thumbnail';
$lang['create_thumbnails'] = 'Create thumbnails on upload';
$lang['croptosize'] = 'Crop the rotated image to the size of the original image';
$lang['currentpath'] = 'Current path:';

// D
$lang['delete'] = 'Delete';
$lang['deleteselected'] = 'Delete the selected files/directories';
$lang['deleteselectedcancelled'] = 'Deletion of selected files cancelled';
$lang['deletesuccess'] = 'Items successfully deleted';
$lang['dirchmodfailmulti'] = 'Changing permissions on the directory failed, some of it\'s content may have gotten new permissions, though.';
$lang['dirchmodfailure'] = 'Changing permissions of the directory failed';
$lang['dirchmodsuccess'] = 'The directory successfully got new permissions';
$lang['dirchmodsuccessmulti'] = 'Changing permissions on the directory and it\'s content was successful';
$lang['direxists'] = 'The directory already exists';
$lang['dirnotemptyconfirm'] = 'This dir is not empty. Do you really want to delete it and all content, including subdirs?';
$lang['dirtreedeletecancelled'] = 'Deletion of dir cancelled';
$lang['dirtreedeletefail'] = 'An error occured when deleting this dir. Some of the contents may have been deleted, however.';
$lang['dirtreedeletesuccess'] = 'The dir including content was successfully deleted.';

// E
$lang['enableadvanced'] = 'Enable advanced mode?';
$lang['error_dirnotempty'] = 'Directory %s is not empty';
$lang['error_notwritable'] = 'No write permission to %s';
$lang['error_thumbnotwritable'] = '%s has a thumbnail that does not have write permission';
$lang['eventdesc_OnFileUploaded'] = 'Sent when a file is uploaded';
$lang['eventhelp_OnFileUploaded'] = <<<EOT
<h4>Parameters:</h4>
<ul>
<li>"file" - The complete file specification to the uploaded file</li>
<li>"thumb" - If generated, the complete file specification to the generated thumbnail</li>
</ul>
EOT;

// F
$lang['file'] = 'file';
$lang['filedate'] = 'Date';
$lang['filedateformat'] = 'm/d/Y H:m:s';
$lang['filedeletefail'] = ' was not deleted due to an error';
$lang['filedeletesuccess'] = 'was successfully deleted';
$lang['fileexistsdest'] = '%s already exists at the destination';
$lang['fileimagetype'] = 'Cannot perform this action on this type of image';
$lang['fileinfo'] = 'File info';
$lang['filename'] = 'Filename';
$lang['fileno'] = 'File no.';
$lang['filenotfound'] = 'File not found';
$lang['filenotimage'] = 'File specified is not an image (or there is some error with the file)';
$lang['fileoutsideuploads'] = 'You are not allowed to modify files outside the uploads-dir! (That requires the Advanced File Management permission)';
$lang['fileowner'] = 'Owner';
$lang['fileperms'] = 'Permissions';
$lang['files'] = 'files';
$lang['filescopiedfailed'] = '%s file(s) failed being copied';
$lang['filescopiedsuccessfully'] = '%s file(s) was successfully copied';
$lang['filesdeletedfiled'] = '%s file(s) failed being deleted';
$lang['filesdeletedsuccessfully'] = '%s file(s) was successfully deleted';
$lang['filesize'] = 'Size';
$lang['filesmovedfailed'] = '%s file(s) failed being moved';
$lang['filesmovedsuccessfully'] = '%s file(s) was successfully moved';
$lang['fileview'] = 'File view';
$lang['folder'] = 'Folder';
$lang['friendlyname'] = 'File Manager';

// G
$lang['group'] = 'Group';

// H
$lang['help']=<<<EOF
		<h3>What does this do?</h3>
		<p>This module provides file management functions.</p>
		<h3>How do I use it?</h3>
		<p>Select it from the content-menu from in the admin.</p>
EOF;

// I
$lang['iconsize'] = 'Icon size';
$lang['ie_upload_message'] = 'Drag and Drop, or multiple file upload is not available in Internet Explorer.  To enable these features use a more capable browser such as Firefox or Chrome';
$lang['image'] = 'Image';
$lang['imsure'] = 'I\'m sure';
$lang['in'] = 'in';
$lang['info_createthumb'] = 'The following thumbnail will be created';
$lang['info_create_thumbnails'] = 'If enabled, thumbnail files for images will be created on upload';
$lang['info_move'] = 'This option will allow moving one or more items to a different directory.  Use this with caution as it can break links to files, or break other functionality on the website';
$lang['info_rotate'] = 'The saved image will not appear identical to the rotated image in this view, and the dimensions of the saved image may change';
$lang['info_rotate_slider'] = 'Drag this slider to the desired rotation angle';
$lang['installed'] = 'FileManager version %s installed';
$lang['insufficientpermission'] = 'Insufficient permission to %s';
$lang['internalerror'] = 'Internal error (meaning something didn\'t make sense at all, please report what you did)';
$lang['invaliddestname'] = 'Destination name specified is empty or invalid';
$lang['invalidmovedir'] = 'The destination directory specified is invalid';
$lang['invalidnewdir'] = 'Name of new directory cannot contain chars like /, \\ and \'..\' ';
$lang['itemstocopy'] = 'Copy these Items';
$lang['itemstomove'] = 'Move these Items';

// K
$lang['kb'] = 'kb';

// L
$lang['largeicons'] = 'Large icons';

// M
$lang['mb'] = 'mb';
$lang['mimetype'] = 'Mime Type';
$lang['moddescription'] = 'Handling of files and directories in the upload-filesection of cmsms';
$lang['morethanonefiledirselected'] = 'Only one file or directory should be marked for this action.';
$lang['move'] = 'Move';
$lang['movedestdirsame'] = 'The destination directory specified is the same as the source';
$lang['movedto'] = 'moved to';
$lang['movefailed'] = 'Move operation failed on %s';
$lang['moveselected'] = 'Move the selected files';
$lang['movesuccess'] = 'Items successfully moved';
$lang['move_destdir'] = 'Destination Directory';

// N
$lang['namealreadyexists'] = 'Name already exists';
$lang['newdir'] = 'New directory';
$lang['newdirfail'] = 'An error occurred while trying to create the directory';
$lang['newdirname'] = 'Create new directory:';
$lang['newdirsuccess'] = 'The directory was created successfully';
$lang['newname'] = 'New name:';
$lang['newpermissions'] = 'New permissions';
$lang['newpermissions'] = 'New permissions:';
$lang['newunsupportedarchive'] = '%s is an unsupported archive format';
$lang['newuploadfailed'] = '%s file(s) failed to upload (or possibly unpack) successfully';
$lang['newuploadsuccess'] = '%s file(s) was successfully uploaded (and unpacked if chosen)';
$lang['nodestinationdirs'] = 'Could not find any valid destination directories for the operation';
$lang['nofilesselected'] = 'No files selected';
$lang['none'] = 'None';
$lang['nothinguploaded'] = 'Nothing uploaded';
$lang['notwritable'] = 'Not writable';

// O
$lang['ok'] = 'OK';
$lang['others'] = 'Others';
$lang['owner'] = 'Owner';

// P
$lang['packfileopenfail'] = 'Could not open the packed file for unpacking (non-supported format?)';
$lang['packfilewritefail'] = 'Could not open the file %s for writing';
$lang['permission'] = 'Usage of the File Manager module';
$lang['permissionadvanced'] = 'Advanced usage of the File Manager module';
$lang['permissionstyle'] = 'Permission style';
$lang['pie'] = 'Resize/Crop';
$lang['pie_image_natural_size'] = 'Image natual size';
$lang['pie_lock_proportion'] = 'lock proportion';
$lang['pie_resize'] = 'resize';
$lang['pie_crop'] = 'crop';
$lang['pie_reset'] = 'reset';
$lang['pie_close'] = 'close';
$lang['pie_image_w'] = 'image width';
$lang['pie_image_h'] = 'image height';
$lang['pie_crop_x'] = 'crop x';
$lang['pie_crop_y'] = 'crop y';
$lang['pie_crop_w'] = 'crop width';
$lang['pie_crop_h'] = 'crop height';
$lang['pie_warn_action'] = 'Are you sure? There is no Crtl+z down here..';
$lang['pie_warn_reset'] = 'Are you sure? It will reset your current modifications';
$lang['postinstall'] = 'FileManager module was installed';
$lang['postletexists'] = 'Warning! Please remove the modules/FileManager/postlet directory completely. The CMS doesn\'t use it anymore.';
$lang['predefined'] = 'Predefined Angles';
$lang['prompt_copy'] = 'Copy one or more Items';
$lang['prompt_dropfiles'] = 'Drop files here to upload';
$lang['prompt_move'] = 'Move Items to Another Directory';

// Q
$lang['quickmode'] = 'Quick chmod (like 777)';

// R
$lang['really_uninstall'] = 'Are you sure the FileManager module should be uninstalled?';
$lang['recursetext'] = 'Recurse into subdirs';
$lang['refresh'] = 'refresh';
$lang['rename'] = 'Rename';
$lang['renamecancelled'] = 'Renaming cancelled';
$lang['renameerror'] = 'Error! Item could not be renamed';
$lang['renamefailure'] = 'Renaming of file failed';
$lang['renamesuccess'] = 'Item successfully renamed';
$lang['return'] = 'Return';
$lang['rotate'] = 'Rotate';
$lang['rotateimage'] = 'Rotate Image';
$lang['rwxstyle'] = 'rwx style';

// S
$lang['save'] = 'Save';
$lang['savesettings'] = 'Save settings';
$lang['selecttargetdir'] = 'Select target dir for move/copy';
$lang['setpermissions'] = 'Set permissions';
$lang['settings'] = 'Settings';
$lang['settingsconfirmsingledelete'] = 'Confirm deletion of a single file?';
$lang['settingssaved'] = 'Settings saved';
$lang['showhiddenfiles'] = 'Show hidden files';
$lang['showhiddenfileshelp'] = 'Only effective with advanced mode on';
$lang['showthumbnails'] = 'Show thumbnails';
$lang['showthumbnailshelp'] = 'Enabling this will show thumbnails of image-files if they exist, and will hide the actual thumbnail-file. Disabling will simply show all files.';
$lang['singledirdeletefail'] = 'An error occurred when trying to delete the directory (is it empty?)';
$lang['singledirdeletesuccess'] = 'The directory  was successfully deleted';
$lang['singlefiledeletefail'] = 'An error occurred when trying to delete the file';
$lang['singlefiledeletesuccess'] = 'The file was successfully deleted';
$lang['smallicons'] = 'Small icons';
$lang['space'] = 'space';
$lang['subdir'] = 'subdirectory';
$lang['subdirs'] = 'subdirectories';
$lang['switchtofileview'] = 'Switch to file view';

// T
$lang['thousanddelimiter'] = 'Thousand delimiter';
$lang['thumberror'] = 'Problem creating thumbnail';
$lang['thumbnail'] = 'Create Thumbnail';
$lang['thumbsuccess'] = 'Thumbnail successfully created';

// U
$lang['uninstalled'] = 'FileManager module uninstalled';
$lang['unknown'] = 'Unknown';
$lang['unknownfileaction'] = 'Internal error: unknown file action';
$lang['unpack'] = 'Unpack';
$lang['unpackafterupload'] = 'Try to unpack file after upload (only tgz and most zip-files)?';
$lang['unpackfail'] = ' failed with this error: ';
$lang['unpacksuccess'] = 'was successfully unpacked';
$lang['upgraded'] = 'FileManager module was upgraded to version %s';
$lang['uploadboxes'] = 'Number of uploadboxes';
$lang['uploaderstandard'] = 'Standard html input-method (allows unpacking)';
$lang['uploadfail'] = 'failed to upload successfully';
$lang['uploadfilesto'] = 'Uploading files to:';
$lang['uploadmethod'] = 'Upload method';
$lang['uploadnewfile'] = 'Upload new file(s):';
$lang['uploadsuccess'] = 'was uploaded successfully';
$lang['uploadview'] = 'Upload files';

// V
$lang['view'] = 'View';

// W
$lang['writable'] = 'Writable';
$lang['writeprotected'] = 'Write protected';

?>