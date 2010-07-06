<?php
$lang['install'] = 'Install';
$lang['changepermissions'] = 'Change Permissions';
$lang['uninstall'] = 'Uninstall';
$lang['installed_modules'] = 'Installed Modules';
$lang['available_updates'] = 'Modules Available for Update';
$lang['all_modules_up_to_date'] = 'There are no newer modules available in the repository';
$lang['error_module_object'] = 'Error: could not get an instance of the %s module';
$lang['error_nomatchingmodules'] = 'Error: could not find any matching modules in the repository';
$lang['error_nomodules'] = 'Error: could not retrieve list of installed modules';
$lang['upgrade_available'] = 'Newer version available (%s), you have (%s)';
$lang['newversions'] = 'Available Upgrades';
$lang['error_depends'] = 'One or more dependencies are not installed.  You should install the dependencies first';
$lang['msg_nodependencies'] = 'This file has not listed any dependencies';
$lang['dependstxt'] = 'Dependencies';
$lang['use_at_your_own_risk'] = 'Use at Your Own Risk';
$lang['compatibility_disclaimer'] = 'The modules displayed here are contributed by both the CMS Developers, and independant third parties.  We make no guarantees that the modules available here are functional, tested, or compatible with your system.  You are encouraged to read the information found in the help and about links for each module before attempting the installation.';
$lang['notice'] = 'Notice';
$lang['general_notice'] = 'The versions displayed here represent the latest XML files uploaded to your selected repository (usually the CMS %s).  They may or may not represent the latest available versions.'; 
$lang['incompatible'] = 'Incompatible';
$lang['prompt_settings'] = 'Settings';
$lang['prompt_otheroptions'] = 'Other Options';
$lang['reset'] = 'Reset';
$lang['error_permissions'] = '<strong><em>WARNING:</em></strong> Insufficient directory permissions to install modules.  You may also be experiencing problems with PHP Safe mode.  Please ensure that safe mode is disabled, and that file system permissions are sufficient.';
$lang['error_minimumrepository'] = 'The repository version is not compatible with this module manager';
$lang['prompt_reseturl'] = 'Reset URL to preset default';
$lang['prompt_resetcache'] = 'Reset the local cache of repository data';
$lang['prompt_dl_chunksize'] = 'Download Chunk Size (Kb)';
$lang['text_dl_chunksize'] = 'The maximum amount of data to download from the server in one chunk (when installing a module)';
$lang['error_nofilesize'] = 'No filesize parameter supplied';
$lang['error_nofilename'] = 'No filename parameter supplied';
$lang['error_checksum'] = 'Checksum error.  This probably indicates a corrupt file, either when it was uploaded to the repository, or a problem in transit down to your machine.';
$lang['cantdownload'] = 'Cannot Download';
$lang['download'] = 'Download &amp; Install';
$lang['error_moduleinstallfailed'] = 'Module installation failed';
$lang['error_connectnomodules'] = 'Although a connection was successfully made to the specified module repository.  It appears that this repository is not yet sharing any modules';
$lang['submit'] = 'Submit';
$lang['text_repository_url'] = 'The URL should be in the form http://www.mycmssite.com/path/soap.php?module=ModuleRepository';
$lang['prompt_repository_url'] = 'ModuleRepository URL';
$lang['availmodules'] = 'Available Modules'; 
$lang['preferences'] = 'Preferences';
$lang['preferencessaved'] = 'Preferences saved';
$lang['repositorycount'] = 'Modules found in the repository';
$lang['instcount'] = 'Modules currently installed';
$lang['availablemodules'] = 'The currrent status of modules available from the current repository';
$lang['helptxt'] = 'Help';
$lang['abouttxt'] = 'About';
$lang['xmltext'] = 'XML File';
$lang['nametext'] = 'Module Name';
$lang['vertext'] = 'Version';
$lang['sizetext'] = 'Size (Kilobytes)';
$lang['statustext'] = 'Status/Action';
$lang['uptodate'] = 'Installed';
$lang['newerversion'] = 'Newer version installed';
$lang['onlynewesttext'] ='Show only newest version';
$lang['upgrade'] = 'Upgrade';
$lang['error_nosoapconnect'] = 'Could not connect to SOAP server';
$lang['error_soaperror'] = 'SOAP Problem';
$lang['error_norepositoryurl'] = 'The URL for the Module Repository has not been specified';
$lang['ModuleManager'] = 'Module Manager';
$lang['postinstall'] = 'Post Install Message.';
$lang['postuninstall'] = 'Module Manager has been uninstalled.  Users will no longer have the ability to install modules from remote repositories.  However, local installation is still possible.';
$lang['really_uninstall'] = 'Are you sure you want to uninstall? You will be missing alot of nice functionality.';
$lang['uninstalled'] = 'Module Uninstalled.';
$lang['installed'] = 'Module version %s installed.';
$lang['upgraded'] = 'Module upgraded to version %s.';
$lang['moddescription'] = 'A client for the ModuleRepository, this module allows previewing, and installing modules from remote sites without the need for ftping, or unzipping archives.  Module XML files are downloaded using SOAP, integrity verified, and then expanded automatically.';

