<?php
# A
$lang['action_freshen'] = 'Freshening / Repairing a CMSMS %s installation';
$lang['action_install'] = 'Creating a new CMSMS %s website';
$lang['action_upgrade'] = 'Upgrading a CMSMS Website to version %s';
$lang['advanced_mode'] = 'Enable advanced mode';
$lang['apptitle'] = 'Installation and upgrade assistant';
$lang['assets_dir_exists'] = 'Assets directory exists';
$lang['available_languages'] = 'Available languages';

# B
$lang['build_date'] = 'Build Date';

# C
$lang['changelog_uc'] = 'CHANGELOG';
$lang['cleaning_files'] = 'Cleaning files that are no longer applicable to the release';
$lang['config_writable'] = 'Check for writeable config file';
$lang['confirm_freshen'] = 'Are you sure you want to freshen (repair) the existing installation of CMSMS? Use with extreme caution!';
$lang['confirm_upgrade'] = 'Are you sure you want to begin the upgrade process';
$lang['curl_extension'] = 'Checking for the Curl extension';
$lang['create_assets_structure'] = 'Creating a location for file resources';

# D
$lang['database_support'] = 'Check for compatible database drivers';
$lang['desc_wizard_step1'] = 'Start the installation or upgrade process';
$lang['desc_wizard_step2'] = 'Analyze destination directory to find existing software';
$lang['desc_wizard_step3'] = 'Check to make sure everything is OK to install the CMSMS core';
$lang['desc_wizard_step4'] = 'For new installs, and freshen operation, enter basic configuration info';
$lang['desc_wizard_step5'] = 'For new installs, enter Admin account info';
$lang['desc_wizard_step6'] = 'For new installs enter some basic site details';
$lang['desc_wizard_step7'] = 'Extract files';
$lang['desc_wizard_step8'] = 'Create or update the database schema, set initial events, permissions, user accounts, templates, stylesheets and content';
$lang['desc_wizard_step9'] = 'Install and/or Upgrade modules as necessary, write the config file, and clean up.';
$lang['destination_directory'] = 'Destination Directory';
$lang['dest_writable'] = 'Write permission in destination directory';
$lang['disable_functions'] = 'Checking disabled functions';
$lang['done'] = 'Done';

# E
$lang['email_accountinfo_message'] = <<<EOT
Your installation of CMS Made Simple is complete.

This email contains sensitive information and should be stored in a secure location.

Here are the details of your installation.
username: %s
password: %s
install directory: %s
root url: %s

EOT;
$lang['email_accountinfo_message_exp'] = <<<EOT
Your installation of CMS Made Simple is complete.

This email contains sensitive information and should be stored in a secure location.

Here are the details of your installation.
username: %s
password: %s
install directory: %s

