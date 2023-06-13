<?php
// A
$lang['about_title'] = 'About the %s module';
$lang['admin_title'] = 'Module Manager Admin Panel';
$lang['abouttxt'] = 'About';
$lang['accessdenied'] = 'Access Denied. Please check your permissions';
$lang['action_activated'] = 'Module %s has been activated';
$lang['action_installed'] = 'Module %s has been installed with the following message(s):<br /><br />%s';
$lang['action_upgraded'] = 'Module %s has been upgraded';
$lang['action'] = 'Action';
$lang['active'] = 'Active';
$lang['admindescription'] = 'Modules extend CMS Made Simple&trade; to provide all kinds of custom functionality';
$lang['advancedsearch_help'] = 'Specify words to include or exclude from the search using a + or -, surround exact phrases with quotes.  i.e:  +red -apple +"some text"';
$lang['allowuninstall'] = 'Allow Module Manager to be uninstalled? Be careful, the uninstallation is irreversible!';
$lang['all_modules_up_to_date'] = 'There are no newer modules available in the repository';
$lang['availablemodules'] = 'The current status of modules available from the current repository';
$lang['available_updates'] = 'One or more modules are available for upgrade; Before upgrading, please read the about information for the release and make sure you have a current backup of your website.';
$lang['availmodules'] = 'Available Modules';

// B
$lang['back'] = 'Back';
$lang['back_to_module_manager'] = 'Return to Module Manager';

// C
$lang['cancel'] = 'Cancel';
$lang['cantdownload'] = 'Cannot Download';
$lang['cantremove'] = 'Cannot Remove';
$lang['cantuninstall'] = 'Cannot Uninstall';
$lang['changeperms'] = 'Change Permissions';
$lang['confirm_action'] = 'Yes, I am sure';
$lang['confirm_chmod'] = 'Continuing will attempt to change the permissions on this modules files.  Are you sure you want to continue?';
$lang['confirm_resetcache'] = 'Are you sure you want to clear the local cache?';
$lang['confirm_reseturl'] = 'Are you sure you want to reset the repository URL?';
$lang['confirm_settings'] = 'Are you sure you want to save these settings?';
$lang['confirm_remove'] = 'Are you sure you want to remove this modules files from the file system';
$lang['confirm_upgrade'] = 'Are you sure you want to upgrade this module?';
$lang['compatibility_disclaimer'] = 'The modules displayed here are contributed by both the CMSMS Developers and independent third parties.  We make no guarantees that the modules available here are functional, tested, or compatible with your system.  You are encouraged to read the information found in the help and about links for each module before attempting the installation.';

// D
$lang['db_newer'] = 'Database Version Newer';
$lang['dependstxt'] = 'Dependencies';
$lang['depend_activate'] = 'Module %s will be activated.';
$lang['depend_customization'] = '<strong>Warning:</strong> This module has customized templates and/or language strings. Continuing may generate errors or you may have difficulty with new functionality.';
$lang['depend_install'] = 'Module %s (version %s) will be installed.';
$lang['depend_upgrade'] = 'Module %s will be upgraded to version %s.';
$lang['depends_upon'] = 'Depends on';
$lang['display_in_english'] = 'Display in English';
$lang['display_in_mylanguage'] = 'Display in NAME LANGUAGE HERE';
$lang['download'] = 'Download &amp; Install';
$lang['downloads'] = 'Downloads';

