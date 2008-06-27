<?php
// This are in admin.inc.ph
$lang['admin']['sitename'] = 'Site Name';
$lang['admin']['warning_safe_mode'] = '<strong><em>WARNING:</em></strong> PHP Safe mode is enabled.  This will cause difficulty with files uploaded via the web browser interface, including images, theme and module XML packages.  You are advised to contact your site administrator to see about disabling safe mode.';
$lang['admin']['test'] = 'Test';
$lang['admin']['results'] = 'Results';
$lang['admin']['untested'] = 'Not Tested';
$lang['admin']['owner'] = 'Owner';
$lang['admin']['permissions'] = 'Permissions';
$lang['admin']['off'] = 'Off';
$lang['admin']['on'] = 'On';
$lang['admin']['permission_information'] = 'Permission Information';
$lang['admin']['server_os'] = 'Server Operating System';
$lang['admin']['server_api'] = 'Server API';
$lang['admin']['server_software'] = 'Server Software';
$lang['admin']['server_information'] = 'Server Information';
$lang['admin']['session_save_path'] = 'Session Save Path';
$lang['admin']['max_execution_time'] = 'Maximum Execution Time';
$lang['admin']['gd_version'] = 'GD version';
$lang['admin']['upload_max_filesize'] = 'Maximum Upload Size';
$lang['admin']['post_max_size'] = 'Maximum Post Size';
$lang['admin']['memory_limit'] = 'PHP Memory Limit';
$lang['admin']['server_db_type'] = 'Server Database';
$lang['admin']['server_db_version'] = 'Server Database Version';
$lang['admin']['phpversion'] = 'Current PHP Version';
$lang['admin']['safe_mode'] = 'PHP Safe Mode';
$lang['admin']['php_information'] = 'PHP Information';
$lang['admin']['cms_install_information'] = 'CMS Install Information';
$lang['admin']['cms_version'] = 'CMS Version';
$lang['admin']['systeminfo_copy_paste'] = 'Please copy and paste this selected text into your forum posting';
$lang['admin']['help_systeminformation'] = <<<EOT
The information displayed below is collected from a variety of locations, and summarized here so that you may be able to conveniently find some of the information required when trying to diagnose a problem or request help with your CMS Made Simple installation.
EOT;
$lang['admin']['systeminfo'] = 'System Information';
$lang['admin']['systeminfodescription'] = 'Display various pieces of information about your system that may be useful in diagnosing problems';
$lang['admin']['error'] = 'Error';
$lang['admin']['new_version_available'] = '<em>Notice:</em> A new version of CMS Made Simple is available.  Please notify your administrator.';
$lang['admin']['info_urlcheckversion'] = 'If this url is the word &quot;none&quot; no checks will be made.<br/>An empty string will result in a default URL being used.';
$lang['admin']['urlcheckversion'] = 'Check for new CMS versions using this URL';