EOT;
$lang['email_accountinfo_subject'] = 'CMS Made Simple Installation Successful';
$lang['emailaccountinfo'] = 'Email the account information';
$lang['emailaddr'] = 'Email Address';
$lang['error_adminacct_emailaddr'] = 'The email address you specified is invalid';
$lang['error_adminacct_emailaddrrequired'] = 'You have selected to email the account information, but have not entered a valid email address';
$lang['error_adminacct_password'] = 'The password you specified is invalid (must be at least six characters long)';
$lang['error_adminacct_repeatpw'] = 'The passwords you entered did not match.';
$lang['error_adminacct_username'] = 'The username you specified is invalid. Please try again';
$lang['error_admindirrenamed'] = 'It appears that, for security reasons, you may have renamed your CMSMS Admin directory. You must reverse <a href="https://docs.cmsmadesimple.org/general-information/securing-cmsms#renaming-admin-folder" target="_blank" class="external">this process</a> in order to proceed!<br/><br/>Once you have reverted the admin directory name to its original location, please reload this page.';
$lang['error_backupconfig'] = 'We could not properly backup the config file';
$lang['error_checksum'] = 'Extracted file checksum does not match original';
$lang['error_cmstablesexist'] = 'It appears that there is already a CMS installation on this database. Please enter different database information. If you would like to use a different table prefix you may need to restart the installation process and enable advanced mode.';
$lang['error_createtable'] = 'Problem creating database table... perhaps this is a permissions issue';
$lang['error_dbconnect'] = 'We could not connect to the database. Please double check the credentials you have supplied';
$lang['error_dirnotvalid'] = 'The directory %s does not exist (or is not writeable)';
$lang['error_droptable'] = 'Problem dropping database table... perhaps this is a permissions issue';
$lang['error_filenotwritable'] = 'The file %s could not be overwritten (permissions problem)';
$lang['error_internal'] = 'Sorry, something has gone wrong... (internal error) (%s)';
$lang['error_invalid_directory'] = 'It appears that the directory you have selected to install in is a working directory for the installer itself';
$lang['error_invalidconfig'] = 'Error in the config file, or config file missing';
$lang['error_invaliddbpassword'] = 'Database password contains invalid characters that cannot be safely saved.';
$lang['error_invalidkey'] = 'Invalid member variable or key %s for class %s';
$lang['error_invalidparam'] = 'Invalid parameter or value for parameter: %s';
$lang['error_invalidtimezone'] = 'The timezone specified is invalid';
$lang['error_invalidqueryvar'] = 'The query variable entered contains invalid characters.  Please use only alphanumerics and underscore.';
$lang['error_missingconfigvar'] = 'The key &quot;%s&quot; is either missing or invalid in the config.ini file';
$lang['error_noarchive'] = 'Problem finding archive file... please restart';
$lang['error_nlsnotfound'] = 'Problem finding NLS files in archive';
$lang['error_nodatabases'] = 'No compatible database extensions could be found';
$lang['error_nodbhost'] = 'Please enter a valid hostname (or IP address) for the database connection';
$lang['error_nodbname'] = 'Please enter the name of a valid database on the host specified above';
$lang['error_nodbpass'] = 'Please enter a valid password for authenticating to the database';
$lang['error_nodbprefix'] = 'Please enter a valid prefix for database tables';
$lang['error_nodbtype'] = 'Please select a database type';
$lang['error_nodbuser'] = 'Please enter a valid username for authenticating to the database';
$lang['error_nodestdir'] = 'Destination directory not set';
$lang['error_nositename'] = 'Sitename is a required parameter. Please enter a suitable name for your website.';
$lang['error_notimezone'] = 'Please enter a valid timezone for this server';
$lang['error_overwrite'] = 'Permissions problem: cannot overwrite %s';
$lang['error_sendingmail'] = 'Error sending mail';
$lang['error_tzlist'] = 'A problem occurred retrieving the timezone identifiers list';
$lang['errorlevel_estrict'] = 'Checking for E_STRICT';
$lang['errorlevel_edeprecated'] = 'Checking for E_DEPRECATED';
$lang['edeprecated_enabled'] = 'E_DEPRECATED is enabled in the PHPs error_reporting.  Though this will not prevent CMSMS from operating, it may result in warnings being displayed in the output screen, particularly from older, third party modules';
$lang['estrict_enabled'] = 'E_STRICT is enabled in the PHPs error_reporting. Though this will not prevent CMSMS from operating, it may result in warnings being displayed in the HTML output, particularly from older, third party modules';