// E
$lang['entersearchterm'] = 'Enter search term';
$lang['error'] = 'Error!';
$lang['err_nothingtodo'] = 'Nothing to do';
$lang['error_active_failed'] = 'The operation to toggle the active state of a module failed';
$lang['error_checksum'] = 'Checksum error.  This probably indicates a corrupt file, either when it was uploaded to the repository, or a problem in transit down to your machine. (expected %s and got %s)';
$lang['error_chmodfailed'] = 'One or more problems encountered when changing permissions of files';
$lang['error_connectnomodules'] = 'It appears that no modules match your search criteria.';
$lang['error_downloadxml'] = 'A problem occurred downloading the XML file: %s';
$lang['error_dependencynotfound'] = 'One or more dependencies could not be found in the repository';
$lang['error_dependencynotfound2'] = 'The module %s (%s) could not be found in the repository, but it is a dependency.<br/>This module may no longer be available in the module repository, or may require manual installation.<br />Please ensure that it is installed correctly, then retry this operation.';
$lang['error_fileupload'] = 'A problem occurred uploading the file';
$lang['error_getmodule'] = 'A problem occurred instantiating %s';
$lang['error_internal'] = 'Internal Error... Please report this to your system administrator';
$lang['error_invaliduploadtype'] = 'The file uploaded is not valid for this field';
$lang['error_minimumrepository'] = 'The repository version is not compatible with your Module Manager version';
$lang['error_missingparams'] = 'A required parameter was missing or invalid';
$lang['error_missingmoduleinfo'] = 'Problem retrieving module information for module %s';
$lang['error_moduleexport'] = 'Module export failed';
$lang['error_moduleinstallfailed'] = 'Module installation failed';
$lang['error_moduleremovefailed'] = 'Failed to remove module';
$lang['error_moduleuninstallfailed'] = 'Module uninstallation failed';
$lang['error_moduleupgradefailed'] = 'Module upgrade failed';
$lang['error_module_object'] = 'Error: could not get an instance of the %s module';
$lang['error_nodata'] = 'No data retrieved';
$lang['error_nofilename'] = 'No filename parameter supplied';
$lang['error_nofilesize'] = 'No filesize parameter supplied';
$lang['error_nofileuploaded'] = 'Please upload a module XML file';
$lang['error_nomatchingmodules'] = 'Error: could not find any matching modules in the repository';
$lang['error_nomodules'] = 'Error: could not retrieve list of installed modules';
$lang['error_norepositoryurl'] = 'The URL for the Module Repository has not been specified';
$lang['error_noresults'] = 'We expected some results to be available from queued operations, but none were found. Please try to reproduce this experience, and provide sufficient information to support personnel for diagnoses.';
$lang['error_notconfirmed'] = 'The operation was not confirmed';
$lang['error_nothingtodo'] = 'Oops.  You requested an action, but we didn\'t calculate anything to do.  This probably means some kind of bug.';
$lang['error_notxmlfile'] = 'The file uploaded was not an XML file';
$lang['error_permissions'] = '<strong><em>WARNING:</em></strong> Insufficient directory permissions to install modules.  You may also be experiencing problems with PHP Safe mode.  Please ensure that safe mode is disabled, and that file system permissions are sufficient.';
$lang['error_request_problem'] = 'A problem occurred communicating with the module server';
$lang['error_search'] = 'Search Error';
$lang['error_searchterm'] = 'You have entered an invalid search term.  The term must consist of ASCII characters and be three or more characters long';
$lang['error_skipping'] = 'Skipping install/upgrade of %s due to errors in setting up dependencies. Please see message above, and try again.';
$lang['error_unsatisfiable_dependency'] = 'Cannot find the required module "%s" (version %s or later) in the repository. It is directly required by %s; this could indicate a problem with the version of this module in the repository. Please contact the module\'s author. Aborting.';
$lang['error_upgrade'] = 'Upgrade of module %s failed!';
$lang['export'] = 'Export';

// F
$lang['friendlyname'] = 'Module Manager';

// G
$lang['general_notice'] = 'The versions displayed here represent the latest XML files uploaded to the CMSMS module repository.  They may or may not represent the latest available versions. Additionally, the module repository may only contain modules released within the last few months';

