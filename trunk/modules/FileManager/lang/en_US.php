<?php
$lang['755style'] = '755 style';

// A
$lang['actiondelete'] = 'Delete one or more items';
$lang['actions']= 'Actions';
$lang['advancedhelp'] = 'Lets you gain access to whole CMSMS file system, not just /uploads/ (if allowed)';
$lang['afilecontainsillegalchars'] = 'A file name contains one or more illegal characters (\',",+,*,\\,/,&,$). Upload canceled.';
$lang['afileistoobig'] = 'A file is too big. The upload is canceled.';
$lang['and'] = 'and';
$lang['angle'] = 'Angle';

// B
$lang['bytes'] = 'bytes';
$lang['bytesin'] = 'bytes in';

// C
$lang['cancel'] = 'Cancel';
$lang['change_working_folder'] = 'Change working folder';
$lang['chmod'] = 'Change permissions';
$lang['chmodcancelled'] = 'Changing of permissions canceled';
$lang['chmodfailure'] = 'Changing permissions of file failed';
$lang['chmodselected'] = 'Change permissions on selected files';
$lang['chmodsuccess'] = 'File successfully got new permissions';
$lang['confirmdeleteselected'] = 'Are you sure the selected files should be deleted?';
$lang['confirmselected'] = 'Are you sure?';
$lang['confirmsingledelete'] = 'Are you sure?';
$lang['confirmsingledirdelete'] = 'Are you sure this directory should be deleted?';
$lang['confirm_delete'] = 'Are you sure you want to remove this file?';
$lang['confirm_unpack'] = 'Are you sure you want to unpack this file?';
$lang['copiedto'] = 'copied to';
$lang['copy'] = 'Copy';
$lang['copyfailed'] = 'Copy operation failed on %s';
$lang['copyselected'] = 'Copy the selected files';
$lang['copysuccess'] = 'Items successfully copied';
$lang['copy_destdir'] = 'Target Directory';
$lang['copy_destname'] = 'Target File name';
$lang['couldnotcopy'] = 'could not copied';
$lang['couldnotmove'] = 'could not be moved';
$lang['create'] = 'Create';
$lang['createnewdir'] = 'Create new directory';
$lang['createthumbnail'] = 'Create <em>(or recreate)</em> an image thumbnail';
$lang['create_thumbnails'] = 'Create thumbnails on upload';
$lang['crop'] = 'Crop';
$lang['currentpath'] = 'Current path:';

// D
$lang['delete'] = 'Delete';
$lang['deleteselected'] = 'Delete the selected files/directories';
$lang['deleteselectedcancelled'] = 'Deletion of selected files canceled';
$lang['deletesuccess'] = 'Items successfully deleted';
$lang['desc_filemanager_settings'] = 'Edit File Manager Settings';
$lang['dirchmodfailmulti'] = 'Changing permissions on the directory failed, some of it\'s content may have gotten new permissions, though.';
$lang['dirchmodfailure'] = 'Changing permissions of the directory failed';
$lang['dirchmodsuccess'] = 'The directory successfully got new permissions';
$lang['dirchmodsuccessmulti'] = 'Changing permissions on the directory and it\'s content was successful';
$lang['direxists'] = 'The directory already exists';
$lang['dirnotemptyconfirm'] = 'The directory isn\'t empty! Do you really want to delete it, including all content and subdirectories?';
$lang['dirtreedeletecancelled'] = 'Deletion of directory canceled';
$lang['dirtreedeletefail'] = 'An error occurred when deleting this directory. Some of the contents may have been deleted, however.';
$lang['dirtreedeletesuccess'] = 'The directory including content was successfully deleted.';

// E
$lang['enableadvanced'] = 'Enable advanced mode?';
$lang['error_dirnotempty'] = 'Directory %s is not empty';
$lang['error_notwritable'] = 'No write permission to %s';
$lang['error_thumbnotwritable'] = '%s has a thumbnail that does not have write permission';

