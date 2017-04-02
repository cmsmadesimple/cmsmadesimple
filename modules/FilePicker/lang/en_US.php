<?php

// A
$lang['add_profile'] = 'Add a new Profile';

// C
$lang['can_delete'] = 'Allow file deletion';
$lang['can_mkdir'] = 'Allow directory creation';
$lang['can_upload'] = 'Uploads allowed';
$lang['changedir'] = 'Change directory to';
$lang['clear'] = 'Clear';
$lang['confirm_delete'] = 'Are you sure you want to delete this?';
$lang['create_dir'] = 'Create a new folder';

// D
$lang['dimension'] = 'Dimensions';
$lang['delete'] = 'Delete';
$lang['delete_profile'] = 'Delete Profile';

// E
$lang['edit_profile'] = 'Edit Profile';
$lang['error_ajax_invalidfilename'] = 'Sorry, that filename is invalid';
$lang['error_ajax_fileexists'] = 'Sorry, a file or directory with that name already exists';
$lang['error_ajax_mkdir'] = 'A problem occurred creating a the directory %s';
$lang['error_ajax_writepermission'] = 'Sorry, you do not have permission to write to this directory';
$lang['error_failed_ajax'] = 'A problem occurred with an ajax request';
$lang['error_problem_upload'] = 'Sorry, a problem occurred uploading';
$lang['error_upload_acceptFileTypes'] = 'Files of this type are not acceptable in this scope.';
$lang['error_upload_maxFileSize'] = 'The file is too large';
$lang['error_upload_minFileSize'] = 'The file is too small';
$lang['error_upload_maxNumberOfFiles'] = 'You are uploading too many files at once';
$lang['err_profile_topdir'] = 'The top directory specified does not exist';

// F
$lang['filename'] = 'Filename';
$lang['filterby'] = 'Filter by';
$lang['filepickertitle'] = 'CMSMS File Picker';
$lang['fileview'] = 'File view';
$lang['friendlyname'] = 'File Picker';

// H
$lang['hdr_add_profile'] = 'Add profile';
$lang['hdr_edit_profile'] = 'Edit profile';
$lang['HelpPopupTitle_ProfileName'] = 'Profile Name';
$lang['HelpPopup_ProfileName'] = 'Each profile should have a simple, unique name.  Names should only contain alphanumeric characters, and/or the Underscore character.';
$lang['HelpPopupTitle_ProfileCan_Delete'] = 'Allow deleting files';
$lang['HelpPopup_ProfileCan_Delete'] = 'Optionally allow users to delete files during the selection process';
$lang['HelpPopupTitle_ProfileCan_Mkdir'] = 'Allow deleting files';
$lang['HelpPopup_ProfileCan_Mkdir'] = 'Optionally allow users to create new directories (below the specified top directory) during the selection process.';
$lang['HelpPopupTitle_ProfileCan_Upload'] = 'Allow uploading';
$lang['HelpPopup_ProfileCan_Upload'] = 'Optionally allow users to upload files during the selection process';
$lang['HelpPopupTitle_ProfileDir'] = 'Top Directory';
$lang['HelpPopup_ProfileDir'] = 'Optionally enter the relative path of a directory (relative to the uploads path) to restrict operations to.';
$lang['HelpPopupTitle_ProfileShowthumbs'] = 'Show Thumbnails';
$lang['HelpPopup_ProfileShowthumbs'] = 'If enabled, thumbnails will be visible for image files for which thumbnails are generated.';

// N
$lang['name'] = 'Name';
$lang['no_profiles'] = 'No profiles defined yet. You can add them by clicking the button above.';

// O
$lang['ok'] = 'Ok';

// S
$lang['select_an_audio_file'] = 'Select an audio file';
$lang['select_a_video_file'] = 'Select a video file';
$lang['select_a_media_file'] = 'Select a media file';
$lang['select_a_document'] = 'Select a document';
$lang['select_an_archive_file'] = 'Select an archive file';
$lang['select_a_file'] = 'Select a file';
$lang['select_an_image'] = 'Select an image';
$lang['select_upload_files'] = 'Select files to upload';
$lang['show_thumbs'] = 'Show thumbnails';
$lang['size'] = 'Size';
$lang['switcharchive'] = 'Only show archive files';
$lang['switchaudio'] = 'Only show audio files';
$lang['switchfiles'] = 'Only show regular files';
$lang['switchgrid'] = 'Display files in a grid';
$lang['switchimage'] = 'Only show image files';
$lang['switchlist'] = 'Display files as a list';
$lang['switchreset'] = 'Show all files';
$lang['switchvideo'] = 'Only show video files';

// T
$lang['th_created'] = 'Created';
$lang['th_default'] = 'Default';
$lang['th_id'] = 'ID';
$lang['th_last_edited'] = 'Last Edited';
$lang['th_name'] = 'Name';
$lang['th_reltop'] = 'Top Directory';
$lang['title_mkdir'] = 'Create Directory';
$lang['topdir'] = 'Top Directory';
$lang['type'] = 'Type';

// U
$lang['upload'] = 'Upload';

// Y
$lang['youareintext'] = 'The current working directory (relative to the top of the installation)';

// HELP TEXT
$lang['help'] = <<<EOT
<h3>What does this do?</h3>
<p>This module provides the generic ability to allow an authorized admin editor to select a file.  I.e:  to select an image for use in a WYSIWYG field, or to associate an image or thumbnail with a page, or attach a PDF file to a news article.  The module also has a small amount of ancillary functionality to allow authorized users to upload and delete files, or to create and remove subdirectories.</p>
<p>This module also allows for the creation of multiple profiles with different capabilities.  Profiles can be used by the <code>{cms_filepicker}</code> plugin or by the module's &quot;select&quot; action when definining how the picker should behave.   Other module parameters, or user permissions can override the settings defined in the profile.</p>

<h3>How Do I Use It</h3>
<p>This module is intended to be used in the core or third party modules via various core API's.  And via the {cms_filepicker} plugin.</p>
<p>Additionally, this module can be called directly via the <code>{cms_module module=FilePicker action=select name=string [profile=string] [type=string] [value=string]}</code> tag, but this is not recommended.   See the {cms_filepicker} tag for information about the type, and other parameters.</p>

<h3>Support</h3>
<p>As per the GPL, this software is provided as-is. Please read the text of the license for the full disclaimer.</p>

<h3>Copyright and License</h3>
<p>Copyright &copy; 2017, JoMorg and calguy1000. All Rights Are Reserved.</p>
<p>This module has been released under the <a href="http://www.gnu.org/licenses/licenses.html#GPL">GNU Public License</a>. You must agree to this license before using the module.</p>
EOT;