# F
$lang['fail_assets_dir'] = 'An assets directory already exists.  This application may write to this directory to rationalize the location of files.  Please ensure that you have a backup';
$lang['fail_assets_msg'] = 'An assets directory already exists.  This application may write to this directory to rationalize the location of files.  Please ensure that you have a backup';
$lang['fail_config_writable'] = 'The HTTP process cannot write to the config.php file. Please try to change the permissions on this file to 777 until the upgrade process is complete';
$lang['fail_curl_extension'] = 'The curl extension was not found. Though not a critical issue, this may cause problems with some third party modules';
$lang['fail_database_support'] = 'No compatible database drivers found';
$lang['fail_file_get_contents'] = 'The file_get_contents function does not exist, or is disabled. CMSMS Cannot continue (even the installer will probably fail)';
$lang['fail_file_uploads'] = 'File upload capabilities are disabled in this environment. Several functions of CMSMS will not function in this environment';
$lang['fail_func_json'] = 'json functionality was not found';
$lang['fail_func_gzopen'] = 'gzopen function was not found';
$lang['fail_func_md5'] = 'md5 functionality was not found';
$lang['fail_func_tempnam'] = 'The tempnam function does not exist. It is a required function for CMSMS functionality';
$lang['fail_func_ziparchive'] = 'ZipArchive functionality was not found.  This may limit functionality';
$lang['fail_ini_set'] = 'It appears that we cannot change ini settings. This could cause problems in third party modules (or when enabling debug mode)';
$lang['fail_intl_support'] = 'PHP\'s internationalization extension is not available';
$lang['fail_magic_quotes_runtime'] = 'It appears that magic quotes are enabled in your configuration. Please disable them and retry';
$lang['fail_max_execution_time'] = 'Your max execution time of %s does not meet the minimum value of %s.  We recommend you increase it to %s or greater';
$lang['fail_memory_limit'] = 'Your memory limit value is too low. You had %s, however a minimum of %s is required, and %s is recommended';
$lang['fail_multibyte_support'] = 'Multibyte support is not enabled in your configuration';
$lang['fail_output_buffering'] = 'Output buffering is not enabled.';
$lang['fail_open_basedir'] = 'Open basedir restrictions are in effect. CMSMS requires that this be disabled';
$lang['fail_php_version'] = 'The version of PHP available to CMSMS is critically important. The minimum accepted version is %s, though we recommend %s or greater. You have %s';
$lang['fail_post_max_size'] = 'Your post max size of %s does not meet the minimum value of %s. A value of %s or greater is recommended, and ensure that it is larger than the upload_max_filesize';
$lang['fail_pwd_writable2'] = 'The HTTP process must be able to write to the destination directory (and to all files and directories beneath it) in order to install files. We do not have write permission to (at least) %s';
$lang['fail_register_globals'] = 'Please disable register globals in your PHP configuration';
$lang['fail_remote_url'] = 'We encountered problems connecting to a remote URL.  This will limit some of the functionality of CMS Made Simple';
$lang['fail_safe_mode'] = 'CMSMS will not operate properly in an environment where safe mode is enabled. Safe mode is deprecated as a failed mechanism, and will be removed in future versions of PHP';
$lang['fail_session_save_path_exists'] = 'The session save path variable value is invalid or the directory does not exist';
$lang['fail_session_save_path_writable'] = 'The session save path directory is not writeable';
$lang['fail_session_use_cookies'] = 'CMSMS requires that PHP be configured to store the session key in a cookie';
$lang['fail_tmpfile'] = 'The system tmpfile() function is not functioning. This is required to allow us to extract archives. The optional TMPDIR url argument can be provided to the installer to specify a writeable directory. See the README file that should be in included in this directory.';
$lang['fail_tmp_dirs_empty'] = 'The CMSMS Temporary directories <em>(tmp/cache and tmp/templates_c) exist, and are not empty.  Please remove or empty them';
$lang['fail_xml_functions'] = 'The XML extension was not found. Please enable this in your PHP environment';
$lang['failed'] = 'failed';
$lang['file_get_contents'] = 'Testing for the file_get_contents function';
$lang['file_installed'] = 'Installed %s';
$lang['file_uploads'] = 'Checking for file upload support';
$lang['finished_custom_freshen_msg'] = 'Your installation has been freshened! The core files have been updated, and a new config file created. Please visit your website to ensure that everything is functioning correctly';
$lang['finished_custom_install_msg'] = 'Done! Please visit your website and login to the Admin panel.';
$lang['finished_custom_upgrade_msg'] = 'Done!  Please visit your CMSMS Admin panel, and frontend, to ensure that everything is working properly.<br/><strong>Hint:</strong> Now is a good time to create another backup.';
$lang['finished_freshen_msg'] = 'Your installation has been freshened! The core files have been updated, and a new config file created.  You can now <a href="%s">visit your website</a> or login to the <a href="%s">CMSMS Admin panel</a>.';
$lang['finished_install_msg'] = 'We are done! You can now <a href="%s">visit your website</a> or login to the <a href="%s">CMSMS admin panel</a>.';
$lang['finished_upgrade_msg'] = 'All done! Please visit your <a href="%s">website frontend</a> and the <a href="%s">Admin panel</a> to verify correct behaviour. You may also need to upgrade some third party modules.<br/><strong>Hint:</strong> Remember to create another backup after verifying correct behaviour.';
$lang['freshen'] = 'Freshen (repair) installation';
$lang['func_json'] = 'Checking for json encoding and decoding functionality';
$lang['func_md5'] = 'Checking for md5 functionality';
$lang['func_tempnam'] = 'Check for tempnam function';
$lang['func_gzopen'] = 'Check for gzopen function';
$lang['func_ziparchive'] = 'Check for ziparchive function';