$lang['eventdesc_OnFileDeleted'] = 'Sent after a file is deleted';
$lang['eventhelp_OnFileDeleted'] = <<<EOT
<h4>Parameters:</h4>
<ul>
<li>"file" - The complete file specification to the deleted file</li>
<li>"thumb" - If it previously existed, the complete file specification to the deleted thumbnail</li>
</ul>
EOT;
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
$lang['filemovesame'] = 'Cannot move a directory';
$lang['filename'] = 'File name';
$lang['fileno'] = 'File no.';
$lang['filenotfound'] = 'File not found';
$lang['filenotimage'] = 'File specified is not an image (or there is some error with the file)';
$lang['fileoutsideuploads'] = 'You are not allowed to modify files outside the uploads directory! (That requires the Advanced File Management permission)';
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
$lang['help_advancedmode'] ='<p>Advanced mode allows users to browse and manage all of the files in the CMSMS installation (including system files).</p><br /><p><strong>Use Caution</strong> as it is possible to corrupt a working installation when using advanced mode.</p>';
$lang['help_create_thumbnails'] = 'If enabled, FileManager will automatically create a new thumbnail for each newly uploaded image';
$lang['help_iconsize'] = 'This option allows specifying the size of the icons displayed in the file list.';
$lang['help_permissionstyle'] = '<p>This option allows changing the way that permissions are displayed in the file list.  Options include somewhat human readable <em>(rwx)</em> style, or octal <em>(755)</em> format.</p>';
$lang['help_showhiddenfiles'] = '<p>When enabled files and directories whose name begins with a . <em>(dot)</em> or _ <em>(underscore)</em> will be displayed in the list.  CMSMS <em>(and other applications)</em> occasionally stores important configuration information, or cache information in these directories.</p><p><strong>Note:</strong> This option has no effect unless advanced mode is also enabled.</p>';
$lang['help_showthumbnails'] = 'If enabled, the file list will display a thumbnail <em>(if one exists)</em> for all images.  If disabled, the system will attempt to display an icon representing the file type.';
$lang['help']=<<<EOF
<h3>What does this do?</h3>
<p>This module provides your CMS Made Simple website with file management functions.</p>
EOF;
$lang['help_postrotate'] = <<<EOT
<p>Options:</p>
<br />
<ul>
  <li><strong>None</strong> - No postrotate action will be taken.  The rotated image could be larger than the original image.</li>
  <li><strong>Crop</strong> - The rotated image will be cropped to the size of the original source image.  This may result in some of the image being clipped.</li>
  <li><strong>Resize</strong> - The rotated image will be resized to fit inside the largest dimension of the rotated image.  The result will most likely be larger than the original image, but nothing should be clipped.';
</ul>
EOT;

// I
$lang['iconsize'] = 'Icon size';
$lang['ie_upload_message'] = 'Drag and Drop, or multiple file upload is not available in Internet Explorer. To enable these features use a more capable browser such as Firefox or Chrome';
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
$lang['invalidchdir'] = 'Attempt to set working directory to an invalid location: %s';
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
$lang['moddescription'] = 'Handling of files and directories in the upload-file section of CMSMS';
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
$lang['pie_image_natural_size'] = 'Image natural size';
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
$lang['pie_warn_action'] = 'Are you sure? There is no CTRL+Z down here..';
$lang['pie_warn_reset'] = 'Are you sure? It will reset your current modifications';
$lang['postinstall'] = 'FileManager module was installed';
$lang['postrotate'] = 'Action for after image rotation';
$lang['predefined'] = 'Predefined Angles';
$lang['prompt_copy'] = 'Copy one or more Items';
$lang['prompt_dropfiles'] = 'Drop files here to upload';
$lang['prompt_move'] = 'Move Items to Another Directory';

// Q
$lang['quickmode'] = 'Quick CHMOD (like 777)';

