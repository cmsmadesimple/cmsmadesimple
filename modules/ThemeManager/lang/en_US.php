<?php
$lang['error_uploadpermissions'] = '<strong>WARNING:</strong> You have insufficient permissions to upload or install themes.  This may be due to the permissions on the uploads or modules subdirectories, or Safe mode may be in effect on your server.';
$lang['error_nomenumanager'] = 'MenuManager module not found';
$lang['active'] = 'Active';
$lang['default'] = 'Default';
$lang['prompt_themename'] = 'Export Theme As:';
$lang['info_themename'] = 'This field determines the output theme name, irrespective of the input theme name';
$lang['error_editingproblem'] = 'Problem cleaning up and changing the referenced files in this theme.';
$lang['error_problemsavingincludes'] = 'Problem saving the files encoded in the theme.';
$lang['error_nofilesuploaded'] = 'No files were uploaded. Make sure your form tag\'s enctype was set to multipart/form-data and that the right field is being checked for the uploaded file.';
$lang['error_templateexists'] = 'A template with the name "%s" already exists';
$lang['error_stylesheetexists'] = 'A stylesheet with the name "%s" already exists';
$lang['error_nostylesheets'] = 'No stylesheets were detected in this theme';
$lang['error_couldnotcreatetemplate'] = 'Could not create the template definition';
$lang['error_couldnotassoccss'] = 'Problem associating stylesheets with the template';
$lang['error_nooutput'] = 'Nothing to output';
$lang['error_notemplate'] = 'Could not find template';
$lang['error_dtdmismatch'] = 'DTD Version mismatch, cannot import';
$lang['import'] = 'Import';
$lang['upload'] = 'Upload Theme';
$lang['id'] = 'Id';
$lang['name'] = 'Name';
$lang['active'] = 'Active';
$lang['default'] = 'Default';
$lang['export'] = 'Export';
$lang['submit'] = 'Submit';
$lang['friendlyname'] = 'Theme Manager';
$lang['postinstall'] = 'Be sure to set "Manage Themes" permissions to use this module!';
$lang['uninstalled'] = 'Module Uninstalled.';
$lang['installed'] = 'Module version %s installed.';
$lang['prefsupdated'] = 'Module preferences updated.';
$lang['accessdenied'] = 'Access Denied. Please check your permissions.';
$lang['error'] = 'Error!';
$lang['upgraded'] = 'Module upgraded to version %s.';
$lang['moddescription'] = 'A module to allow importing and exporting of content themes (templates and stylesheets)';
$lang['import_succeeded']='The theme was successfully imported';

$lang['help'] = '<h3>What Does This Do?</h3>
<p>This module allows you to import and export templates and their attached stylesheets as "themes".  This allows you to share your look and feel with other cms users.</p
<h3>How Do I Use It</h3>
<p>This module has no front end interface, only an admin interface.  It allows you to select an existing (active) template, and click "Export".  An XML file containing the template and its attached stylesheets will be created and sent you you via download.</p>
<h3>Permissions</h3>
<p>The permissions model is strict for the Theme Manager to ensure database integrity.  The permission "Manage Themes" is required to export themes, and these three permissions ("Add Stylesheets", "Add StyleSheet Assocations", and "Add Templates") are required to be able to import a theme.</p>
<p>As well, the opposite functionality exists, you may upload a theme file (xml format) and have the enclosed templates and stylesheets automatically imported into your cmsms installation.</p>
<h3>Support</h3>
<p>This module does not include commercial support. However, there are a number of resources available to help you with it:</p>
<ul>
<li>For the latest version of this module, FAQs, or to file a Bug Report or buy commercial support, please visit the module homepage at <a href="http://dev.cmsmadesimple.org">dev.cmsmadesimple.org</a>.</li>
<li>Additional discussion of this module may also be found in the <a href="http://forum.cmsmadesimple.org">CMS Made Simple Forums</a>.</li>
<li>The author, calguy1000, can often be found in the <a href="irc://irc.freenode.net/#cms">CMS IRC Channel</a>.</li>
<li>Lastly, you may have some success emailing the author directly.</li>  
</ul>
<p>As per the GPL, this software is provided as-is. Please read the text
of the license for the full disclaimer.</p>

<h3>Copyright and License</h3>
<p>Copyright &copy; 2005, Robert Campbell <a href="mailto:calguy1000@hotmail.com">&lt;calguy1000@hotmail.com&gt;</a>. All Rights Are Reserved.</p>
<p>This module has been released under the <a href="http://www.gnu.org/licenses/licenses.html#GPL">GNU Public License</a>. You must agree to this license before using the module.</p>
';
?>