# G
$lang['gd_version'] = 'GD Version';
$lang['goback'] = 'Back';

# H

# I
$lang['info_addlanguages'] = 'Select languages (in addition to English) to install. <strong>Note:</strong> not all translations are complete.';
$lang['info_adminaccount'] = 'Please provide credentials for the initial administrator account. This account will have access to all of the functionality of the CMSMS Admin console.';
$lang['info_advanced'] = 'Advanced mode enables more options in the installation procedure.';
$lang['info_dbinfo'] = 'CMS Made Simple stores a great deal of data in the database. A database connection is mandatory. Additionally, the user credentials you supply should have ALL PRIVILEGES on the specified database to allow creating, dropping and modifying tables, indexes and views.';
$lang['info_errorlevel_edeprecated'] = 'E_DEPRECATED is a flag for PHP&quot;s error reporting that indicates that warnings should be displayed about code that is using deprecated techniques.  Although the CMSMS core attempts to ensure that we no longer use deprecated techniques, some modules may not.  We recommend that this setting be disabled in the PHP configuration';
$lang['info_errorlevel_estrict'] = 'E_STRICT is a flag for PHP&#39;s error reporting which indicates that strict coding standards should be respected. Although the CMSMS core attempts to conform to E_STRICT standards, some modules may not. We recommend that this setting be disabled in the PHP configuration';
$lang['info_installcontent'] = 'By default, this installer will create a series of sample pages, stylesheets and templates in CMSMS. The sample content provides extensive information and tips to aid in building websites with CMSMS and is useful to read. However, if you are already familiar with CMS Made Simple, disabling this option will result in a minimal set of templates, stylesheets and content pages.';
$lang['info_open_basedir_session_save_path'] = 'open_basedir is enabled in your PHP configuration. We could not properly test session capabilities. However, getting to this point in the installation process probably indicates that sessions are working okay.';
$lang['info_pwd_writable'] = 'This application needs write permission to the current working directory';
$lang['info_queryvar'] = 'The query variable is used internally by CMSMS to identify the page requested. In most circumstances you should not need to adjust this.';
$lang['info_sitename'] = 'The website name is used in default templates as part of the title. Please enter a human readable name for the website';
$lang['info_timezone'] = 'The time zone information is needed for time calculations and time/date displays. Please select the server timezone';
$lang['ini_set'] = 'Testing if we can change INI settings';
$lang['install'] = 'Install';
$lang['install_attachstylesheets'] = 'Attach stylesheets to themes';
$lang['install_backupconfig'] = 'Backing up the config file';
$lang['install_createassets'] = 'Create assets structure';
$lang['install_created_index'] = 'Created index %s ... %s';
$lang['install_create_tables'] = 'Create database tables';
$lang['install_createconfig'] = 'Create new config file';
$lang['install_createcontentpages'] = 'Create default content pages';
$lang['install_created_table'] = 'Created table %s: .... %s';
$lang['install_createtablesindexes'] = 'Creating tables and indexes';
$lang['install_createtmpdirs'] = 'Create temporary directories';
$lang['install_creating_index'] = 'Created index %s';
$lang['install_default_collections'] = 'Install Default collections';
$lang['install_defaultcontent'] = 'Install default content';
$lang['install_detectlanguages'] = 'Detect installed languages';
$lang['install_dropping_tables'] = 'Dropping tables';
$lang['install_dummyindexhtml'] = 'Create dummy index.html files';
$lang['install_extractfiles'] = 'Extract files from archive';
$lang['install_initevents'] = 'Create events';
$lang['install_initsitegroups'] = 'Create initial groups';
$lang['install_initsiteperms'] = 'Set initial permissions';
$lang['install_initsiteprefs'] = 'Set initial site preferences';
$lang['install_initsiteusers'] = 'Create initial user account';
$lang['install_initsiteusertags'] = 'Initial user defined tags';
$lang['install_module'] = 'Install module %s';
$lang['install_modules'] = 'Install available modules';
$lang['install_passwordsalt'] = 'Set password salt';
$lang['install_requireddata'] = 'Set initial required data';
$lang['install_schema'] = 'Create database schema';
$lang['install_setschemaver'] = 'Set schema version';
$lang['install_setsequence'] = 'Reset sequence tables';
$lang['install_setsitename'] = 'Set site name';
$lang['install_stylesheets'] = 'Create default stylesheets';
$lang['install_templates'] = 'Create default templates';
$lang['install_templatetypes'] = 'Create standard template types';
$lang['install_update_sequences'] = 'Update sequence tables';
$lang['install_updatehierarchy'] = 'Update content hierarchy positions';
$lang['install_updateseq'] = 'Update sequence for %s';
$lang['installer_ver'] = 'Installer Version';
$lang['intl_support'] = 'Check for internationalization capabilities';