$lang['admin']['read'] = 'Read';
$lang['admin']['write'] = 'Write';
$lang['admin']['execute'] = 'Execute';
$lang['admin']['group'] = 'Group';
$lang['admin']['other'] = 'Other';
$lang['admin']['global_umask'] = 'File Creation Mask (umask)';
$lang['admin']['errorcantcreatefile'] = 'Could not create a file (permissions problem?)';
$lang['admin']['add'] = 'Add';
$lang['admin']['about'] = 'About';
$lang['admin']['action'] = 'Action';
$lang['admin']['actionstatus'] = 'Action/Status';
$lang['admin']['active'] = 'Active';
$lang['admin']['cantremove'] = 'Cannot Remove';
$lang['admin']['changepermissions'] = 'Change Permissions';
$lang['admin']['changepermissionsconfirm'] = 'USE CAUTION\n\nThis action will attempt to ensure that all of the files making up the module are writable by the web server.\nAre you sure you want to continue?';
$lang['admin']['success'] = 'Success';
$lang['admin']['advanced'] = 'Advanced';
$lang['admin']['back'] = 'Back to Menu';
$lang['admin']['cancel'] = 'Cancel';
$lang['admin']['cantchmodfiles'] = 'Couldn\'t change permissions on some files';
$lang['admin']['cantremovefiles'] = 'Problem Removing Files (permissions?)';
$lang['admin']['create'] = 'Create';
$lang['admin']['database'] = 'Database';
$lang['admin']['databaseprefix'] = 'Database Prefix';
$lang['admin']['databasetype'] = 'Database Type';
$lang['admin']['date'] = 'Date';
$lang['admin']['default'] = 'Default';
$lang['admin']['delete'] = 'Delete';
$lang['admin']['deleteconfirm'] = 'Are you sure you want to delete - %s - ?';
$lang['admin']['description'] = 'Description';
$lang['admin']['directoryexists'] = 'This directory already exists.';
$lang['admin']['down'] = 'Down';
$lang['admin']['edit'] = 'Edit';
$lang['admin']['email'] = 'Email Address';
$lang['admin']['errordeletingfile'] = 'Could not delete file. Permissions Problem?';
$lang['admin']['errordirectorynotwritable'] = 'No permission to write in directory.  This could be caused by file permissions and ownership.  Safe mode may also be in effect.';
$lang['admin']['cachenotwritable'] = 'Cache folder is not writable. Clearing cache will not work. Please make the tmp/cache folder have full read/write/execute permissions (chmod 777).  You may also have to disable safe mode.';
$lang['admin']['modulesnotwritable'] = 'The modules folder is not writable, if you would like to install modules by uploading an XML file you need to make the modules folder have full read/write/execute permissions (chmod 777).  Safe mode may also be in effect.';
$lang['admin']['false'] = 'False';
$lang['admin']['settrue'] = 'Set True';
$lang['admin']['filename'] = 'Filename';
$lang['admin']['filesize'] = 'File Size';
$lang['admin']['help'] = 'Help';
$lang['admin']['language'] = 'Language';
$lang['admin']['lastname'] = 'Last Name';
$lang['admin']['name'] = 'Name';
$lang['admin']['owner'] = 'Owner';
$lang['admin']['password'] = 'Password';
$lang['admin']['passwordagain'] = 'Password (again)';
$lang['admin']['remove'] = 'Remove';
$lang['admin']['saveconfig'] = 'Save Config';
$lang['admin']['true'] = 'True';
$lang['admin']['setfalse'] = 'Set False';
$lang['admin']['type'] = 'Type';
$lang['admin']['typenotvalid'] = 'Type is not valid';
$lang['admin']['user'] = 'User';
$lang['admin']['userdefinedtags'] = 'User Defined Tags';
$lang['admin']['usermanagement'] = 'User Management';
$lang['admin']['username'] = 'Username';
$lang['admin']['usernameincorrect'] = 'Username or password incorrect';
$lang['admin']['version'] = 'Version';



// Only used by Installer
$lang['admin']['install_title'] = 'CMS Made Simple Install (step %s)';
$lang['admin']['install_system'] = 'Install System';
$lang['admin']['install_thanks'] = 'Thanks for installing CMS Made Simple';
$lang['admin']['install_please_read'] = 'Please read the <a href="http://wiki.cmsmadesimple.org/index.php/User_Handbook/Installation/Troubleshooting">Installation Troubleshooting</a> page in the CMS Made Simple Documentation Wiki.';
$lang['admin']['install_checking'] = 'Checking permissions and PHP settings';
$lang['admin']['install_test'] = 'Test';
$lang['admin']['install_result'] = 'Result';
$lang['admin']['install_required_settings'] = 'Required settings';
$lang['admin']['install_recommended_settings'] = 'Recommended settings';
$lang['admin']['install_you_have'] = 'You have';
$lang['admin']['install_legend'] = 'Legend';
$lang['admin']['install_symbol'] = 'Symbol';
$lang['admin']['install_definition'] = 'Definition';
$lang['admin']['install_value_passed'] = 'A required test passed';
$lang['admin']['install_value_failed'] = 'A required test failed';

