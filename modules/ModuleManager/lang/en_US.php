<?php
$lang['error_search'] = 'Search Error';
$lang['prompt_disable_caching'] = 'Disable caching of requests from the server';
$lang['info_disable_caching'] = '<strong>Not Recommended</strong>.  For performance reasons, ModuleManager will cache for (by default one hour) much of the information retrieved from the remote server';
$lang['operation_results'] = 'Operation Results';
$lang['error_noresults'] = 'We expected some results to be available from queued operations, but none were found. Please try to reproduce this experience, and provide sufficient information to support personnel for diagnoses.';
$lang['versionsformodule'] = 'Available versions of the module %s';
$lang['yourversion'] = 'Your Version';
$lang['latestdepends'] = 'Always install the newest modules';
$lang['info_latestdepends'] = 'When installing a module with dependencies, this option will make sure that the latest version of the dependency will be installed';
$lang['error_internal'] = 'Internal Error... Please report this to your system administrator';
$lang['error_downloadxml'] = 'A problem occurred downloading the XML FILE: %s';
$lang['error_request_problem'] = 'A problem occurred communicating with the module server';
$lang['error_searchterm'] = 'You have entered an invalid search term.  The term must consist of ascii characters and be three or more characters long';
$lang['search_noresults'] = 'Search succeeded but no results matched the expresssion';
$lang['advancedsearch_help'] = 'Specify words to include or exclude from the search using a + or -, surround exact phrases with quotes.  i.e:  +red -apple +"some text"';
$lang['search_results'] = 'Search Results';
$lang['prompt_advancedsearch'] = 'Advanced Search';
$lang['search_input'] = 'Search Input';
$lang['searchterm'] = 'Search Term';
$lang['search'] = 'Search';
$lang['error_searchterm']='Please specify something valid to search for';
$lang['available_updates'] = 'Modules Available for Update; Before upgrading, please read the releasenotes in the Forge and create a backup of the website.';
$lang['all_modules_up_to_date'] = 'There are no newer modules available in the repository';
$lang['error_module_object'] = 'Error: could not get an instance of the %s module';
$lang['error_nomatchingmodules'] = 'Error: could not find any matching modules in the repository';
$lang['error_nomodules'] = 'Error: could not retrieve list of installed modules';
$lang['upgrade_available'] = 'Newer version available (%s), you have (%s)';
$lang['newversions'] = 'Available Upgrades';
$lang['notice_depends'] = '%s has unresolved dependencies. In order to install this module the following actions must occur';
$lang['install_submit'] = 'Install';
$lang['depend_upgrade'] = 'Module %s will be upgraded to version %s.';
$lang['depend_install'] = 'Module %s (version %s) will be installed.';
$lang['depend_activate'] = 'Module %s will be activated.';
$lang['action_activated'] = 'Module %s has been activated.';
$lang['action_installed'] = 'Module %s has been installed with the following message(s): %s';
$lang['action_upgraded'] = 'Module %s has been upgraded';
$lang['title_installation_complete'] = 'Installation Process Complete!';
$lang['install_with_deps'] = 'Evaluate all Dependencies and Install';
$lang['msg_nodependencies'] = 'This file has not listed any dependencies';
$lang['error_upgrade'] = 'Upgrade of module %s failed!';
$lang['error_skipping'] = 'Skipping install/upgrade of %s due to errors in setting up dependencies. Please see message above, and try again.';
$lang['dependstxt'] = 'Dependencies';
$lang['use_at_your_own_risk'] = 'Use at Your Own Risk';
$lang['compatibility_disclaimer'] = 'The modules displayed here are contributed by both the CMS Developers, and independent third parties.  We make no guarantees that the modules available here are functional, tested, or compatible with your system.  You are encouraged to read the information found in the help and about links for each module before attempting the installation.';
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
$lang['error_unsatisfiable_dependency'] = 'Cannot find the required module "%s" (version %s or later) in the repository. It is directly required by %s; this could indicate a problem with the version of this module in the repository. Please contact the module\'s author. Aborting.';
$lang['error_checksum'] = 'Checksum error.  This probably indicates a corrupt file, either when it was uploaded to the repository, or a problem in transit down to your machine. (expected %s and got %s)';
$lang['cantdownload'] = 'Cannot Download';
$lang['download'] = 'Download &amp; Install';
$lang['error_moduleinstallfailed'] = 'Module installation failed';
$lang['error_connectnomodules'] = 'Although a connection was successfully made to the specified module repository.  It appears that this repository is not yet sharing any modules';
$lang['submit'] = 'Submit';
$lang['text_repository_url'] = 'The URL should be in the form http://www.mycmsmssite.com/ModuleRepository/request/v2 (assuming pretty urls are enabled on the repository server)<br />Note: opening this link in your webbrowser will return an Error404!';
$lang['prompt_repository_url'] = 'ModuleRepository URL';
$lang['title_installation'] = 'Installation';
$lang['availmodules'] = 'Available Modules'; 
$lang['preferences'] = 'Preferences';
$lang['preferencessaved'] = 'Preferences saved';
$lang['repositorycount'] = 'Modules found in the repository';
$lang['instcount'] = 'Modules currently installed';
$lang['availablemodules'] = 'The current status of modules available from the current repository';
$lang['time_warning'] = 'Two or more actions need to be performed. Be aware that the install could take a few minutes. Please be patient!';
$lang['helptxt'] = 'Help';
$lang['abouttxt'] = 'About';
$lang['xmltext'] = 'XML File';
$lang['nametext'] = 'Module Name';
$lang['mod_name_ver'] = '%s version %s';
$lang['unknown'] = 'Unknown';
$lang['vertext'] = 'Version';
$lang['sizetext'] = 'Size (Kilobytes)';
$lang['statustext'] = 'Status/Action';
$lang['uptodate'] = 'Installed';
$lang['install'] = 'install';
$lang['newerversion'] = 'Newer version installed';
$lang['onlynewesttext'] ='Show only newest version';
$lang['upgrade'] = 'Upgrade';
$lang['error_norepositoryurl'] = 'The URL for the Module Repository has not been specified';
$lang['friendlyname'] = 'Module Manager';
$lang['postinstall'] = 'Post Install Message.';
$lang['postuninstall'] = 'Module Manager has been uninstalled.  Users will no longer have the ability to install modules from remote repositories.  However, local installation is still possible.';
$lang['really_uninstall'] = 'Are you sure you want to uninstall? You will be missing a lot of nice functionality.';
$lang['uninstalled'] = 'Module Uninstalled.';
$lang['installed'] = 'Module version %s installed.';
$lang['upgraded'] = 'Module upgraded to version %s.';
$lang['moddescription'] = 'A client for the ModuleRepository, this module allows previewing, and installing modules from remote sites without the need for ftping, or unzipping archives.  Module XML files are downloaded using a REST API, integrity verified, and then expanded automatically.';
$lang['back_to_module_manager'] = '&#171; Return to Module Manager';

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
This version should reduce the memory requirements of this module, and trade it off for performance on the server, and more requests to the server.
   <ul>
    <li>Bumped Minimum CMS Version to 1.3</li>
    <li>Bumped Minimum repository version to 1.1</li>
    <li>Get rid of all of the session stuff</li>
    <li>Add support for requesting modules beginning with a prefix (usually a single letter)</li>
    <li>Add support for requesting only the newest versions of the modules</li>
   </ul>