# J

# K

# L
$lang['legend'] = 'Legend';

# M
$lang['magic_quotes_runtime'] = 'Ensure magic quotes are disabled';
$lang['max_execution_time'] = 'Checking PHP script max execution time';
$lang['meaning'] = 'Meaning';
$lang['memory_limit'] = 'Checking for a sufficient PHP memory limit';
$lang['msg_clearedcache'] = 'Cleared server cache';
$lang['msg_configsaved'] = 'Existing config file saved to %s';
$lang['msg_upgrade_module'] = 'Upgrading module %s';
$lang['msg_upgrademodules'] = 'Upgrading modules';
$lang['msg_yourvalue'] = 'You have: %s';
$lang['multibyte_support'] = 'Check for multibyte support';

# N
$lang['next'] = 'Next';
$lang['no'] = 'No';
$lang['none'] = 'None';

# O
$lang['open_basedir'] = 'open_basedir restrictions';
$lang['open_basedir_session_save_path'] = 'open_basedir is in enabled. Cannot test session save path.';
$lang['output_buffering'] = 'Ensuring that output buffering is enabled';

# P
$lang['pass_config_writable'] = 'The HTTP process has write permission to the config.php file';
$lang['pass_database_support'] = 'At least one compatible database driver found';
$lang['pass_func_json'] = 'json functionality detected';
$lang['pass_func_md5'] = 'md5 functionality was detected';
$lang['pass_func_tempnam'] = 'The tempnam function exists';
$lang['pass_intl_support'] = 'Internationalization capabilities appear to be enabled';
$lang['pass_memory_limit_nolimit'] = 'There is no preset PHP memory limit';
$lang['pass_multibyte_support'] = 'Multibyte support appears to be enabled';
$lang['pass_php_version'] = 'The PHP version currently configured does not meet minimum requirements. At a minimum, PHP %s is required, though we recommend %s or higher';
$lang['pass_pwd_writable'] = 'The HTTP process can write into the destination directory. This is necessary for extracting files';
$lang['password'] = 'Password';
$lang['ph_sitename'] = 'Enter a Site Name';
$lang['php_version'] = 'PHP Version';
$lang['post_max_size'] = 'Checking maximum amount of data that can be posted in one request';
$lang['prompt_addlanguages'] = 'Additional Languages';
$lang['prompt_createtables'] = 'Create Database Tables';
$lang['prompt_dbhost'] = 'Database Hostname';
$lang['prompt_dbinfo'] = 'Database Information';
$lang['prompt_dbname'] = 'Database Name';
$lang['prompt_dbpass'] = 'Password';
$lang['prompt_dbport'] = 'Database Port Number';
$lang['prompt_dbprefix'] = 'Database Table Name Prefix';
$lang['prompt_dbtype'] = 'Database Type';
$lang['prompt_dbuser'] = 'User name';
$lang['prompt_dir'] = 'Installation Directory';
$lang['prompt_installcontent'] = 'Install Sample Content';
$lang['prompt_queryvar'] = 'Query Variable';
$lang['prompt_sitename'] = 'Web Site Name';
$lang['prompt_timezone'] = 'Server Timezone';
$lang['pwd_writable'] = 'Directory Writeable';

# Q
$lang['queue_for_upgrade'] = 'Queued non core module %s for upgrade at the next step.';

# R
$lang['readme_uc'] = 'README';
$lang['register_globals'] = 'Ensuring &quot;register globals&quot; is disabled';
$lang['remote_url'] = 'Test if we can make outgoing HTTP connections';
$lang['repeatpw'] = 'Repeat password';
$lang['reset_site_preferences'] = 'Reset some site preferences';
$lang['reset_user_settings'] = 'Reset user preferences';
$lang['retry'] = 'Retry';