// H
$lang['help'] = '<h3>What Does This Do?</h3>
<p>A client for the CMS Made Simple Module Repository. This module allows previewing and installing modules from the CMSMS Forge without the need for ftp-ing, or unzipping archives.  Module XML files are downloaded using REST, integrity verified, and then expanded automatically.</p>
<h3>How Do I Use It</h3>
<p>In order to use this module you will need the \'Modify Modules\' permission.</p>
<br />
<p>You can find the interface for this module under the \'Site Admin\' menu.  When you select this module, the \'Module Repository\' installation will automatically be queried for a list of it\'s available XML modules.  This list will be cross referenced with the list of currently installed modules, and a summary page displayed.  From here, you can view the descriptive information, the Help, and the About information for a module without physically installing it.  You can also choose to upgrade or install modules.</p>
<h3>Support</h3>
<p>As per the GPL, this software is provided as-is. Please read the text of the license for the full disclaimer.</p>
<h3>Copyright and License</h3>
<p>Copyright &copy; 2006, calguy1000 <a href="mailto:calguy1000@hotmail.com">&lt;calguy1000@hotmail.com&gt;</a>. All Rights Are Reserved.</p>
<p>This module has been released under the <a href="http://www.gnu.org/licenses/licenses.html#GPL">GNU Public License</a>. You must agree to this license before using the module.</p>';
$lang['helptxt'] = 'Help';
$lang['help_allowuninstall'] = 'If enabled, then this module can be uninstalled.  This option is provided to prevent the accidental removal of this module which would result in an unrecoverable error';
$lang['help_disable_caching'] = 'TODO';
$lang['help_dl_chunksize'] = 'This parameter specifies the size <em>(in kilobytes)</em> of each chunk of data that will be downloaded from the repository when requesting a module.';
$lang['help_latestdepends'] = 'When installing a module with dependencies, this will ensure that the newest version of a dependent module is installed';
$lang['help_mm_importxml'] = 'This form allows importing a module XML file that you received from another user, or downloaded from the <a class="external" href="http://dev.cmsmadesimple.org" target="_blank">CMSMS Forge</a>';

// I
$lang['importxml'] = 'Import Module';
$lang['incompatible'] = 'Incompatible';
$lang['info_searchtab'] = 'This tab displays a list of installed modules for which there is a newer version available';
$lang['install'] = 'Install';
$lang['installed'] = 'Installed';
$lang['install_module'] = 'Install Module';
$lang['install_procede'] = 'Proceed';
$lang['install_submit'] = 'Install';
$lang['install_with_deps'] = 'Evaluate all Dependencies and Install';
$lang['instcount'] = 'Modules currently installed';

// L
$lang['latestdepends'] = 'Always install the newest dependency module';
$lang['lbl_module'] = 'Module';
$lang['lbl_version'] = 'Version';

// M
$lang['minversion'] = 'Minimum Version';
$lang['missingdeps'] = 'Missing dependencies';
$lang['mod_name_ver'] = '%s version %s';
$lang['moddescription'] = 'A client for the CMS Made Simple &trade; Module Repository. This module allows previewing and installing modules from the CMSMS Forge without the need for ftp-ing, or unzipping archives.  Module XML files are downloaded using REST, integrity verified, and then expanded automatically.';
$lang['msg_batch_completed'] = '%d operations completed';
$lang['msg_cachecleared'] = 'Cache cleared';
$lang['msg_cancelled'] = 'Operation canceled';
$lang['msg_module_activated'] = 'Module %s activated';
$lang['msg_module_chmod'] = 'Permissions changed';
$lang['msg_module_deactivated'] = 'Module %s deactivated';
$lang['msg_module_exported'] = 'Module %s exported to XML';
$lang['msg_module_imported'] = 'Module imported';
$lang['msg_module_installed'] = 'Module %s successfully installed';
$lang['msg_module_removed'] = 'Module files permanently removed';
$lang['msg_module_uninstall'] = '<strong>Warning:</strong> Uninstalling a module typically deletes all of the data owned by that module, along with all preferences and templates.  Please use extreme caution.';
$lang['msg_module_uninstalled'] = 'Module %s successfully uninstalled. Templates and data associated with this module has been deleted';
$lang['msg_module_upgraded'] = 'Module %s successfully upgraded';
$lang['msg_nodependencies'] = 'This file has not listed any dependencies';
$lang['msg_prefssaved'] = 'Preferences Updated';
$lang['msg_urlreset'] = 'The ModuleRepository URL has been reset to the default value';