$lang['admin']['install_value_required'] = 'A setting is below a required minimum value';
$lang['admin']['install_value_recommended'] = 'A setting is above the required value, but below the recommended value<br />or... A capability that <em>may</em> be required for some optional functionality is unavailable';
$lang['admin']['install_value_exceed'] = 'A setting meets or exceeds the recommended threshhold<br />or... A capability that <em>may</em> be required for some optional functionality is available';
$lang['admin']['install_test_failed'] = 'One or more tests have failed. You can still install the system but some functions may not work correctly.<br />Please try to correct the situation and click "Try Again", or click the Continue button.';
$lang['admin']['install_test_passed'] = 'All tests passed (at least at a minimum level). Please click the Continue button.';
$lang['admin']['install_failed_again'] = 'One or more tests have failed. Please correct the problem and click the button below to recheck.';

$lang['admin']['install_try_again'] = 'Try Again';
$lang['admin']['install_continue'] = 'Continue';
$lang['admin']['success'] = 'Success';
$lang['admin']['failure'] = 'Failure';
$lang['admin']['caution'] = 'Caution';

$lang['admin']['test_username_not_given'] = 'Username not given!';
$lang['admin']['test_username_illegal'] = 'Username contains illegal characters!';
$lang['admin']['test_not_both_passwd'] = 'Not both password fields given!';
$lang['admin']['test_passwd_not_match'] = 'Password fields do not match!';
$lang['admin']['test_email_accountinfo'] = 'E-mail accountinfo selected, but no E-mail address given!';
$lang['admin']['test_database_prefix'] = 'Database prefix contains invalid characters';
$lang['admin']['test_no_dbms'] = 'No dbms selected!';
$lang['admin']['test_could_not_connect_db'] = 'Could not connect to the database. Verify that username and password are correct, and that the user has access to the given database.';
$lang['admin']['test_could_not_drop_table'] = 'Could not drop a table. Verify that the user has privileges to drop tables in the given database.';
$lang['admin']['test_could_not_create_table'] = 'Could not create a table. Verify that the user has privileges to create tables in the given database.';

$lang['admin']['test_check_php'] = 'Checking for PHP version 4.3+';
$lang['admin']['test_min_recommend'] = '(min %s, recommend %s)';
$lang['admin']['test_requires_php_version'] = 'CMS Made Simple requires a php version of 4.3 or greater (you have %s), but PHP %s or greater is recommended to ensure maximum compatibility with third party addons';
$lang['admin']['test_check_md5_func'] = 'Checking for md5 Function';
$lang['admin']['test_check_safe_mode'] = 'Checking for safe mode';
$lang['admin']['test_check_safe_mode_failed'] = 'PHP safe mode could create some problems with uploading files and other functions. It all depends on how strict your server safe mode settings are.';
$lang['admin']['test_check_tokenizer'] = 'Checking for tokenizer functions';
$lang['admin']['test_check_tokenizer_failed'] = 'Not having the tokenizer could cause pages to render as purely white. We recommend you have this installed, but your website may work fine without it.';
  $lang['admin']['test_check_gd'] = 'Checking for GD library';
  $lang['admin']['test_check_gd_failed'] = 'GD library are mandatory for some modules and functionality.';
$lang['admin']['test_check_write'] = 'Checking write permission on';
$lang['admin']['test_may_not_exist'] = 'This file may not exist yet. If it does not, you should create an empty file with this name. Please also ensure that this file writable by the web server process.';