# S
$lang['safe_mode'] = 'Testing to ensure &quot;safe mode&quot; is disabled';
$lang['saltpasswords'] = 'Salt Passwords';
$lang['select_language'] = 'The first thing we will ask you to do is to select your preferred language from the list below. This will be used to enhance your experience during this installation sequence, but will not affect your CMSMS installation.';
$lang['send_admin_email'] = 'Send Admin login credentials email';
$lang['session_capabilities'] = 'Testing for proper session capabilities (sessions are using cookies and session save path is writeable, etc)';
$lang['session_save_path_exists'] = 'Session_save_path exists';
$lang['session_save_path_writable'] = 'Session_save_path is writeable';
$lang['session_use_cookies'] = 'Ensuring that PHP sessions use cookies';
$lang['sometests_failed'] = 'We have performed numerous tests of your current web environment. Although no critical issues were found, we recommend that the following items be corrected before continuing.';
$lang['step1_advanced'] = 'Advanced Mode';
$lang['step1_destdir'] = 'Select Directory';
$lang['step1_info_destdir'] = '<strong>Warning:</strong> This program can install or upgrade multiple installations of CMS Made Simple. It is important that you select the correct directory for installation or upgrading.';
$lang['step1_language'] = 'Select Language';
$lang['step1_title'] = 'Select Language';
$lang['step2_cmsmsfound'] = 'An installation of CMS Made Simple was found. It is possible to upgrade this installation. However, before proceeding, ensure that you have a current, VERIFIED backup of all files and of the database';
$lang['step2_cmsmsfoundnoupgrade'] = 'Although an installation of CMS Made Simple was found, it is not possible to upgrade this version using this application. The version may be too old.';
$lang['step2_confirminstall'] = 'Are you sure you would like to install CMS Made Simple';
$lang['step2_confirmupgrade'] = 'Are you sure you would like to upgrade CMS Made Simple';
$lang['step2_errorsamever'] = 'The selected directory appears to contain a CMSMS installation with the same version that is included in this script. Continuing will freshen the installation.';
$lang['step2_errortoonew'] = 'The selected directory appears to contain a CMSMS installation with a newer version that is included in this script. Unable to proceed';
$lang['step2_info_freshen'] = 'Freshening the installation involves replacing all core files and recreating the configuration. You will be asked basic configuration information, however the database will not be touched.';
$lang['step2_installdate'] = 'Approximate installation date';
$lang['step2_install_dirnotempty2'] = 'This folder already contains some files and/or subfolders.  Though it is possible to install CMSMS here, it may inadvertantely corrupt an existing application.  Please double check the contents of this folder.  For reference purposes some of the files are listed below.  Please ensure that this is correct.';
$lang['step2_hdr_upgradeinfo'] = 'Version information';
$lang['step2_info_upgradeinfo'] = 'Below are the available release notes and changelog information for each release. The buttons below will display detailed information as to what has changed in each version of CMS Made Simple. There may be further instructions or warnings in each version that could affect the upgrade process.';
$lang['step2_minupgradever'] = 'The minimum version that this application can upgrade from is: %s. You may need to upgrade your application to a newer version in stages, using another method before completing the upgrade process. Please ensure that you have a complete, verified backup before using any upgrade method.';
$lang['step2_nocmsms'] = 'We did not find an installation of CMS Made Simple in this directory. It looks like this is a new installation';
$lang['step2_nofiles'] = 'As requested, CMSMS Core files will not be processed during this process';
$lang['step2_passed'] = 'Passed';
$lang['step2_pwd'] = 'Your current working directory';
$lang['step2_schemaver'] = 'Database Schema version';
$lang['step2_version'] = 'Your version';
$lang['step3_failed'] = 'This package has performed numerous tests of your PHP environment, and one or more of those tests have failed. You will need to rectify these errors in your configuration before continuing. Once you have rectified the errors, click &quot;Retry&quot; below.';
$lang['step3_passed'] = 'This package has performed numerous tests of your PHP environment, and they have all passed. This is great news! Although this is not an all-encompassing test, you should have no difficulty running the core installation of CMSMS.';
$lang['step9_removethis'] = '<strong>Warning</strong> For security reasons it is important that you remove the installation assistant from your browseable website as soon as you have verified that the operation has succeeded.';
$lang['symbol'] = 'Symbol';
$lang['social_message'] = 'I have successfully installed CMS Made Simple!';