// N
$lang['nametext'] = 'Module Name';
$lang['need_upgrade'] = 'This module is awaiting upgrade';
$lang['newerversion'] = 'Newer version installed';
$lang['newer_available'] = 'New version available';
$lang['notavailable'] = 'This module cannot be loaded. Possibly due to dependencies';
$lang['notcompatible'] = 'This module is not compatible with this version of CMSMS';
$lang['notice'] = 'Notice';
$lang['notinstalled'] = 'Not installed';

// O
$lang['onlynewesttext'] = 'Show only newest version';
$lang['operation_results'] = 'Operation Results';

// P
$lang['postinstall'] = 'Module Manager has been successfully installed.';
$lang['postuninstall'] = 'Module Manager has been uninstalled.  Users will no longer have the ability to install modules from the remote repository.  However, local installation is still possible.';
$lang['preferences'] = 'Preferences';
$lang['preferencessaved'] = 'Preferences saved';
$lang['prompt_advancedsearch'] = 'Advanced Search';
$lang['prompt_disable_caching'] = 'Disable caching of requests from the server';
$lang['prompt_dl_chunksize'] = 'Download Chunk Size (Kb)';
$lang['prompt_otheroptions'] = 'Other Options';
$lang['prompt_repository_url'] = 'ModuleRepository URL';
$lang['prompt_settings'] = 'Settings';

// R
$lang['really_uninstall'] = 'Are you sure you want to uninstall? You will be missing a lot of nice functionality.';
$lang['releasedate'] = 'Date';
$lang['remove'] = 'Remove';
$lang['repositorycount'] = 'Modules found in the repository';
$lang['reset'] = 'Reset';
$lang['reset_cache'] = 'Reset Cache';

// S
$lang['search'] = 'Search';
$lang['searchterm'] = 'Search Term';
$lang['search_input'] = 'Search Input';
$lang['search_noresults'] = 'Search succeeded but no results matched the expression';
$lang['search_results'] = 'Search Results';
$lang['sizetext'] = 'Size (Kilobytes)';
$lang['status'] = 'Status';
$lang['status_db_newer'] = 'The version number stored in the database is greater than the one in the module.';
$lang['status_need_upgrade'] = 'The upgrade routine needs to be run on this module';
$lang['status_newer_available'] = 'A newer version of this module is available in the repository';
$lang['statustext'] = 'Status/Action';
$lang['status_installed'] = 'This module is currently installed and available for use.';
$lang['submit'] = 'Submit';
$lang['success'] = 'Success';