$lang['error'] = 'Error!';
$land['admin_title'] = 'Module Manager Admin Panel';
$lang['admindescription'] = 'A tool for retrieving and installing modules from remote servers.';
$lang['accessdenied'] = 'Access Denied. Please check your permissions.';
$lang['postinstall'] = 'Module Manager has been successfully installed.';
$lang['changelog'] = '
<ul>
<li>Version 1.0. 10 January 2006. Initial Release.</li>
<li>Version 1.1. July, 2006. Released with the 1.0- beta</li>
<li>Version 1.1.1 August, 2006.  Require 1.0.1 of nuSOAP</li>
<li>Version 1.1.2 September, 2006.  Fixed a mistake that resulted in upgrade not not working at all</li>
<li>Version 1.1.3 September, 2006.
  <ul>
  <li>Bumped minimum CMS Version to 1.0</li>
  <li>Now use 1 request to get the complete list of modules from the repository</li>
  <li>Added some missing lang strings</li>
  <li>Added the ability to reset the local cache of repository information</li>
  <li>Added the ability to restore the repository url to factory defaults</li>
  </ul>
</li>
<li>Version 1.1.4 February, 2007.  Now handles the safe mode check, and disables upgrading or installing modules if the permissions are wrong.</li>
<li>Version 1.1.5 September, 2007. New preference to make only latest module version show. Added nice message after saving preferences</li>
</li>
<li>Version 1.1.6 May, 2008. Now show if available modules are incompatible with the current CMS_VERSION.</li>
</li>
<li>Version 1.2 June, 2008.<br/>
This version should reduce the memory requirements of this module, and trade it off for performance on the server, and mroe requests to the server.
   <ul>
    <li>Bumped Minimum CMS Version to 1.3</li>
    <li>Bumped Minimum repository version to 1.1</li>
    <li>Get rid of all of the session stuff</li>
    <li>Add support for requesting modules beginning with a prefix (usually a single letter)</li>
    <li>Add support for requestion only the newest versions of the modules</li>
   </ul>
</li>
<li>Version 1.2.1 August, 2008.<br/>
Added a warning message to the top of the admin display.
</li>
<li>Version 1.3 May, 2009.<br/>
Added dependency checking.
</li>
</ul>';
$lang['help'] = '<h3>What Does This Do?</h3>
<p>A client for the ModuleRepository, this module allows previewing, and installing modules from remote sites without the need for ftping, or unzipping archives.  Module XML files are downloaded using SOAP, integrity verified, and then expanded automatically.</p>
<h3>How Do I Use It</h3>
<p>In order to use this module, you will need the \'Modify Modules\' permission, and you will also need the complete, and full URL to a \'Module Repository\' installation.  You can specify this url in the \'Extensions\' --&gt; \'Module Manager\' --&gt; \'Preferences\' page.</p><br/>
<p>You can find the interface for this module under the \'Extensions\' menu.  When you select this module, the \'Module Repository\' installation will automatically be queried for a list of it\'s available xml modules.  This list will be cross referenced with the list of currently installed modules, and a summary page displayed.  From here, you can view the descriptive information, the help, and the about information for a module without physically installing it.  You can also choose to upgrade or install modules.</p>
<h3>Support</h3>
<p>As per the GPL, this software is provided as-is. Please read the text of the license for the full disclaimer.</p>
<h3>Copyright and License</h3>
<p>Copyright &copy; 2006, calguy1000 <a href="mailto:calguy1000@hotmail.com">&lt;calguy1000@hotmail.com&gt;</a>. All Rights Are Reserved.</p>
<p>This module has been released under the <a href="http://www.gnu.org/licenses/licenses.html#GPL">GNU Public License</a>. You must agree to this license before using the module.</p>';
?>