# T
$lang['test_failed'] = 'A required test failed';
$lang['test_passed'] = 'A test passed <em>(passed tests are only displayed in advanced mode)</em>';
$lang['test_warning'] = 'A setting is above the required value, but below the recommended value, or...<br />A capability that may be required for some optional functionality is unavailable';
$lang['th_status'] = 'Status';
$lang['th_testname'] = 'Test';
$lang['th_value'] = 'Value';
$lang['title_error'] = 'Houston, We have a problem!';
$lang['title_step2'] = 'Step 2 - Detect existing software';
$lang['title_step3'] = 'Step 3 - Tests';
$lang['title_step4'] = 'Step 4 - Basic Configuration Information';
$lang['title_step5'] = 'Step 5 - Admin Account Information';
$lang['title_step6'] = 'Step 6 - Site Settings';
$lang['title_step7'] = 'Step 7 - Install Application Files';
$lang['title_step8'] = 'Step 8 - Database Work';
$lang['title_step9'] = 'Step 9 - Finish';
$lang['title_welcome'] = 'Welcome';
$lang['title_forum'] = 'Support Forum';
$lang['title_docs'] = 'Official Documentation';
$lang['title_api_docs'] = 'Official API Documentation';
$lang['to'] = 'to';
$lang['title_share'] = 'Share your experience with your friends.';
$lang['tmpfile'] = 'Checking for working tmpfile()';
$lang['tmp_dirs_empty'] = 'Ensure that temporary directories are empty or do not exist';

# U
$lang['upgrade'] = 'Upgrade';
$lang['upgrade_deleteoldevents'] = 'Deleting old events';
$lang['upgrading_schema'] = 'Updating database schema';
$lang['upload_max_filesize'] = 'Checking maximum size of uploaded files';
$lang['username'] = 'User name';

# V

# W
$lang['warn_disable_functions'] = 'Note: one or more PHP core functions are disabled. This can have negative impact on your CMSMS installation, particularly with third party extensions. Please keep an eye on your error log. Your disabled functions are: <br /><br />%s';
$lang['warn_max_execution_time'] = 'Although your max execution time of %s meets or exceeds the minimum value of %s, we recommend you increase it to %s or greater';
$lang['warn_memory_limit'] = 'Your memory limit value is %s, which is above the minimum of %s. However, %s is recommended';
$lang['warn_open_basedir'] = 'open_basedir is enabled in your php configuration.  Although you may continue, CMSMS will not support installs with open_basedir restrictions.';
$lang['warn_post_max_size'] = 'Your post max size value is %s, which is above the minimum of %s, however %s is recommended. Also, please ensure that this value is larger than the upload_max_filesize';
$lang['warn_tests'] = '<strong>Note:</strong> passing all of these tests should ensure that CMSMS functions properly for most sites. However, as the site grows and more functionality is added, these minimal values may become insufficient. Additionally, third party modules may have further requirements to function properly';
$lang['warn_upload_max_filesize'] = 'Although your setting of %s is sufficient, we recommend you increase the upload_max_filesize setting in PHP to at least %s';
$lang['welcome_message'] = 'Welcome! This is the CMS Made Simple Automatic Installation Mechanism. This package will allow you to quickly and easily confirm that your web host is compatible with CMSMS and to install or upgrade to the latest version of CMS Made Simple.<br />We know that you will enjoy it.';
$lang['wizard_step1'] = 'Welcome';
$lang['wizard_step2'] = 'Detect Existing Software';
$lang['wizard_step3'] = 'Compatibility Tests';
$lang['wizard_step4'] = 'Configuration Info';
$lang['wizard_step5'] = 'Admin Account Info';
$lang['wizard_step6'] = 'Site Settings';
$lang['wizard_step7'] = 'Files';
$lang['wizard_step8'] = 'Database work';
$lang['wizard_step9'] = 'Finish';

# X
$lang['xml_functions'] = 'Checking for XML functionality';

# Y
$lang['yes'] = 'Yes';

# Z

?>