// R
$lang['really_uninstall'] = 'Are you sure the File Manager module should be uninstalled?';
$lang['recursetext'] = 'Recurse into subdirectories';
$lang['refresh'] = 'refresh';
$lang['rename'] = 'Rename';
$lang['renamecancelled'] = 'Renaming canceled';
$lang['renameerror'] = 'Error! Item could not be renamed';
$lang['renamefailure'] = 'Renaming of file failed';
$lang['renamesuccess'] = 'Item successfully renamed';
$lang['resize'] = 'Resize';
$lang['resizecrop'] = 'Resize/Crop';
$lang['return'] = 'Return';
$lang['rotate'] = 'Rotate';
$lang['rotateimage'] = 'Rotate Image';
$lang['rotate_neg180'] = 'Negative 180 degrees';
$lang['rotate_neg135'] = 'Negative 135 degrees';
$lang['rotate_neg90'] = 'Negative 90 degrees';
$lang['rotate_neg45'] = 'Negative 45 degrees';
$lang['rotate_neg30'] = 'Negative 30 degrees';
$lang['rotate_pos30'] = 'Positive 30 degrees';
$lang['rotate_pos45'] = 'Positive 45 degrees';
$lang['rotate_pos90'] = 'Positive 90 degrees';
$lang['rotate_pos135'] = 'Positive 135 degrees';
$lang['rotate_pos180'] = 'Positive 180 degrees';
$lang['rwxstyle'] = 'rwx style';

// S
$lang['save'] = 'Save';
$lang['savesettings'] = 'Save settings';
$lang['selecttargetdir'] = 'Select target directory for move/copy';
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
$lang['site_root'] = 'Root';
$lang['smallicons'] = 'Small icons';
$lang['space'] = 'space';
$lang['subdir'] = 'subdirectory';
$lang['subdirs'] = 'subdirectories';
$lang['submit'] = 'Submit';
$lang['switchtofileview'] = 'Switch to file view';

// T
$lang['title_changedir'] = 'Change working directory to this directory';
$lang['title_changeupdir'] = 'Go to the parent folder';
$lang['title_col_filedate'] = 'This column displays the last modification date of the file';
$lang['title_col_fileperms'] = 'This column displays the permissions of the file';
$lang['title_col_filesize'] = 'This column displays the size of the file';
$lang['title_col_fileowner'] = 'This column displays the user name of the owner of the file';
$lang['title_copy'] = 'Copy the selected item';
$lang['title_delete'] = 'Delete the selected item';
$lang['title_dropzone'] = 'You can drop files here from your file manager to automatically begin uploading';
$lang['title_filefield'] = 'Select one or more files they will automatically begin uploading';
$lang['title_filemanager_settings'] = 'Settings - File Manager';
$lang['title_move'] = 'Move the selected item to another folder';
$lang['title_newdir'] = 'Create a new directory';
$lang['title_rawurl'] = 'View this item natively in the browser';
$lang['title_rename'] = 'Rename the selected item';
$lang['title_resizecrop'] = 'Resize and/or Crop the selected image';
$lang['title_rotate'] = 'Rotate the selected image';
$lang['title_tagall'] = 'Select all of the visible items';
$lang['title_thumbnail'] = 'Create a thumbnail of this image';
$lang['title_unpack'] = 'Unpack the selected archive';
$lang['title_view'] = 'View the selected item';
$lang['title_view_newwindow'] = 'View the selected item in a new window';
$lang['thousanddelimiter'] = 'Thousand delimiter';
$lang['thumberror'] = 'Problem creating thumbnail';
$lang['thumbnail'] = 'Create Thumbnail';
$lang['thumbsuccess'] = 'Thumbnail successfully created';
$lang['toggle'] = 'Toggle selection';
$lang['top'] = 'Top';

// U
$lang['uninstalled'] = 'The FileManager module is uninstalled';
$lang['unknown'] = 'Unknown';
$lang['unknownfileaction'] = 'Internal error: unknown file action';
$lang['unpack'] = 'Unpack';
$lang['unpackafterupload'] = 'Try to unpack file after upload (only tgz and most zip-files)?';
$lang['unpackfail'] = ' failed with this error: ';
$lang['unpacksuccess'] = 'is successfully unpacked';
$lang['upgraded'] = 'The FileManager module is upgraded to version %s';
$lang['uploaderstandard'] = 'Standard HTML input-method (allows unpacking)';
$lang['uploadfail'] = 'failed to upload successfully';
$lang['uploadmethod'] = 'Upload method';
$lang['uploadnewfile'] = 'Upload new file(s):';
$lang['uploadsuccess'] = 'is uploaded successfully';

// V
$lang['view'] = 'View';

// W
$lang['writable'] = 'Writable';
$lang['writeprotected'] = 'Write protected';

?>