$lang['admin']['could_not_retrieve_a_value'] = 'Could not retrieve a value.... passing anyways.';
$lang['admin']['displaying_the_value_originally'] = '<br />Displaying the value originally set in the config file (this may not be accurate).';
$lang['admin']['test_check_xml_func'] = 'Checking for basic XML (expat) support';
$lang['admin']['test_check_xml_failed'] = 'XML support is not compiled into your php install. You can still use the system, but will not be able to use any of the remote module installation functions.';
$lang['admin']['test_check_file_upload'] = 'Checking file uploads';
$lang['admin']['test_check_file_failed'] = 'When file uploads are disabled you will not be able to use any of the file uploading facilities included in CMS Made Simple. If possible, this restriction should be lifted by your system admin to properly use all file management features of the system. Proceed with caution.';
$lang['admin']['test_check_memory'] = 'Checking PHP memory limit';
  $lang['admin']['test_check_time_limit'] = 'Checking PHP time limit';
$lang['admin']['test_check_memory_failed'] = 'You may not have enough memory to run CMSMS correctly, or with all of your desired addons. If possible, you should try to get your system admin to raise this value. Proceed with caution.';
  $lang['admin']['test_check_time_limit_failed'] = 'Number of seconds a script is allowed to run. If this is reached, the script returns a fatal error.';
  $lang['admin']['test_check_post_max'] = 'Checking max post size';
  $lang['admin']['test_check_post_max_failed'] = 'You will probably not be able to submit (larger) data. Please be aware of this restriction.';
$lang['admin']['test_check_upload_max'] = 'Checking max upload file size';
$lang['admin']['test_check_upload_max_failed'] = 'You will probably not be able to upload (larger) files using the included file management functions. Please be aware of this restriction.';
$lang['admin']['test_check_writable'] = 'Checking if %s is writable';
$lang['admin']['test_check_upload_failed'] = 'The uploads folder is not writable. You can still install the system, but you will not be able to upload files via the Admin Panel.';
  $lang['admin']['test_check_images_failed'] = 'The images folder is not writable. You can still install the system, but you will not be able to upload and used images via the Admin Panel.';
$lang['admin']['test_check_modules_failed'] = 'The modules folder is not writable. You can still install the system, but you will not be able to upload modules via the Admin Panel.';
$lang['admin']['test_check_file_get_contents'] = 'Checking for file_get_contents';
$lang['admin']['test_check_file_get_contents_failed'] = 'The file_get_contents function was added in PHP 4.3 and although a workaround has been added that should allow most functionality that uses this function to work properly in PHP 4.2, it may be advisable to upgrade to PHP 4.3 or greater.';
$lang['admin']['test_check_session_save_path'] = 'Checking if session.save_path is writable';
  $lang['admin']['test_empty_session_save_path'] = 'Your session.save_path is empty. PHP will use the temporary directory of your SO. If you have SESSION problems and ini_set works you can try to enable session cookies adding: ini_set(\'session.use_only_cookies\', 1);  to top of include.php';
$lang['admin']['test_check_session_save_path_failed'] = 'Your session.save_path is "%s". Not having this as writable may make logins to the Admin Panel not work. You may want to look into making this path writable if you have trouble logging into the Admin Panel. This test may fail if safe_mode is enabled (see below).';
$lang['admin']['test_check_ini_set'] = 'Checking if ini_set works';
$lang['admin']['test_check_ini_set_failed'] = 'Although the ability to override php ini settings is not mandatory, some addon (optional) functionality uses ini_set to extend timeouts, and allow uploading of larger files, etc. You may have difficulty with some addon functionality without this capability. This test may fail if safe_mode is enabled (see below).';

$lang['admin']['install_admin_header'] = 'Admin Account Information';
$lang['admin']['install_admin_info'] = 'Select the username, password and email address for your admin account. Please make sure you record this password somewhere, as there will be no other way to login to your CMS Made Simple admin system without it.';
$lang['admin']['install_admin_email'] = 'E-mail Address';
$lang['admin']['install_admin_email_info'] = 'E-Mail Account Information';
$lang['admin']['install_admin_email_note'] = '<strong>Note:</strong> This function uses the php\'s mail function. If you don\'t receive this email, it may be an indication that your server is not properly configured and that you should contact your host administrator.';