</li>
<li>Version 1.2.1 August, 2008.<br/>
Added a warning message to the top of the admin display.
</li>
<li>Version 1.3 May, 2009.<br/>
Added dependency checking.
</li>
<li>Version 1.3.3 March, 2010.<br/>
<ul>
  <li>PHP 5.x improvements (specifically remove warnings for PHP 5.3)</li>
  <li>Minor bug fixes.</li>
</ul>
</li>
<li>Version 1.4 June, 2010.<br/>
<ul>
  <li>Implemented automatic dependency calculation, and one-click installation.</li>
  <li>Assorted usability improvements.</li>
  <li>Minor bug fixes.</li>
</ul>
<li>Version 1.5  - July, 2011
<ul>
  <li>Changes to REST API.. No longer uses nuSOAP.</li>
  <li>Many optimizations to downloading and installing modules.</li>
  <li>Can now install older versions of modules easily.</li>
  <li>Handle automatic upgrading of ddependencies.</li>
</ul>
</li>
</ul>';
$lang['help'] = '<h3>What Does This Do?</h3>
<p>A client for the ModuleRepository, this module allows previewing, and installing modules from remote sites without the need for ftping, or unzipping archives.  Module XML files are downloaded using REST, integrity verified, and then expanded automatically.</p>
<h3>How Do I Use It</h3>
<p>In order to use this module, you will need the \'Modify Modules\' permission, and you will also need the complete, and full URL to a \'Module Repository\' installation.  You can specify this url in the \'Extensions\' --&gt; \'Module Manager\' --&gt; \'Preferences\' page.</p><br/>
<p>You can find the interface for this module under the \'Extensions\' menu.  When you select this module, the \'Module Repository\' installation will automatically be queried for a list of it\'s available xml modules.  This list will be cross referenced with the list of currently installed modules, and a summary page displayed.  From here, you can view the descriptive information, the help, and the about information for a module without physically installing it.  You can also choose to upgrade or install modules.</p>
<h3>Support</h3>
<p>As per the GPL, this software is provided as-is. Please read the text of the license for the full disclaimer.</p>
<h3>Copyright and License</h3>
<p>Copyright &copy; 2006, calguy1000 <a href="mailto:calguy1000@hotmail.com">&lt;calguy1000@hotmail.com&gt;</a>. All Rights Are Reserved.</p>
<p>This module has been released under the <a href="http://www.gnu.org/licenses/licenses.html#GPL">GNU Public License</a>. You must agree to this license before using the module.</p>';
?>