// T
$lang['tab_newversions'] = 'Upgrades Available';
$lang['time_warning'] = 'Installing modules is a data and memory intensive operation. Depending upon the network bandwidth, server load, and installation tasks that need to be performed this could take several minutes.  Also, installing modules may cause problems with a working installation.  It is recommended that you have a verified backup of your site before proceeding.';
$lang['title_advancedsearch'] = 'Enable advanced search functionality';
$lang['title_cantremove'] = 'The file system permissions on this module directory do not permit deleting the files';
$lang['title_cantuninstall'] = 'Other installed modules depend on this module, therefore you cannot uninstall it at this time.';
$lang['title_chmod'] = 'Attempt to recursively change permissions on this directory';
$lang['title_depends_upon'] = 'This module depends on on other modules for its functionality.';
$lang['title_deprecated'] = 'This module is deprecated (the development is stopped and there will be no new releases).';
$lang['title_has_dependants'] = 'This module is required because it used by other installed modules, and therefore cannot be uninstalled';
$lang['title_install'] = 'Install this module for use in your website';
$lang['title_installation_complete'] = 'Installation Process Complete!';
$lang['title_letter'] = 'Show modules whose name starts with %s';
$lang['title_missingdeps'] = 'This module cannot be installed or upgraded due to missing dependencies';
$lang['title_missingdeps2'] = 'Missing Dependencies';
$lang['title_mm_importxml'] = 'Import module from XML';
$lang['title_moduleabout'] = 'View the author and changelog information for this module';
$lang['title_moduleactive'] = 'Toggle the active state of the module. Inactive modules are not loaded, and cannot be called, however data remains intact';
$lang['title_moduleaction'] = 'This column displays the actions available for each module';
$lang['title_moduledepends'] = 'View the dependencies for this module';
$lang['title_moduledownloads'] = 'This column displays the approximate number of downloads for each release of the module';
$lang['title_moduledownloads2'] = 'This column displays the approximate number of downloads for the newest version of the module';
$lang['title_moduleexport'] = 'Export this module to XML for sharing';
$lang['title_modulehelp'] = 'View basic usage instructions for this module';
$lang['title_moduleinstallupgrade'] = 'Install or Upgrade this module';
$lang['title_modulelastreleasedate'] = 'This column displays the date of the last release for the module';
$lang['title_modulelastversion'] = 'This column displays the version number of the last release for the module';
$lang['title_moduletotaldownloads'] = 'This column displays the approximate total downloads for all released versions of the module';
$lang['title_modulereleaseabout'] = 'View the author and changelog information for this release';
$lang['title_modulereleasedate'] = 'This column displays the release date of the module';
$lang['title_modulereleasedepends'] = 'View the dependencies for this release';
$lang['title_modulereleasehelp'] = 'View the documentation supplied with this release';
$lang['title_modulesize2'] = 'This column displays the size of the module XML file to be downloaded (in kilobytes)';
$lang['title_modulestatus'] = 'This column displays the status or actions available for a particular module';
$lang['title_moduleversion'] = 'This column displays the module version';
$lang['title_new'] = 'This module was released/updated within the last month';
$lang['title_newmoduleversion'] = 'This column displays the version number of the most recent release of the module';
$lang['title_notavailable'] = 'This is not ready for use at this time';
$lang['title_notcompatible'] = 'This module has not passed compatibility tests';
$lang['title_notinstalled'] = 'This module exists in the modules subdirectory but has not been installed for use';
$lang['title_remove'] = 'Remove this modules files from the module directory';
$lang['title_searchterm'] = 'Enter a natural language search term.  If advanced mode is enabled, then boolean operations similar to Google can be used';
$lang['title_stale'] = 'This module is marked &quot;Stale&quot; (Last release over two years ago) This means it may work fine, but it has not had any recent development. Use your own discretion when using this module!';
$lang['title_star'] = 'This icon indicates that a newer version of this module is available in the repository';
$lang['title_system'] = 'This icon identifies a CMS Made Simple&trade; system module (module distributed with the CMSMS core)';
$lang['title_uninstall_module'] = 'Uninstall Module';
$lang['title_uninstall'] = 'Uninstall this module. This action may destroy data and templates associated with the module';
$lang['title_upgrade'] = 'Upgrade this module';
$lang['title_warning'] = 'This module was released some time ago. Use caution!';
$lang['title_yourmoduledate'] = 'This column displays the date of the latest release for this module';
$lang['title_yourmoduleversion'] = 'This column displays the version number of module that is currently installed';
$lang['toggle_active'] = 'Set this module as active and usable';
$lang['toggle_inactive'] = 'Set this module as inactive and unused. No module data will be destroyed';

// U
$lang['uninstall'] = 'Uninstall';
$lang['uninstalled'] = 'Module Uninstalled';
$lang['unknown'] = 'Unknown';
$lang['upgrade'] = 'Upgrade';
$lang['upgraded'] = 'Module upgraded to version %s';
$lang['upgrade_available'] = 'Newer version available (%s), you have (%s)';
$lang['upgrade_module'] = 'Upgrade module';
$lang['uploadfile'] = 'Upload XML File';
$lang['uptodate'] = 'Installed';
$lang['use_at_your_own_risk'] = 'Use at Your Own Risk';

// V
$lang['versionsformodule'] = 'Available versions of the module %s';
$lang['vertext'] = 'Version';

// W
$lang['warning'] = 'Warning';
$lang['warn_dependencies'] = 'The module you selected to install or upgrade depends on one or more additional modules that must also be installed or upgraded.';
$lang['warn_modulecustom'] = 'The following modules have customizations in the &lt;root&gt;/assets/module_custom directory of your installation.  These customizations may or may not cause errors after the upgrade.  You may need to remove or revise these customizations to restore proper functionality.  Proceed with caution.';
// X
$lang['xmltext'] = 'XML File';

// Y
$lang['yourversion'] = 'Your Version';

?>