$lang['admin']['install_admin_sitename'] = 'This is the name of your site. It will be used in various places of the default templates and can be used anywhere with the	{sitename} tag.';
$lang['admin']['install_admin_db'] = 'Database Information';
$lang['admin']['install_admin_db_info'] = <<<EOT
<p>Make sure you have created your database and granted full privileges to a user to use that database.</p>
<p>For MySQL, use the following:</p>
<p>Log in to mysql from a console and run the following commands:</p>
<ol>
<li>create database cms; (use whatever name you want here but make sure to remember it, you&apos;ll need to enter it on this page)</li>
<li>grant all privileges on cms.* to cms_user@localhost identified by 'cms_pass';</li>
</ol>
EOT;
$lang['admin']['install_admin_follow'] = 'Please complete the following fields';
$lang['admin']['install_admin_db_type'] = 'Database Type';
$lang['admin']['install_admin_no_db'] = 'No valid database drivers appear to be compiled into your PHP install. Please confirm that you have mysql, mysqli, and/or postgres7 support installed, and try again.';
$lang['admin']['install_admin_db_host'] = 'Database host address';
$lang['admin']['install_admin_db_name'] = 'Database name';
$lang['admin']['install_admin_db_create'] = 'Create Tables (Warning: Deletes existing data)';
$lang['admin']['install_admin_db_sample'] = 'Install sample content and templates';
$lang['admin']['retry'] = 'Retry';
$lang['admin']['install_admin_db_create_seq'] = 'Creating %s table sequence...';

$lang['admin']['install_admin_importing'] = 'Importing sample data...';
$lang['admin']['invalid_query'] = 'Invalid query: %s';
$lang['admin']['install_creating_table'] = '<p>Creating %s table...';

$lang['admin']['done'] = 'done';
$lang['admin']['failed'] = 'failed';
  $lang['admin']['install_admin_error_schema'] = 'Error in retrieve SQL schema';
$lang['admin']['install_admin_set_account'] = 'Setting admin account information...';
$lang['admin']['install_admin_set_sitename'] = 'Setting sitename...';
$lang['admin']['install_admin_setup'] = 'Now let\'s continue to setup your configuration file, we already have most of the stuff we need. Chances are you can leave all these values alone, so when you are ready, click Continue.';
$lang['admin']['install_admin_docroot'] = 'CMS Document root (as seen from the webserver)';
$lang['admin']['install_admin_docroot_path'] = 'Path to the Document root';
$lang['admin']['install_admin_querystring'] = 'Query string (leave this alone unless you have trouble, then edit config.php by hand)';
$lang['admin']['invalid_query'] = 'Invalid query: %s';
  $lang['admin']['invalid_querys'] = '<b>WARNING<b/>: Invalid queries on your DB!';
$lang['admin']['install_admin_sitedown'] = 'Error: Could not remove the tmp/cache/SITEDOWN file. Please remove manually.';
$lang['admin']['install_admin_update_hierarchy'] = 'Updating hierarchy positions...';
$lang['admin']['install_admin_set_core_event'] = 'Setting up core events...';
$lang['admin']['install_admin_install_modules'] = 'Installing modules...';
$lang['admin']['install_admin_index_search'] = 'Index Search...';
$lang['admin']['install_admin_clear_cache'] = 'Clearing site cache (if any)...';
$lang['admin']['install_admin_emailing'] = 'E-mailing admin account information...';
$lang['admin']['install_admin_congratulations'] = 'Congratulations, you are all setup - here is your <a href="%s">CMS site</a>';
$lang['admin']['could_not_connect_db'] = 'Could not connect to the database. Verify that username and password are correct, and that the user has access to the given database.';
$lang['admin']['cannot_write_config'] = 'Error: Cannot write to %s.';
?>