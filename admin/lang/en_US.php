<?php
$lang['addmoduletemplate'] = 'Add Module Template';
$lang['editmoduletemplate'] = 'Edit Module Template';
$lang['add_template'] = 'Add Template';
$lang['page_name'] = 'Page Name';
$lang['page_type'] = 'Page Type';
$lang['page_template'] = 'Page Template';
$lang['unique_alias'] = 'Unique Alias';
$lang['listtemplates_pagelimit'] = 'Number of rows per page when viewing templates';
$lang['liststylesheets_pagelimit'] = 'Number of rows per page when viewing stylesheets';
$lang['listgcbs_pagelimit'] = 'Number of rows per page when viewing Global Content Blocks';
$lang['insecure'] = 'Insecure (HTTP)';
$lang['secure'] = 'Secure (HTTPS)';
$lang['secure_page'] = 'Use HTTPS for this page';
$lang['thumbnail_width'] = 'Thumbnail Width';
$lang['thumbnail_height'] = 'Thumbnail Height';
$lang['E_STRICT'] = 'Is E_STRICT disabled in error_reporting';
$lang['test_estrict_failed'] = 'E_STRICT is enabled in the error_reporting';
$lang['info_estrict_failed'] = 'Some libraries that CMSMS uses do not work well with E_STRICT.  Please disable this before continuing';
$lang['E_DEPRECATED'] = 'Is E_DEPRECATED disabled in error_reporting';
$lang['test_edeprecated_failed'] = 'E_DEPRECATED is enabled';
$lang['info_edeprecated_failed'] = 'If E_DEPRECATED is enabled in your error reporting users will see alot of warning messages that could effect the display and functionalty';
$lang['session_use_cookies'] = 'Sessions are allowed to use Cookies';
$lang['errorgettingcontent'] = 'Could not retrieve information for the specified content object';
$lang['errordeletingcontent'] = 'Error deleting content (either this page has children or is the default content)';
$lang['invalidemail'] = 'The email address entered is invalid';
$lang['info_deletepages'] = 'Note: due to permission restrictions, some of the pages you selected for deletion may not be listed below';
$lang['info_pagealias'] = 'Specify a unique alias for this page.';
$lang['info_autoalias'] = 'If this field is empty, an alias will be created automatically.';
$lang['invalidparent'] = 'You must select a parent page (contact your administrator if you do not see this option).';
$lang['forgotpwprompt'] = 'Enter your admin username.  An email will then be sent to the email address associated with that username with new login information';
$lang['info_basic_attributes'] = 'This field allows you to specify which content properties that users without the &quot;Manage All Content&quot; permission are allowed to edit.';
$lang['basic_attributes'] = 'Basic Properties';
$lang['no_permission'] = 'You have not permission to perform that function.';
$lang['bulk_success'] = 'Bulk operation was successfully updated.';
$lang['no_bulk_performed'] = 'No bulk operation performed.';
$lang['info_preview_notice'] = 'Warning: This preview panel behaves much like a browser window allowing you to navigate away from the initially previewed page. However, if you do that, you may experience unexpected behaviour. If you navigate away from the initial display and return, you may not see the un-committed content until you make a change to the content in the main tab, and then reload this tab. When adding content, if you navigate away from this page, you will be unable to return, and must refresh this panel.';
$lang['sitedownexcludes'] = 'Exclude these Addresses from Sitedown Messages';
$lang['info_sitedownexcludes'] = <<<EOT
This parameter allows listing a comma separated list of ip addresses or networks that should not be subject to the sitedown mechanism.  This allows administrators to work on a site whilst anonymous visitors receive a sitedown message.<br/><br/>Addresses can be specified in the following formats:<br/>
1. xxx.xxx.xxx.xxx -- (exact IP address)<br/>
2. xxx.xxx.xxx.[yyy-zzz] -- (IP address range)<br/>
3. xxx.xxx.xxx.xxx/nn -- (nnn = number of bits, cisco style.  i.e:  192.168.0.100/24 = entire 192.168.0 class C subnet)
EOT;
$lang['setup'] = 'Advanced Setup';
$lang['handle_404'] = 'Custom 404 Handling';
$lang['sitedown_settings'] = 'Sitedown Settings';
$lang['general_settings'] = 'General Settings';
$lang['help_function_page_attr'] = <<<EOT
<h3>What does this do?</h3>
<p>This tag can be used to return the value of the attributes of a certain page.</p>
<h3>How do I use it?</h3>
<p>Insert the tag into the template like: <code>{page_attr key="extra1"}</code>.</p>
<h3>What parameters does it take?</h3>
<ul>
  <li><strong>key [required]</strong> The key to return the attribute of.</li>
</ul>
EOT;
$lang['forge'] = 'Forge';
$lang['disable_wysiwyg'] = 'Disable WYSIWYG editor on this page (regardless of template or user settings)';
$lang['help_function_page_image'] = <<<EOT
<h3>What does this do?</h3>
<p>This tag can be used to return the value of the image or thumbnail fields of a certain page.</p>
<h3>How do I use it?</h3>
<p>Insert the tag into the template like: <code>{page_image}</code>.</p>
<h3>What parameters does it take?</h3>
<ul>
  <li>thumbnail - Optionally display the value of the thumbnail property instead of the image property.</li>
</ul>
EOT;
$lang['pagelink_circular'] = 'A page link cannot list another page link as its destination';
$lang['destinationnotfound'] = 'The selected page could not be found or is invalid';
$lang['help_function_dump'] = <<<EOT
<h3>What does this do?</h3>
  <p>This tag can be used to dump the contents of any smarty variable in a more readable format.  This is useful for debugging, and editing templates, to know the format and types of data available.</p>
<h3>How do I use it?</h3>
<p>Insert the tag in the template like <code>{dump item='the_smarty_variable_to_dump'}</code>.</p>
<h3>What parameters does it take?</h3>
<ul>
<li><strong>item (required)</strong> - The smarty variable to dump the contents of.</li>
<li>maxlevel - The maximum number of levels to recurse (applicable only if recurse is also supplied.  The default value for this parameter is 3</li>
<li>nomethods - Skip output of methods from objects.</li>
<li>novars - Skip output of object members.</li>
<li>recurse - Recurse a maximum number of levels through the objects providing verbose output for each item until the maximum number of levels is reached.</li>
</ul>
EOT;
$lang['sqlerror'] = 'SQL error in %s';
$lang['image'] = 'Image';
$lang['thumbnail'] = 'Thumbnail';
$lang['searchable'] = 'This page is searchable';
$lang['help_function_content_image'] = <<<EOT
<h3>What does this do?</h3>
<p>This plugin allows template designers to prompt users to select an image file when editing the content of a page. It behaves similarly to the content plugin, for additional content blocks.</p>
<h3>How do I use it?</h3>
<p>Just insert the tag into your page template like: <code>{content_image block='image1'}</code>.</p>
<h3>What parameters does it take?</h3>
<ul>
  <li><strong>(required)</strong> block - The name for this additional content block.
  <p>Example:</p>
  <pre>{content_image block='image1'}</pre><br/>
  </li>

  <li><em>(optional)</em> label - A label or prompt for this content block in the edit content page.  If not specified, the block name will be used.</li>
 
  <li><em>(optional)</em> dir - The name of a directory (relative to the uploads directory, from which to select image files. If not specified, the uploads directory will be used.
  <p>Example: use images from the uploads/image directory.</p>
  <pre>{content_image block='image1' dir='images'}</pre><br/>
  </li>

  <li><em>(optional)</em> class - The css class name to use on the img tag in frontend display.</li>

  <li><em>(optional)</em> id - The id name to use on the img tag in frontend display.</li> 

  <li><em>(optional)</em> name - The tag name to use on the img tag in frontend display.</li> 

  <li><em>(optional)</em> width - The desired width of the image.</li>

  <li><em>(optional)</em> height - The desired height of the image.</li>

  <li><em>(optional)</em> alt - Alternative text if the image cannot be found.</li>
  <li><em>(optional)</em> urlonly - output only the url to the image, ignoring all parameters like id, name, width, height, etc.</li>
</ul>
EOT;
$lang['error_udt_name_chars'] = 'A valid UDT name starts with a letter or underscore, followed by any number of letters, numbers, or underscores.';
$lang['errorupdatetemplateallpages'] = 'Template is not active';
$lang['hidefrommenu'] = 'Hide From Menu';
$lang['settemplate'] = 'Set Template';
$lang['text_settemplate'] = 'Set Selected Pages to a different Template';
$lang['cachable'] = 'Cachable';
$lang['noncachable'] = 'Non Cachable';
$lang['copy_from'] = 'Copy From';
$lang['copy_to'] = 'Copy To';
$lang['copycontent'] = 'Copy Content Item';
$lang['md5_function'] = 'md5 function';
$lang['tempnam_function'] = 'tempnam function';
$lang['register_globals'] = 'PHP register_globals';
$lang['output_buffering'] = 'PHP output_buffering';
$lang['disable_functions'] = 'disable_functions in PHP';
$lang['xml_function'] = 'Basic XML (expat) support';
$lang['magic_quotes_gpc'] = 'Magic quotes for Get/Post/Cookie';
$lang['magic_quotes_gpc_on'] = 'Single-quote, double quote and backslash are escaped automatically. You can experience problems when saving templates';
$lang['magic_quotes_runtime'] = 'Magic quotes in runtime';
$lang['magic_quotes_runtime_on'] = 'Most functions that return data will have quotes escaped with a backslash. You can experience problems';
$lang['file_get_contents'] = 'Test file_get_contents';
$lang['check_ini_set'] = 'Test ini_set';
$lang['check_ini_set_off'] = 'You may have difficulty with some functionality without this capability. This test may fail if safe_mode is enabled';
$lang['file_uploads'] = 'File uploads';
$lang['test_remote_url'] = 'Test for remote URL';
$lang['test_remote_url_failed'] = 'You will probably not be able to open a file on a remote web server.';
$lang['test_allow_url_fopen_failed'] = 'When allow url fopen is disabled you will not be able to accessing URL object like file using the ftp or http protocol.';
$lang['connection_error'] = 'Outgoing http connections do not appear to work! There is a firewall or some ACL for external connections?. This will result in module manager, and potentially other functionality failing.';
$lang['remote_connection_timeout'] = 'Connection Timed Out!';
$lang['search_string_find'] = 'Connection ok!';
$lang['connection_failed'] = 'Connection failed!';
$lang['remote_response_ok'] = 'Remote response: ok!';
$lang['remote_response_404'] = 'Remote response: not found!';
$lang['remote_response_error'] = 'Remote response: error!';

$lang['notifications_to_handle'] = 'You have <b>%d</b> unhandled notifications';
$lang['notification_to_handle'] = 'You have <b>%d</b> unhandled notification';
$lang['notifications'] = 'Notifications';
$lang['dashboard'] = 'View Dashboard';
$lang['ignorenotificationsfrommodules'] = 'Ignore notifications from these modules';
$lang['admin_enablenotifications'] = 'Allow users to view notifications<br/><em>(notifications will be displayed on all admin pages)</em>';
$lang['enablenotifications'] = 'Enable user notifications in the admin section';
$lang['test_check_open_basedir_failed'] = 'Open basedir restrictions are in effect. You may have difficulty with some addon functionality with this restriction';
$lang['config_writable'] = 'config.php writable. It is more safe if you change permission to read-only';
$lang['caution'] = 'Caution';
$lang['create_dir_and_file'] = 'Checking if the httpd process can create a file inside of a directory it created';
$lang['os_session_save_path'] = 'No check because OS path';
$lang['unlimited'] = 'Unlimited';
$lang['open_basedir'] = 'PHP Open Basedir';
$lang['open_basedir_active'] = 'No check because open basedir active';
$lang['invalid'] = 'Invalid';
$lang['checksum_passed'] = 'All checksums match those in the uploaded file';
$lang['error_retrieving_file_list'] = 'Error retrieving file list';
$lang['files_checksum_failed'] = 'Files could not be checksummed';
$lang['failure'] = 'Failure';
$lang['help_function_process_pagedata'] = <<<EOT
<h3>What does this do?</h3>
<p>This plugin will process the data in the &quot;pagedata&quot; block of content pages through smarty.  It allows you to specify page specific data to smarty without changing the template for each page.</p>
<h3>How do I use it?</h3>
<ol>
  <li>Insert smarty assign variables and other smarty logic into the pagedata field of some of your content pages.</li>
  <li>Insert the <code>{process_pagedata}</code> tag into the very top of your page template.</li>
</ol>
<br/>
<h3>What parameters does it take?</h3>
<p>None at this time</p>
EOT;
$lang['page_metadata'] = 'Page Specific Metadata';
$lang['pagedata_codeblock'] = 'Smarty data or logic that is specific to this page';
$lang['error_uploadproblem'] = 'An error occurred in the upload';
$lang['error_nofileuploaded'] = 'No File has been uploaded';
$lang['files_failed'] = 'Files failed md5sum check';
$lang['files_not_found'] = 'Files Not found';
$lang['info_generate_cksum_file'] = <<<EOT
This function will allow you to generate a checksum file and save it on your local computer for later validation.  This should be done just prior to rolling out the website, and/or after any upgrades, or major modifications.
EOT;
$lang['info_validation'] = <<<EOT
This function will compare the checksums found in the uploaded file with the files on the current installation.  It can assist in finding problems with uploads, or exactly what files were modified if your system has been hacked.  A checksum file is generated for each release of CMS Made simple from version 1.4 on.
EOT;
$lang['download_cksum_file'] = 'Download Checksum File';
$lang['perform_validation'] = 'Perform Validation';
$lang['upload_cksum_file'] = 'Upload Checksum File';
$lang['checksumdescription'] = 'Validate the integrity of CMS files by comparing against known checksums';
$lang['system_verification'] = 'System Verification';
$lang['extra1'] = 'Extra Page Attribute 1';
$lang['extra2'] = 'Extra Page Attribute 2';
$lang['extra3'] = 'Extra Page Attribute 3';
$lang['start_upgrade_process'] = 'Start Upgrade Process';
$lang['warning_upgrade'] = '<em><strong>Warning:</strong></em> CMSMS is in need of an upgrade.';
$lang['warning_upgrade_info1'] = 'You are now running schema version %s. and you need to be upgraded to version %s';
$lang['warning_upgrade_info2'] = 'Please click the following link: %s.';
$lang['warning_mail_settings'] = <<<EOT
Your mail settings have not been configured.  This could interfere with the ability of your website to send email messages.  You should go to <a href="%s">Extensions >> CMSMailer</a> and configure the mail settings with the information provided by your host.
EOT;
$lang['view_page'] = 'View this page in a new window';
$lang['off'] = 'Off';
$lang['on'] = 'On';
$lang['invalid_test'] = 'Invalid test param value!';
$lang['copy_paste_forum'] = 'View Text Report <em>(suitable for copying into forum posts)</em>';
$lang['permission_information'] = 'Permission Information';
$lang['server_os'] = 'Server Operating System';
$lang['server_api'] = 'Server API';
$lang['server_software'] = 'Server Software';
$lang['server_information'] = 'Server Information';
$lang['session_save_path'] = 'Session Save Path';
$lang['max_execution_time'] = 'Maximum Execution Time';
$lang['gd_version'] = 'GD version';
$lang['upload_max_filesize'] = 'Maximum Upload Size';
$lang['post_max_size'] = 'Maximum Post Size';
$lang['memory_limit'] = 'PHP Effective Memory Limit';
$lang['server_db_type'] = 'Server Database';
$lang['server_db_version'] = 'Server Database Version';
$lang['phpversion'] = 'Current PHP Version';
$lang['safe_mode'] = 'PHP Safe Mode';
$lang['php_information'] = 'PHP Information';
$lang['cms_install_information'] = 'CMS Install Information';
$lang['cms_version'] = 'CMS Version';
$lang['installed_modules'] = 'Installed Modules';
$lang['config_information'] = 'Config Information';
$lang['systeminfo_copy_paste'] = 'Please copy and paste this selected text into your forum posting';
$lang['help_systeminformation'] = <<<EOT
The information displayed below is collected from a variety of locations, and summarized here so that you may be able to conveniently find some of the information required when trying to diagnose a problem or request help with your CMS Made Simple installation.
EOT;
$lang['systeminfo'] = 'System Information';
$lang['systeminfodescription'] = 'Display various pieces of information about your system that may be useful in diagnosing problems';
$lang['welcome_user'] = 'Welcome';
$lang['itsbeensincelogin'] = 'It has been %s since you last logged in';
$lang['days'] = 'days';
$lang['day'] = 'day';
$lang['hours'] = 'hours';
$lang['hour'] = 'hour';
$lang['minutes'] = 'minutes';
$lang['minute'] = 'minute';
$lang['help_css_max_age'] = 'This parameter should be set relatively high for static sites, and should be set to 0 for site development';
$lang['css_max_age'] = 'Maximum amount of time (seconds) stylesheets can be cached in the browser';
$lang['error'] = 'Error';
$lang['clear_version_check_cache'] = 'Clear any cached version check information on submit';
$lang['new_version_available'] = '<em>Notice:</em> A new version of CMS Made Simple is available.  Please notify your administrator.';
$lang['info_urlcheckversion'] = 'If this url is the word &quot;none&quot; no checks will be made.<br/>An empty string will result in a default URL being used.';
$lang['urlcheckversion'] = 'Check for new CMS versions using this URL';
$lang['master_admintheme'] = 'Default Administration Theme (for the login page and new user accounts)';
$lang['contenttype_separator'] = 'Separator';
$lang['contenttype_sectionheader'] = 'Section Header';
$lang['contenttype_link'] = 'External Link';
$lang['contenttype_content'] = 'Content';
$lang['contenttype_pagelink'] = 'Internal Page Link';
$lang['nogcbwysiwyg'] = 'Disallow WYSIWYG editors on global content blocks';
$lang['destination_page'] = 'Destination Page';
$lang['additional_params'] = 'Additional Parameters';
$lang['help_function_current_date'] = <<<EOT
        <h3 style="color: red;">Deprecated</h3>
	 <p>use <code>{\$smarty.now|cms_date_format}</code></p>
	<h3>What does this do?</h3>
	<p>Prints the current date and time.  If no format is given, it will default to a format similar to 'Jan 01, 2004'.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{current_date format="%A %d-%b-%y %T %Z"}</code></p>
	<h3>What parameters does it take?</h3>
	<ul>
		<li><em>(optional)</em>format - Date/Time format using parameters from php's strftime function.  See <a href="http://php.net/strftime" target="_blank">here</a> for a parameter list and information.</li>
		<li><em>(optional)</em>ucword - If true return uppercase the first character of each word.</li>
	</ul>
EOT;
$lang['help_function_valid_xhtml'] = <<<EOT
<h3>What does this do?</h3>
<p>Returns a link to the w3c HTML validator.</p>
<h3>How do I use it?</h3>
<p>Just insert the tag into your template/page like: <code>{valid_xhtml}</code></p>
<h3>What parameters does it take?</h3>
    <ul>
	<li><em>(optional)</em> url         (string)     - The URL used for validation, if none is given http://validator.w3.org/check/referer is used.</li>
	<li><em>(optional)</em> class       (string)     - If set, this will be used as class attribute for the link (a) element</li>
	<li><em>(optional)</em> target      (string)     - If set, this will be used as target attribute for the link (a) element</li>
	<li><em>(optional)</em> image       (true/false) - If set to false, a text link will be used instead of an image/icon.</li>
	<li><em>(optional)</em> text        (string)     - If set, this will be used for the link text or alternate text for the image. Default is 'valid XHTML 1.0 Transitional'.<br /> When an image is used, the given string will also be used for the image alt attribute (by default, this can be overridden by using the 'alt' parameter).</li>
	<li><em>(optional)</em> image_class (string)     - Only if 'image' is not set to false. If set, this will be used as class attribute for the image (img) element</li>
	<li><em>(optional)</em> src         (string)     - Only if 'image' is not set to false. The icon to show. Default is http://www.w3.org/Icons/valid-xhtml10</li>
	<li><em>(optional)</em> width       (string)     - Only if 'image' is not set to false. The image width. Default is 88 (width of http://www.w3.org/Icons/valid-xhtml10)</li>
	<li><em>(optional)</em> height      (string)     - Only if 'image' is not set to false. The image height. Default is 31 (height of http://www.w3.org/Icons/valid-xhtml10)</li>
	<li><em>(optional)</em> alt         (string)     - Only if 'image' is not set to false. The alternate text ('alt' attribute) for the image (element). If none is given the link text will be used.</li>
    </ul>
EOT;
$lang['help_function_valid_css'] = <<<EOT
<h3>What does this do?</h3>
<p>Returns a link to the w3c CSS validator.</p>
<h3>How do I use it?</h3>
<p>Just insert the tag into your template/page like: <code>{valid_css}</code></p>
<h3>What parameters does it take?</h3>
    <ul>
        <li><em>(optional)</em> url         (string)     - The URL used for validation, if none is given http://jigsaw.w3.org/css-validator/check/referer is used.</li>
	<li><em>(optional)</em> class       (string)     - If set, this will be used as class attribute for the link (a) element</li>
	<li><em>(optional)</em> target      (string)     - If set, this will be used as target attribute for the link (a) element</li>
	<li><em>(optional)</em> image       (true/false) - If set to false, a text link will be used instead of an image/icon.</li>
	<li><em>(optional)</em> text        (string)     - If set, this will be used for the link text or alternate text for the image. Default is 'Valid CSS 2.1'.<br /> When an image is used, the given string will also be used for the image alt attribute (by default, this can be overridden by using the 'alt' parameter).</li>
	<li><em>(optional)</em> image_class (string)     - Only if 'image' is not set to false. If set, this will be used as class attribute for the image (img) element</li>
        <li><em>(optional)</em> src         (string)     - Only if 'image' is not set to false. The icon to show. Default is http://jigsaw.w3.org/css-validator/images/vcss</li>
        <li><em>(optional)</em> width       (string)     - Only if 'image' is not set to false. The image width. Default is 88 (width of http://jigsaw.w3.org/css-validator/images/vcss)</li>
        <li><em>(optional)</em> height      (string)     - Only if 'image' is not set to false. The image height. Default is 31 (height of http://jigsaw.w3.org/css-validator/images/vcss)</li>
	<li><em>(optional)</em> alt         (string)     - Only if 'image' is not set to false. The alternate text ('alt' attribute) for the image (element). If none is given the link text will be used.</li>
    </ul>
EOT;
$lang['help_function_title'] = <<<EOT
	<h3>What does this do?</h3>
	<p>Prints the title of the page.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{title}</code></p>
	<h3>What parameters does it take?</h3>
	<p><em>(optional)</em> assign (string) - Assign the results to a smarty variable with that name.</p>
EOT;
$lang['help_function_stylesheet'] = <<<EOT
	<h3>What does this do?</h3>
	<p>Gets stylesheet information from the system.  By default, it grabs all of the stylesheets attached to the current template.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page's head section like: <code>{stylesheet}</code></p>
	<h3>What parameters does it take?</h3>
	<ul>
		<li><em>(optional)</em>name - Instead of getting all stylesheets for the given page, it will only get one spefically named one, whether it's attached to the current template or not.</li>
		<li><em>(optional)</em>media - If name is defined, this allows you set a different media type for that stylesheet.</li>
    <li><em>(optional)</em>templateid - If templateid is defined, this will return stylesheets associated with that template instead of the current one.</li>
	</ul>
EOT;
$lang['help_function_stopexpandcollapse'] = <<<EOT
	<h3>What does this do?</h3>
	<p>Uses Javascript to enable content in an area to be expandable and collapsable on a mouse click.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like:<br />
	<br />
	<code>{startExpandCollapse id="name" title="Click Here"}<br />
	This is all the content the user will see when they click the title "Click Here" above. It will display all the content that is between the {startExpandCollapse} and {stopExpandCollapse} when clicked.<br />
	{stopExpandCollapse}
	</code>
	<br />
	<br />
	Note: If you intend to use this multiple times on a single page each startExpandCollapse tag must have a unique id.</p>
	<h3>What if I want to change the look of the title?</h3>
	<p>The look of the title can be changed via css. The title is wrapped in a div with the id you specify.</p>

	<h3>What parameters does it take?</h3>
	<p>
	<i>startExpandCollapse takes the following parameters</i><br />
	&nbsp; &nbsp;id - A unique id for the expand/collapse section.<br />
	&nbsp; &nbsp;title - The text that will be displayed to expand/collapse the content.<br />
	<i>stopExpandCollapse takes no parameters</i><br />
	</p>
EOT;
$lang['help_function_startexpandcollapse'] = <<<EOT
	<h3>What does this do?</h3>
	<p>Enables content to be expandable and collapsable. Like the following:</p>
	&lt;a href="#expand1" onClick="expandcontent('expand1')" style="cursor:hand; cursor:pointer"&gt;Click here for more info&lt;/a:gt;<span id="expand1" class="expand">&lt;a name="help"&gt; - Here is all the info you will ever need...&lt;/a&gt;</span>

	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{startExpandCollapse id="name" title="Click Here"}</code>. Also, you must use the {stopExpandCollapse} at the end of the collapseable content. Here is an example:<br />
	<br />
	<code>{startExpandCollapse id="name" title="Click Here"}<br />
	This is all the content the user will see when they click the title "Click Here" above. It will display all the content that is between the {startExpandCollapse} and {stopExpandCollapse} when clicked.<br />
	{stopExpandCollapse}
	</code>
	<br />
	<br />
	Note: If you intend to use this multiple times on a single page each startExpandCollapse tag must have a unique id.</p>
	<h3>What if I want to change the look of the title?</h3>
	<p>The look of the title can be changed via css. The title is wrapped in a div with the id you specify.</p>

	<h3>What parameters does it take?</h3>
	<p>
	<i>startExpandCollapse takes the following parameters</i><br />
	&nbsp; &nbsp;id - A unique id for the expand/collapse section.<br />
	&nbsp; &nbsp;title - The text that will be displayed to expand/collapse the content.<br />
	<i>stopExpandCollapse takes no parameters</i><br />
	</p>
EOT;
$lang['help_function_sitemap'] = <<<EOT
    <h3>Notice</h3>
    <p>This plugin is deprecated.  Users should now see the <code>{site_mapper}</code> plugin.</p>
    <h3>What does this do?</h3>
    <p>Prints out a sitemap.</p>
    <h3>How do I use it?</h3>
    <p>Just insert the tag into your template/page like: <code>{sitemap}</code></p>
    <h3>What parameters does it take?</h3>
        <ul>
            <li><em>(optional)</em> <tt>class</tt> - A css_class for the ul-tag which includes the complete sitemap.</li>
            <li><em>(optional)</em> <tt>start_element</tt> - The hierarchy of your element (ie : 1.2 or 3.5.1 for example). This parameter sets the root of the menu. You can use the page alias instead of hierarchy.</li>
            <li><em>(optional)</em> <tt>number_of_levels</tt> - An integer, the number of levels you want to show in your menu. Should be set to 2 using a delimiter.</li>
            <li><em>(optional)</em> <tt>delimiter</tt> - Text to separate entries not on depth 1 of the sitemap (i.e. 1.1, 1.2). This is helpful for showing entries on depth 2 beside each other (using css display:inline).</li>
            <li><em>(optional)</em> <tt>initial 1/0</tt> - If set to 1, begin also the first entries not on depth 1 with a delimiter (i.e. 1.1, 2.1).</li>
            <li><em>(optional)</em> <tt>relative 1/0</tt> - We are not going to show current page (with the sitemap) - we'll show only his childs.</li>
            <li><em>(optional)</em> <tt>showall 1/0</tt> - We are going to show all pages if showall is enabled, else we'll only show pages with active menu entries.</li>
            <li><em>(optional)</em> <tt>add_elements</tt> - A comma separated list of alias names which will be added to the shown pages with active menu entries (showall not enabled).</li>
        </ul>
EOT;
$lang['help_function_adsense'] = <<<EOT
	<h3>What does this do?</h3>
	<p>Google adsense is a popular advertising program for websites. This tag will take the basic parameters that would be provided by the adsense program and puts them in a easy to use tag that makes your templates look much cleaner.  See <a href="http://www.google.com/adsense" target="_blank">here</a> for more details on adsense.</p>
	<h3>How do I use it?</h3>
	<p>First, sign up for a google adsense account and get the parameters for your ad. Then just use the tag in your page/template like so: <code>{adsense ad_client="pub-random#" ad_width="120" ad_height="600" ad_format="120x600_as"}</code></p>
	<h3>What parameters does it take?</h3>
	<p>All parameters are optional, though skipping one might not necessarily made the ad work right.  Options are:</p>
	<ul>
		<li>ad_client - This would be the pub_random# id that would represent your adsense account number</li>
		<li>ad_width - width of the ad</li>
		<li>ad_height - height of the ad</li>
		<li>ad_format - "format" of the ad <em>e.g. 120x600_as</em></li>
		<li>ad_channel - channels are an advanced feature of adsense.  Put it here if you use it.</li>
		<li>ad_slot - slots are an advanced feature of adsense.  Put it here if you use it.</li>
		<li>ad_type - possible options are text, image or text_image.</li>
		<li>color_border - the color of the border. Use HEX color or type the color name (Ex. Red)</li>
		<li>color_link - the color of the linktext. Use HEX color or type the color name (Ex. Red)</li>
		<li>color_url - the color of the URL. Use HEX color or type the color name (Ex. Red)</li>
		<li>color_text - the color of the text. Use HEX color or type the color name (Ex. Red)</li>
	</ul>
EOT;
$lang['help_function_sitename'] = <<<EOT
        <h3>What does this do?</h3>
        <p>Shows the name of the site.  This is defined during install and can be modified in the Global Settings section of the admin panel.</p>
        <h3>How do I use it?</h3>
        <p>Just insert the tag into your template/page like: <code>{sitename}</code></p>
        <h3>What parameters does it take?</h3>
	<p><em>(optional)</em> assign (string) - Assign the results to a smarty variable with that name.</p>
EOT;
$lang['help_function_search'] = <<<EOT
	<h3>What does this do?</h3>
	<p>This is actually just a wrapper tag for the Search module to make the tag syntax easier. 
	Instead of having to use <code>{cms_module module='Search'}</code> you can now just use <code>{search}</code> to insert the module in a template.
	</p>
	<h3>How do I use it?</h3>
	<p>Just put <code>{search}</code> in a template where you want the search input box to appear. For help about the Search module, please refer to the Search module help.</p>
EOT;
$lang['help_function_root_url'] = <<<EOT
	<h3>What does this do?</h3>
	<p>Prints the root url location for the site.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{root_url}</code></p>
	<h3>What parameters does it take?</h3>
	<p>None at this time.</p>
EOT;
$lang['help_function_repeat'] = <<<EOT
  <h3>What does this do?</h3>
  <p>Repeats a specified sequence of characters, a specified number of times</p>
  <h3>How do I use it?</h3>
  <p>Insert a tag similar to the following into your template/page, like this: <code>{repeat string='repeat this ' times='3'}</code></p>
  <h3>What parameters does it take?</h3>
  <ul>
  <li>string='text' - The string to repeat</li>
  <li>times='num' - The number of times to repeat it.</li>
  </ul>
EOT;
$lang['help_function_recently_updated'] = <<<EOT
	<h3>What does this do?</h3>
	<p>Outputs a list of recently updated pages.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{recently_updated}</code></p>
	<h3>What parameters does it take?</h3>
	<ul>
	 <li><p><em>(optional)</em> number='10' - Number of updated pages to show.</p><p>Example: {recently_updated number='15'}</p></li>
 	 <li><p><em>(optional)</em> leadin='Last changed' - Text to show left of the modified date.</p><p>Example: {recently_updated leadin='Last Changed'}</p></li>
 	 <li><p><em>(optional)</em> showtitle='true' - Shows the titleattribute if it exists as well (true|false).</p><p>Example: {recently_updated showtitle='true'}</p></li>											 	
	 <li><p><em>(optional)</em> css_class='some_name' - Warp a div tag with this class around the list.</p><p>Example: {recently_updated css_class='some_name'}</p></li>											 	
	 <li><p><em>(optional)</em> dateformat='d.m.y h:m' - default is d.m.y h:m , use the format you whish (php -date- format)</p><p>Example: {recently_updated dateformat='D M j G:i:s T Y'}</p></li>											 	
	</ul>
	<p>or combined:</p>
	<pre>{recently_updated number='15' showtitle='false' leadin='Last Change: ' css_class='my_changes' dateformat='D M j G:i:s T Y'}</pre>
EOT;
$lang['help_function_print'] = <<<EOT
	<h3>What does this do?</h3>
	<p>This is actually just a wrapper tag for the Printing module to make the tag syntax easier. 
	Instead of having to use <code>{cms_module module='Printing'}</code> you can now just use <code>{print}</code> to insert the module on pages and templates.
	</p>
	<h3>How do I use it?</h3>
	<p>Just put <code>{print}</code> on a page or in a template. For help about the Printing module, what parameters it takes etc., please refer to the Printing module help.</p>
EOT;
$lang['help_function_oldprint'] = <<<EOT
	<h3>What does this do?</h3>
	<p>Creates a link to only the content of the page.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{oldprint}</code><br /></p>
        <h3>What parameters does it take?</h3>
        <ul>
                <li><em>(optional)</em> goback - Set to "true" to show a "Go Back" link on the page to print.</li>
                <li><em>(optional)</em> popup - Set to "true" and page for printing will by opened in new window.</li>
                <li><em>(optional)</em> script - Set to "true" and in print page will by used java script for run print of page.</li>
                <li><em>(optional)</em> showbutton - Set to "true" and will show a printer graphic instead of a text link.</li>
                <li><em>(optional)</em> class - class for the link, defaults to "noprint".</li>
                <li><em>(optional)</em> text - Text to use instead of "Print This Page" for the print link.</li>
                <li><em>(optional)</em> title - Text to show for title attribute. If blank show text parameter.</li>
                <li><em>(optional)</em> more - Place additional options inside the &lt;a&gt; link.</li>
                <li><em>(optional)</em> src_img - Show this image file. Default images/cms/printbutton.gif.</li>
                <li><em>(optional)</em> class_img - Class of &lt;img&gt; tag if showbutton is sets.</li>
        </ul>
                    <p>Example:</p>
                     <pre>{oldprint text="Printable Page"}</pre>      

EOT;
$lang['login_info_title'] = 'Information';
$lang['login_info'] = 'For the Admin console to work properly';
$lang['login_info_params'] = <<<EOT
<ol> 
  <li>Cookies must be enabled in your browser</li> 
  <li>Javascript must be enabled in your browser</li> 
  <li>Popup windows must be allowed for the following address:</li> 
</ol>
EOT;

$lang['help_function_news'] = <<<EOT
	<h3>What does this do?</h3>
	<p>This is actually just a wrapper tag for the News module to make the tag syntax easier. 
	Instead of having to use <code>{cms_module module='News'}</code> you can now just use <code>{news}</code> to insert the module on pages and templates.
	</p>
	<h3>How do I use it?</h3>
	<p>Just put <code>{news}</code> on a page or in a template. For help about the News module, what parameters it takes etc., please refer to the News module help.</p>
EOT;
$lang['help_function_modified_date'] = <<<EOT
        <h3>What does this do?</h3>
        <p>Prints the date and time the page was last modified.  If no format is given, it will default to a format similar to 'Jan 01, 2004'.</p>
        <h3>How do I use it?</h3>
        <p>Just insert the tag into your template/page like: <code>{modified_date format="%A %d-%b-%y %T %Z"}</code></p>
        <h3>What parameters does it take?</h3>
        <ul>
                <li><em>(optional)</em>format - Date/Time format using parameters from php's strftime function.  See <a href="http://php.net/strftime" target="_blank">here</a> for a parameter list and information.</li>
        </ul>
EOT;
$lang['help_function_metadata'] = <<<EOT
	<h3>What does this do?</h3>
	<p>Displays the metadata for this page. Both global metdata from the global settings page and metadata for each page will be shown.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template like: <code>{metadata}</code></p>
	<h3>What parameters does it take?</h3>
	<ul>
		<li><em>(optional)</em>showbase (true/false) - If set to false, the base tag will not be sent to the browser.  Defaults to true if use_hierarchy is set to true in config.php.</li>
	</ul>
EOT;
$lang['help_function_menu_text'] = <<<EOT
	<h3>What does this do?</h3>
	<p>Prints the menu text of the page.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{menu_text}</code></p>
	<h3>What parameters does it take?</h3>
	<p>None at this time.</p>
EOT;
$lang['help_function_menu'] = <<<EOT
	<h3>What does this do?</h3>
	<p>This is actually just a wrapper tag for the Menu Manager module to make the tag syntax easier. 
	Instead of having to use <code>{cms_module module='MenuManager'}</code> you can now just use <code>{menu}</code> to insert the module on pages and templates.
	</p>
	<h3>How do I use it?</h3>
	<p>Just put <code>{menu}</code> on a page or in a template. For help about the Menu Manager module, what parameters it takes etc., please refer to the Menu Manager module help.</p>
EOT;
$lang['help_function_last_modified_by'] = <<<EOT
        <h3>What does this do?</h3>
        <p>Prints last person that edited this page.  If no format is given, it will default to a ID number of user .</p>
        <h3>How do I use it?</h3>
        <p>Just insert the tag into your template/page like: <code>{last_modified_by format="fullname"}</code></p>
        <h3>What parameters does it take?</h3>
        <ul>
                <li><em>(optional)</em>format - id, username, fullname</li>
        </ul>
EOT;
$lang['help_function_image'] = <<<EOT
  <h3>What does this do?</h3>
  <p>Creates an image tag to an image stored within your images directory</p>
  <h3>How do I use it?</h3>
  <p>Just insert the tag into your template/page like: <code>{image src="something.jpg"}</code></p>
  <h3>What parameters does it take?</h3>
  <ul>
     <li><em>(required)</em>  <tt>src</tt> - Image filename within your images directory.</li>
     <li><em>(optional)</em>  <tt>width</tt> - Width of the image within the page. Defaults to true size.</li>
     <li><em>(optional)</em>  <tt>height</tt> - Height of the image within the page. Defaults to true size.</li>
     <li><em>(optional)</em>  <tt>alt</tt> - Alt text for the image -- needed for xhtml compliance. Defaults to filename.</li>
     <li><em>(optional)</em>  <tt>class</tt> - CSS class for the image.</li>
     <li><em>(optional)</em>  <tt>title</tt> - Mouse over text for the image. Defaults to Alt text.</li>
     <li><em>(optional)</em>  <tt>addtext</tt> - Additional text to put into the tag</li>
  </ul>
EOT;
$lang['help_function_imagegallery'] = <<<EOT
	<h3>What does this do?</h3>
	<p>Creates a gallery out of a folder of images (.gif, .jpg or .png). 
	You can click on a thumbnail image to view the bigger image. It can use 
	captions which are based on the image name, minus the file extension. It 
	follows web standards and uses CSS for formatting. There are classes 
	for various elements and for the surrounding 'div'. Check out the CSS below for
	more information.</p>

	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template or page like: </p>
	<code>{ImageGallery picFolder="uploads/images/yourfolder/"}</code>
	<p>Where picFolder is the folder where your images are stored.</p>
	
    <h3>What parameters does it take?</h3>
    <p>It can take quite a few parameters, but the example above is probably 
good for most people :) </p>
        <ol>
		<li><strong>picFolder e.g. picFolder="uploads/images/yourfolder/"</strong><br/>
		Is the path to the gallery (yourfolder) ending in'/'. So you can have 
		lots of directories and lots of galleries.</li>

		<li><strong>type e.g. type="click" or type="popup"</strong><br/>
		For the "popup" function to work you need to include the popup javascript into
		the head of your template e.g. "&lt;head&gt;&lt;/head&gt;". The javascript is at
		the bottom of this page! <em>The default is 'click'.</em></li>

		<li><strong>divID e.g. divID ="imagegallery"</strong><br/>
		Sets the wrapping 'div id' around your gallery so that you can have 
		different CSS for each gallery. <em>The default is 'imagegallery'.</em></li>

		<li><strong>sortBy e.g. sortBy = "name" or sortBy = "date"</strong><br/>
		Sort images by 'name' OR 'date'. <em>No default.</em></li>

		<li><strong>sortByOrder e.g. sortByOrder = "asc" or sortByOrder = "desc"</strong><br/> 
		 <em>No default.</em>.</li>

		<li>This sets caption above the big (clicked on) image<br/>
		<strong>bigPicCaption = "name" </strong>(filename excluding extension)<em> or </em><br/>
		<strong>bigPicCaption = "file" </strong>(filename including extension)<em> or </em><br/>
		<strong>bigPicCaption = "number" </strong>(a number sequence)<em> or </em><br/>
		<strong>bigPicCaption = "none" </strong>(No caption)<br/>
		<em>The Default is "name". </em></li>

		<li>This sets the caption below the small thumbnail<br/>
		<strong>thumbPicCaption = "name"</strong> (filename excluding extension)<em> or </em><br/>
		<strong>thumbPicCaption = "file"</strong> (filename including extension)<em> or </em><br/>
		<strong>thumbPicCaption = "number" </strong>(a number sequence)<em> or </em><br/>
		<strong>thumbPicCaption = "none" </strong>(No caption)<br/>
		<em>The Default is "name".</em></li>

		<li>Sets the 'alt' tag for the big image - compulsory.<br/>
		<strong>bigPicAltTag = "name" </strong>(filename excluding extension)<em> or </em><br/>
		<strong>bigPicAltTag = "file" </strong>(filename including extension)<em> or </em><br/>
		<strong>bigPicAltTag = "number" </strong>(a number sequence)<br/>
		<em>The Default is "name".</em></li>

		<li> Sets the 'title' tag for the big image. <br/>
		<strong>bigPicTitleTag = "name" </strong>(filename excluding extension)<em> or </em><br/>
		<strong>bigPicTitleTag = "file" </strong>(filename including extension)<em> or </em><br/>
		<strong>bigPicTitleTag = "number" </strong>(a number sequence)<em> or </em><br/>
		<strong>bigPicTitleTag = "none" </strong>(No title)<br/>
		<em>The Default is "name".</em></li>

		<li><strong>thumbPicAltTag</strong><br/>
		<em>Is the same as bigPicAltTag, but for the small thumbnail images.</em></li>

		<li><strong>thumbPicTitleTag *</strong><br/>
		<em>Is the same as bigPicTitleTag but for the small thumbnail images.<br/>
		<strong>*Except that after the options you have '... click for a bigger image' 
		or if you do not set this option then you get the default of 
		'Click for a bigger image...'</strong></em></li>
        </ol>
  <p>A More Complex Example</p>
        <p>'div id' is 'cdcovers', no Caption on big images, thumbs have default caption. 
        'alt' tags for the big image are set to the name of the image file without the extension 
        and the big image 'title' tag is set to the same but with an extension. 
        The thumbs have the default 'alt' and 'title' tags. The default being the name 
        of the image file without the extension for 'alt' and 'Click for a bigger image...' for the 'title',
		would be:</p>
		<code>{ImageGallery picFolder="uploads/images/cdcovers/" divID="cdcovers" bigPicCaption="none"  bigPicAltTag="name" bigPicTitleTag="file"}</code>
        <br/>
		<p>It's got lots of options but I wanted to keep it very flexible and you don't have to set them, the defaults are sensible.</p>
		
  <br/>
	<h4>Example CSS</h4>
<pre>
	/* Image Gallery - Small Thumbnail Images */
	.thumb {
		margin: 1em 1em 1.6em 0; /* Space between images */
		padding: 0;
		float: left;
		text-decoration: none;
		line-height: normal;
		text-align: left;
	}

	.thumb img, .thumb a img, .thumb a:link img{ /* Set link formatting*/
		width: 100px; /* Image width*/
		height: 100px; /* Image height*/
		display: inline;
		padding: 12px; /* Image padding to form photo frame */
		/* You can set the above to 0px = no frame - but no hover indication! Adjust other widths ot text!*/
		margin: 0;
		background-color: white; /*Background of photo */ 
		border-top: 1px solid #eee; /* Borders of photo frame */
		border-right: 2px solid #ccc;
		border-bottom: 2px solid #ccc;
		border-left: 1px solid #eee;
		text-decoration: none;
	}

	.thumb a:visited img {
		background-color: #eee; /*Background of photo on hover - sort of a light grey */
	}

	.thumb a:hover img {
		background-color: #dae6e4; /*Background of photo on hover - sort of light blue/green */
	}

	.thumbPicCaption {
		text-align: center;
		font-size: smaller;
		margin: 0 1px 0 0;
		padding: 0;
		width: 124px; /* Image width plus 2 x padding for image (photo frame) - to center text on image */
		/* display: none;  if you do not want to display this text */
	}

	/* Image Gallery - Big Images */
	.bigPic {
		margin: 10px 0 5px 0;
		padding: 0;
		line-height: normal;
	}

	.bigPicCaption { /*Big Image Name - above image above .bigpicImageFileName (Without extension) */
		text-align: center;
		font-weight: bold;
		font-variant: small-caps;
		font-weight: bold;
		margin: 0 1px 0 0;
		padding: 0;
		width: 386px; /* Image width plus 2 x padding for image (photo frame) - to center text on image */
		/* display: none;  if you do not want to display this text */
	}

	.bigPic img{ /* Big Image settings */
		width: 350px; /* Width of Big Image */
			height: auto;
		display: inline;
		padding: 18px; /* Image padding to form photo frame. */
		/* You can set the above to 0px = no frame - but no hover indication! Adjust other widths ot text!*/
		margin: 0;
		background-color: white; /* Background of photo */ 
		border-top: 1px solid #eee; /* Borders of photo frame */
		border-right: 2px solid #ccc; 
		border-bottom: 2px solid #ccc;
		border-left: 1px solid #eee;
		text-decoration: none; 
		text-align: left;
	}

	.bigPicNav { /* Big Image information: 'Image 1 of 4' and gallery navigation */
		margin: 0;
		width: 386px; /* Image width plus 2 x padding for image (photo frame) - to center text on image */
		padding: 0;
		color: #000;
		font-size: smaller;
		line-height: normal;
		text-align: center;
		/* display: none;  if you do not want to display this text. Why? You Lose Navigation! */
	}

</pre>
<br/>

	<h4>The popup javascript is now included in plugin code and will be generated automatically if you still have javascript in your template please remove it.</h4>
EOT;
$lang['help_function_html_blob'] = <<<EOT
	<h3>What does this do?</h3>
	<p>See the help for global_content for a description.</p>
EOT;
$lang['help_function_googlepr'] = <<<EOT
	<h3>What does this do?</h3>
	<p>Display's a number that represents your google pagerank.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{googlepr}</code><br /></p>

	<h3>What parameters does it take?</h3>
	<ul>
		<li><em>(optional)</em> domain - The website to display the pagerank for.</li>
	</ul>
EOT;
$lang['help_function_google_search'] = <<<EOT
	<h3>What does this do?</h3>
	<p>Search's your website using Google's search engine.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{google_search}</code><br />
	<br />
	Note: Google needs to have your website indexed for this to work. You can submit your website to google <a href="http://www.google.com/addurl.html">here</a>.</p>
	<h3>What if I want to change the look of the textbox or button?</h3>
	<p>The look of the textbox and button can be changed via css. The textbox is given an id of textSearch and the button is given an id of buttonSearch.</p>

	<h3>What parameters does it take?</h3>
	<ul>
		<li><em>(optional)</em> domain - This tells google the website domain to search. This script tries to determine this automatically.</li>
		<li><em>(optional)</em> buttonText - The text you want to display on the search button. The default is "Search Site".</li>
	</ul>
EOT;
$lang['help_function_global_content'] = <<<EOT
	<h3>What does this do?</h3>
	<p>Inserts a global content block into your template or page.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{global_content name='myblob'}</code>, where name is the name given to the block when it was created.</p>
	<h3>What parameters does it take?</h3>
	<ul>
  	  <li>name - The name of the global content block to display.</li>
          <li><em>(optional)</em> assign - The name of a smarty variable that the global content block should be assigned to.</li>
	</ul>
EOT;
$lang['help_function_get_template_vars'] = <<<EOT
	<h3>What does this do?</h3>
	<p>Dumps all the known smarty variables into your page</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{get_template_vars}</code></p>
	<h3>What parameters does it take?</h3>
											  <p>None at this time</p>
EOT;
$lang['help_function_embed'] = <<<EOT
	<h3>What does this do?</h3>
	<p>Enable inclusion (embeding) of any other application into the CMS. The most usual use could be a forum. 
	This implementation is using IFRAMES so older browsers can have problems. Sorry bu this is the only known way 
	that works without modifing the embeded application.</p>
	<h3>How do I use it?</h3>
        <ul>
        <li>a) Add <code>{embed header=true}</code> into the head section of your page template, or into the metadata section in the options tab of a content page.  This will ensure that the required javascript gets included.   If you insert this tag into the metadata section in the options tab of a content page you must ensure that <code>{metadata}</code> is in your page template.</li>
        <li>b) Add <code>{embed url="http://www.google.com"}</code> into your page content or in the body of your page template.</li>
        </ul>
        <br/>
        <h4>Example to make the iframe larger</h4>
	<p>Add the following to your style sheet:</p>
        <pre>#myframe { height: 600px; }</pre>
        <br/>
        <h3>What parameters does it take?</h3>
        <ul>
            <li><em>(required)</em>url - the url to be included</li> 
            <li><em>(required)</em>header=true - this will generate the header code for good resizing of the IFRAME.</li>
            <li>(optional)name - an optional name to use for the iframe (instead of myframe).<p>If this option is used, it must be used identically in both calls, i.e: {embed header=true name=foo} and {embed name=foo url=http://www.google.com} calls.</p></li>
        </ul>
EOT;
$lang['help_function_edit'] = <<<EOT
	<h3>What does this do?</h3>
	<p>Creates a link to edit the page</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{edit}</code><br /></p>
        <h3>What parameters does it take?</h3>
        <ul>
                <li><em>(optional)</em>showbutton - Set to "true" and will show a edit graphic instead of a text link.</li>
        </ul>
EOT;
$lang['help_function_description'] = <<<EOT
	<h3>What does this do?</h3>
	<p>Prints the description (title attribute) of the page.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{description}</code></p>
	<h3>What parameters does it take?</h3>
	<p>None at this time.</p>
EOT;
$lang['help_function_created_date'] = <<<EOT
        <h3>What does this do?</h3>
        <p>Prints the date and time the page was created.  If no format is given, it will default to a format similar to 'Jan 01, 2004'.</p>
        <h3>How do I use it?</h3>
        <p>Just insert the tag into your template/page like: <code>{created_date format="%A %d-%b-%y %T %Z"}</code></p>
        <h3>What parameters does it take?</h3>
        <ul>
                <li><em>(optional)</em>format - Date/Time format using parameters from php's strftime function.  See <a href="http://php.net/strftime" target="_blank">here</a> for a parameter list and information.</li>
        </ul>
EOT;
$lang['help_function_content'] = <<<EOT
	<h3>What does this do?</h3>
	<p>This is where the content for your page will be displayed. It's inserted into the template and changed based on the current page being displayed.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template like: <code>{content}</code>.</p>
	<p><strong>The default block <code>{content}</code> is required for proper working. (so without the block-parameter)</strong> To give the block a specific label, use the label-parameter. Additional blocks can be added by using the block-parameter.</p>
	<h3>What parameters does it take?</h3>
	<ul>
		<li><em>(optional) </em>block - Allows you to have more than one content block per page. When multiple content tags are put on a template, that number of edit boxes will be displayed when the page is edited.
<p>Example:</p>
<pre>{content block="second_content_block" label="Second Content Block"}</pre>
<p>Now, when you edit a page there will a textarea called "Second Content Block".</p></li>
		<li><em>(optional) </em>wysiwyg (true/false) - If set to false, then a wysiwyg will never be used while editing this block. If true, then it acts as normal.  Only works when block parameter is used.</li>
		<li><em>(optional) </em>oneline (true/false) - If set to true, then only one edit line will be shown while editing this block. If false, then it acts as normal.  Only works when block parameter is used.</li>
<li><em>(optional) </em>size - Applicable only when the oneline option is used this optional parameter allows you to specify the size of the edit field.  The default value is 50.</li>
		<li><em>(optional) </em>default - Allows you to specify default content for this content blocks (additional content blocks only).</li>
		<li><em>(optional) </em>label - Allows specifying a label for display in the edit content page.</li>
		<li><em>(optional) </em>assign - Assigns the content to a smarty parameter, which you can then use in other areas of the page, or use to test whether content exists in it or not.
<p>Example of passing page content to a User Defined Tag as a parameter:</p></li>
<pre>
         {content assign=pagecontent}
         {table_of_contents thepagecontent="\$pagecontent"}
</pre>
</li>
	</ul>
EOT;

$lang['help_function_contact_form'] = <<<EOT
  <h2>NOTE: This plugin is deprecated</h2>
  <h3>This plugin has been removed as of CMS made simple version 1.5</h3>
EOT;

$lang['help_function_cms_versionname'] = <<<EOT
	<h3>What does this do?</h3>
	<p>This tag is used to insert the current version name of CMS into your template or page.  It doesn't display any extra besides the version name.</p>
	<h3>How do I use it?</h3>
	<p>This is just a basic tag plugin.  You would insert it into your template or page like so: <code>{cms_versionname}</code></p>
	<h3>What parameters does it take?</h3>
	<p>It takes no parameters.</p>
EOT;

$lang['help_function_cms_version'] = <<<EOT
	<h3>What does this do?</h3>
	<p>This tag is used to insert the current version number of CMS into your template or page.  It doesn't display any extra besides the version number.</p>
	<h3>How do I use it?</h3>
	<p>This is just a basic tag plugin.  You would insert it into your template or page like so: <code>{cms_version}</code></p>
	<h3>What parameters does it take?</h3>
	<p>It takes no parameters.</p>
EOT;

$lang['about_function_cms_selflink'] = <<<EOT
		<p>Author: Ted Kulp &lt;tedkulp@users.sf.net&gt;</p>
		<p>Version: 1.1</p>
		<p>Modified: Martin B. Vestergaard &lt;mbv@nospam.dk&gt;</p>
		<p>Version: 1.41</p>
		<p>Modified: Russ Baldwin</p>
		<p>Version: 1.42</p>
		<p>Modified: Marcus Bointon &lt;coolbru@users.sf.net&gt;</p>
		<p>Version: 1.43</p>
		<p>Modified: Tatu Wikman &lt;tsw@backspace.fi&gt;</p>
		<p>Version: 1.44</p>
		<p>Modified: Hans Mogren &lt;http://hans.bymarken.net/&gt;</p>
		<p>Version: 1.45</p>

		<p>
		Change History:<br/>
		1.46 - Fixes a problem with too many queries when using the dir=start option.<br/>
		1.45 - Added a new option for &quot;dir&quot;, &quot;up&quot;, for links to the parent page e.g. dir=&quot;up&quot; (Hans Mogren).<br />
		1.44 - Added new parameters &quot;ext&quot; and &quot;ext_info&quot; to allow external links with class=&quot;external&quot; and info text after the link, ugly hack but works thinking about rewriting this(Tatu Wikman)<br />
		1.43 - Added new parameters &quot;image&quot; and &quot;imageonly&quot; to allow attachment of images to be used for page links, either instead of or in addition to text links. (Marcus Bointon)<br />
		1.42 - Added new parameter &quot;anchorlink&quot; and a new option for &quot;dir&quot; namely, &quot;anchor&quot;, for internal page links. e.g. dir=&quot;anchor&quot; anchorlink=&quot;internal_link&quot;. (Russ)<br />
		1.41 - added new parameter &quot;href&quot; (LeisureLarry)<br />
		1.4 - fixed bug next/prev linking to non-content pages. (Thanks Teemu Koistinen for this fix)<br />
		1.3 - added option &quot;more&quot;<br />
		1.2 - by Martin B. Vestergaard
		<ul>
		<li>changed default text to Page Name (was Page Alias)</li>
		<li>added option dir=next/prev to display next or previous item in the hirachy - thanks to 100rk</li>
		<li>added option class to add a class= statement to the a-tag.</li>
		<li>added option menu to display menu-text in sted of Page Name</li>
		<li>added option lang to display link-labels in different languages</li>
		</ul>
		1.1 - Changed to new content system<br />
		1.0 - Initial release
		</p>
EOT;
$lang['help_function_cms_selflink'] = <<<EOT
		<h3>What does this do?</h3>
		<p>Creates a link to another CMSMS content page inside your template or content. Can also be used for external links with the ext parameter.</p>
		<h3>How do I use it?</h3>
		<p>Just insert the tag into your template/page like: <code>{cms_selflink page=&quot;1&quot;}</code> or  <code>{cms_selflink page=&quot;alias&quot;}</code></p>
		<h3>What parameters does it take?</h3>
		<ul>
		<li><em>(optional)</em> <tt>page</tt> - Page ID or alias to link to.</li>
		<li><em>(optional)</em> <tt>dir anchor (internal links)</tt> - New option for an internal page link. If this is used then <tt>anchorlink</tt> should be set to your link. </li> <!-- Russ - 25-04-2006 -->
		<li><em>(optional)</em> <tt>anchorlink</tt> - New paramater for an internal page link. If this is used then <tt>dir =&quot;anchor&quot;</tt> should also be set. No need to add the #, because it is added automatically.</li> <!-- Russ - 25-04-2006 -->
		<li><em>(optional)</em> <tt>urlparam</tt> - Specify additional parameters to the URL.  <strong>Do not use this in conjunction with the <em>anchorlink</em> parameter</strong></li>
		<li><em>(optional)</em> <tt>tabindex =&quot;a value&quot;</tt> - Set a tabindex for the link.</li> <!-- Russ - 22-06-2005 -->
		<li><em>(optional)</em> <tt>dir start/next/prev/up (previous)</tt> - Links to the default start page or the next or previous page, or the parent page (up). If this is used <tt>page</tt> should not be set.</li> <!-- mbv - 21-06-2005 -->
		</ul>
		<strong>Note!</strong> Only one of the above may be used in the same cms_selflink statement!!
		<ul>
		<li><em>(optional)</em> <tt>text</tt> - Text to show for the link.  If not given, the Page Name is used instead.</li>
		<li><em>(optional)</em> <tt>menu 1/0</tt> - If 1 the Menu Text is used for the link text instead of the Page Name</li> <!-- mbv - 21-06-2005 -->
		<li><em>(optional)</em> <tt>target</tt> - Optional target for the a link to point to.  Useful for frame and javascript situations.</li>
		<li><em>(optional)</em> <tt>class</tt> - Class for the &lt;a&gt; link. Useful for styling the link.</li> <!-- mbv - 21-06-2005 -->
		<li><em>(optional)</em> <tt>lang</tt> - Display link-labels  (&quot;Next Page&quot;/&quot;Previous Page&quot;) in different languages (0 for no label.) Danish (dk), English (en) or French (fr), for now.</li> <!-- mbv - 21-06-2005 -->
		<li><em>(optional)</em> <tt>id</tt> - Optional css_id for the &lt;a&gt; link.</li> <!-- mbv - 29-06-2005 -->
		<li><em>(optional)</em> <tt>more</tt> - place additional options inside the &lt;a&gt; link.</li> <!-- mbv - 29-06-2005 -->
		<li><em>(optional)</em> <tt>label</tt> - Label to use in with the link if applicable.</li>
		<li><em>(optional)</em> <tt>label_side left/right</tt> - Side of link to place the label (defaults to "left").</li>
		<li><em>(optional)</em> <tt>title</tt> - Text to use in the title attribute.  If none is given, then the title of the page will be used for the title.</li>
		<li><em>(optional)</em> <tt>rellink 1/0</tt> - Make a relational link for accessible navigation.  Only works if the dir parameter is set and should only go in the head section of a template.</li>
		<li><em>(optional)</em> <tt>href</tt> - If href is used only the href value is generated (no other parameters possible). <strong>Example:</strong> &lt;a href=&quot;{cms_selflink href=&quot;alias&quot;}&quot;&gt;&lt;img src=&quot;&quot;&gt;&lt;/a&gt;</li>
		<li><em>(optional)</em> <tt>image</tt> - A url of an image to use in the link. <strong>Example:</strong> {cms_selflink dir=&quot;next&quot; image=&quot;next.png&quot; text=&quot;Next&quot;}</li>
		<li><em>(optional)</em> <tt>alt</tt> - Alternative text to be used with image (alt="" will be used if no alt parameter is given).</li>
		<li><em>(optional)</em> <tt>imageonly</tt> - If using an image, whether to suppress display of text links. If you want no text in the link at all, also set lang=0 to suppress the label. <strong>Example:</strong> {cms_selflink dir=&quot;next&quot; image=&quot;next.png&quot; text=&quot;Next&quot; imageonly=1}</li>
		<li><em>(optional)</em> <tt>ext</tt> - For external links, will add class=&quot;external and info text. <strong>warning:</strong> only text, target and title parameters are compatible with this parameter</li>
		<li><em>(optional)</em> <tt>ext_info</tt> - Used together with &quot;ext&quot; defaults to (external link).</li>
        <li><em>(optional)</em> <tt>assign</tt> - Assign the results to the named smarty variable.</li>
		</ul>
EOT;

$lang['about_function_cms_module'] = <<<EOT
	<p>Author: Ted Kulp&lt;tedkulp@users.sf.net&gt;</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	None
	</p>
EOT;
$lang['help_function_cms_module'] = <<<EOT
	<h3>What does this do?</h3>
	<p>This tag is used to insert modules into your templates and pages. If a module is created to be used as a tag plugin (check it's help for details), then you should be able to insert it with this tag.</p>
	<h3>How do I use it?</h3>
	<p>It's just a basic tag plugin.  You would insert it into your template or page like so: <code>{cms_module module="somemodulename"}</code></p>
	<h3>What parameters does it take?</h3>
	<p>There is only one required parameter.  All other parameters are passed on to the module.</p>
	<ul>
		<li>module - Name of the module to insert.  This is not case sensitive.</li>
	</ul>
EOT;

$lang['about_function_breadcrumbs'] = <<<EOT
<p>Author: Marcus Deglos &lt;<a href="mailto:md@zioncore.com">md@zioncore.com</a>&gt;</p>
<p>Version: 1.7</p>
<p>
Change History:<br/>
1.1 - Modified to use new content rewrite (wishy)<br />
1.2 - Added parameters: delimiter, initial, and root (arl)<br />
1.3 - Added parameter: classid (tdh / perl4ever)<br />
1.4 - Added parameter currentclassid and fixed some bugs (arl)<br />
1.5 - Modified to use new hierarchy manager<br />
1.6 - Modified to skip any parents that are marked to be "not shown in menu" except for root<br />
1.7 - Added root_url parameter (elijahlofgren)<br />
</p>
EOT;

$lang['help_function_breadcrumbs'] = <<<EOT
<h3>What does this do?</h3>
<p>Prints a breadcrumb trail .</p>
<h3>How do I use it?</h3>
<p>Just insert the tag into your template/page like: <code>{breadcrumbs}</code></p>
<h3>What parameters does it take?</h3>
<ul>
<li><em>(optional)</em> <tt>delimiter</tt> - Text to seperate entries in the list (default "&gt;&gt;").</li>
<li><em>(optional)</em> <tt>initial</tt> - 1/0 If set to 1 start the breadcrumbs with a delimiter (default 0).</li>
<li><em>(optional)</em> <tt>root</tt> - Page alias of a page you want to always appear as the first page in
    the list. Can be used to make a page (e.g. the front page) appear to be the root of everything even though it is not.</li>
<li><em>(optional)</em> <tt>root_url</tt> - Override the URL of the root page. Useful for making link be to '/' instead of '/home/'. This requires that the root page be set as the default page.</li>
<li><em>(optional)</em> <tt>classid</tt> - The CSS class for the non current page names, i.e. the first n-1 pages in the list. If the name is a link it is added to the &lt;a href&gt; tags, otherwise it is added to the &lt;span&gt; tags.</li>
<li><em>(optional)</em> <tt>currentclassid</tt> - The CSS class for the &lt;span&gt; tag surrounding the current page name.</li>
<li><em>(optional)</em> <tt>starttext</tt> - Text to append to the front of the breadcrumbs list, something like &quot;You are here&quot;.</li>
</ul>
EOT;

$lang['about_function_anchor'] = <<<EOT
	<p>Author: Ted Kulp&lt;tedkulp@users.sf.net&gt;</p>
	<p>Version: 1.1</p>
	<p>
	Change History:<br/>
	<strong>Update to version 1.1 from 1.0</strong> <em>2006/07/19</em><br/>
	Russ added the means to insert a title, a tabindex and a class for the anchor link. Westis added accesskey and changed parameter names to not include 'anchorlink'.<br/>
	</hr>
	</p>
EOT;
$lang['help_function_anchor'] = <<<EOT
	<h3>What does this do?</h3>
	<p>Makes a proper anchor link.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{anchor anchor='here' text='Scroll Down'}</code></p>
	<h3>What parameters does it take?</h3>
	<ul>
	<li><tt>anchor</tt> - Where we are linking to.  The part after the #.</li>
	<li><tt>text</tt> - The text to display in the link.</li>
	<li><tt>class</tt> - The class for the link, if any</li>
	<li><tt>title</tt> - The title to display for the link, if any.</li>
	<li><tt>tabindex</tt> - The numeric tabindex for the link, if any.</li>
	<li><tt>accesskey</tt> - The accesskey for the link, if any.</li>
	<li><em>(optional)</em> <tt>onlyhref</tt> - Only display the href and not the entire link. No other options will work</li>
	</ul>
EOT;

$lang['help_function_site_mapper'] = <<<EOT
<h3>What does this do?</h3>
  <p>This is actually just a wrapper tag for the Menu Manager module to make the tag syntax easier, and to simplify creating a sitemap.</p>
<h3>How do I use it?</h3>
  <p>Just put <code>{site_mapper}</code> on a page or in a template. For help about the Menu Manager module, what parameters it takes etc., please refer to the Menu Manager module help.</p>
  <p>By default, if no template option is specified the minimal_menu.tpl file will be used.</p>
  <p>Any parameters used in the tag are available in the menumanager template as <code>{\$menuparams.paramname}</code></p>
EOT;

$lang['help_function_redirect_url'] = <<<EOT
<h3>What does this do?</h3>
  <p>This plugin allows you to easily redirect to a specified url.  It is handy inside of smarty conditional logic (for example, redirect to a splash page if the site is not live yet).</p>
<h3>How do I use it?</h3>
<p>Simply insert this tage into your page or template: <code>{redirect_url to='http://www.cmsmadesimple.org'}</code></p>
EOT;
$lang['help_function_redirect_page'] = <<<EOT
<h3>What does this do?</h3>
 <p>This plugin allows you to easily redirect to another page.  It is handy inside of smarty conditional logic (for example, redirect to a login page if the user is not logged in.)</p>
<h3>How do I use it?</h3>
<p>Simply insert this tage into your page or template: <code>{redirect_page page='some-page-alias'}</code></p>
EOT;

$lang['of'] = 'of';
$lang['first'] = 'First';
$lang['last'] = 'Last';
$lang['adminspecialgroup'] = 'Warning: Members of this group automatically have all permissions';
$lang['disablesafemodewarning'] = 'Disable admin safe mode warning';
$lang['allowparamcheckwarnings'] = 'Allow parameter checks to create warning messages';
$lang['date_format_string'] = 'Date Format String';
$lang['date_format_string_help'] = '<em>strftime</em> formatted date format string.  Try googling \'strftime\'';
$lang['last_modified_at'] = 'Last modified at';
$lang['last_modified_by'] = 'Last modified by';
$lang['read'] = 'Read';
$lang['write'] = 'Write';
$lang['execute'] = 'Execute';
$lang['group'] = 'Group';
$lang['other'] = 'Other';
$lang['event_desc_moduleupgraded'] = 'Sent after a module is upgraded';
$lang['event_desc_moduleinstalled'] = 'Sent after a module is installed';
$lang['event_desc_moduleuninstalled'] = 'Sent after a module is uninstalled';
$lang['event_desc_edituserdefinedtagpost'] = 'Sent after a user defined tag is updated';
$lang['event_desc_edituserdefinedtagpre'] = 'Sent prior to a user defined tag update';
$lang['event_desc_deleteuserdefinedtagpre'] = 'Sent prior to deleting a user defined tag';
$lang['event_desc_deleteuserdefinedtagpost'] = 'Sent after a user defined tag is deleted';
$lang['event_desc_adduserdefinedtagpost'] = 'Sent after a user defined tag is inserted';
$lang['event_desc_adduserdefinedtagpre'] = 'Sent prior to a user defined tag insert';
$lang['global_umask'] = 'File Creation Mask (umask)';
$lang['errorcantcreatefile'] = 'Could not create a file (permissions problem?)';
$lang['errormoduleversionincompatible'] = 'Module is incompatible with this version of CMS';
$lang['errormodulenotloaded'] = 'Internal error, the module has not been instantiated';
$lang['errormodulenotfound'] = 'Internal error, could not find the instance of a module';
$lang['errorinstallfailed'] = 'Module installation failed';
$lang['errormodulewontload'] = 'Problem instantiating an available module';
$lang['frontendlang'] = 'Default language for the frontend';
$lang['info_edituser_password'] = 'Change this field to change the user\'s password';
$lang['info_edituser_passwordagain'] = 'Change this field to change the user\'s password';
$lang['originator'] = 'Originator';
$lang['module_name'] = 'Module Name';
$lang['event_name'] = 'Event Name';
$lang['event_description'] = 'Event Description';
$lang['error_delete_default_parent'] = 'You cannot delete the parent of the default page.';
$lang['jsdisabled'] = 'Sorry, this function requires that you have Javascript enabled.';
$lang['order'] = 'Order';
$lang['reorderpages'] = 'Reorder Pages';
$lang['reorder'] = 'Reorder';
$lang['page_reordered'] = 'Page was successfully reordered.';
$lang['pages_reordered'] = 'Pages were successfully reordered';
$lang['sibling_duplicate_order'] = 'Two sibling pages can not have the same order. Pages were not reordered.';
$lang['no_orders_changed'] = 'You chose to reorder pages, but you did not change the order of any of them. Pages were not reordered.';
$lang['order_too_small'] = 'A page order cannot be zero. Pages were not reordered.';
$lang['order_too_large'] = 'A page order cannot be larger than the number of pages in that level. Pages were not reordered.';
$lang['user_tag'] = 'User Tag';
$lang['add'] = 'Add';
$lang['CSS'] = 'CSS';
$lang['about'] = 'About';
$lang['action'] = 'Action';
$lang['actionstatus'] = 'Action/Status';
$lang['active'] = 'Active';
$lang['addcontent'] = 'Add New Content';
$lang['cantremove'] = 'Cannot Remove';
$lang['changepermissions'] = 'Change Permissions';
$lang['changepermissionsconfirm'] = 'USE CAUTION\n\nThis action will attempt to ensure that all of the files making up the module are writable by the web server.\nAre you sure you want to continue?';
$lang['contentadded'] = 'The content was successfully added to the database.';
$lang['contentupdated'] = 'The content was successfully updated.';
$lang['contentdeleted'] = 'The content was successfully removed from the database.';
$lang['success'] = 'Success';
$lang['addcss'] = 'Add a Stylesheet';
$lang['addgroup'] = 'Add New Group';
$lang['additionaleditors'] = 'Additional Editors';
$lang['addtemplate'] = 'Add New Template';
$lang['adduser'] = 'Add New User';
$lang['addusertag'] = 'Add User Defined Tag';
$lang['adminaccess'] = 'Access to login to admin';
$lang['adminlog'] = 'Admin Log';
$lang['adminlogcleared'] = 'The Admin Log was succesfully cleared';
$lang['adminlogempty'] = 'The Admin Log is empty';
$lang['adminsystemtitle'] = 'CMS Admin System';
$lang['adminpaneltitle'] = 'CMS Made Simple Admin Console'; // needs translation
$lang['advanced'] = 'Advanced';
$lang['aliasalreadyused'] = 'The supplied "Page Alias" is already in use on another page.  Change the "Page Alias" to something else.';
$lang['aliasmustbelettersandnumbers'] = 'Alias must be all letters and numbers';
$lang['aliasnotaninteger'] = 'Alias cannot be an integer';
$lang['allpagesmodified'] = 'All pages modified!';
$lang['assignments'] = 'Assign Users';
$lang['associationexists'] = 'This association already exists';
$lang['autoinstallupgrade'] = 'Automatically install or upgrade';
$lang['back'] = 'Back to Menu';
$lang['backtoplugins'] = 'Back to Plugins List';
$lang['cancel'] = 'Cancel';
$lang['cantchmodfiles'] = 'Couldn\'t change permissions on some files';
$lang['cantremovefiles'] = 'Problem Removing Files (permissions?)';
$lang['confirmcancel'] = 'Are you sure you want to discard your changes? Click OK to discard all changes. Click Cancel to continue editing.';
$lang['canceldescription'] = 'Discard Changes';
$lang['clearadminlog'] = 'Clear Admin Log';
$lang['code'] = 'Code';
$lang['confirmdefault'] = 'Are you sure you want to set - %s - as site default page?';
$lang['confirmdeletedir'] = 'Are you sure you want to delete this dir and all of its contents?';
$lang['content'] = 'Content';
$lang['contentmanagement'] = 'Content Management';
$lang['contenttype'] = 'Content Type';
$lang['copy'] = 'Copy';
$lang['copytemplate'] = 'Copy Template';
$lang['create'] = 'Create';
$lang['createnewfolder'] = 'Create New Folder';
$lang['cssalreadyused'] = 'CSS name already in use';
$lang['cssmanagement'] = 'CSS Management';
$lang['currentassociations'] = 'Current Associations';
$lang['currentdirectory'] = 'Current Directory';
$lang['currentgroups'] = 'Current Groups';
$lang['currentpages'] = 'Current Pages';
$lang['currenttemplates'] = 'Current Templates';
$lang['currentusers'] = 'Current Users';
$lang['custom404'] = 'Custom 404 Error Message';
$lang['database'] = 'Database';
$lang['databaseprefix'] = 'Database Prefix';
$lang['databasetype'] = 'Database Type';
$lang['date'] = 'Date';
$lang['default'] = 'Default';
$lang['delete'] = 'Delete';
$lang['deleteconfirm'] = 'Are you sure you want to delete - %s - ?';
$lang['deleteassociationconfirm'] = 'Are you sure you want to delete association to - %s - ?';
$lang['deletecss'] = 'Delete CSS';
$lang['dependencies'] = 'Dependencies';
$lang['description'] = 'Description';
$lang['directoryexists'] = 'This directory already exists.';
$lang['down'] = 'Down';
$lang['edit'] = 'Edit';
$lang['editconfiguration'] = 'Edit Configuration';
$lang['editcontent'] = 'Edit Content';
$lang['editcss'] = 'Edit Stylesheet';
$lang['editcsssuccess'] = 'Stylesheet updated';
$lang['editgroup'] = 'Edit Group';
$lang['editpage'] = 'Edit Page';
$lang['edittemplate'] = 'Edit Template';
$lang['edittemplatesuccess'] = 'Template updated';
$lang['edituser'] = 'Edit User';
$lang['editusertag'] = 'Edit User Defined Tag';
$lang['usertagadded'] = 'The User Defined Tag was successfully added.';
$lang['usertagupdated'] = 'The User Defined Tag was successfully updated.';
$lang['usertagdeleted'] = 'The User Defined Tag was successfully removed.';
$lang['email'] = 'Email Address';
$lang['errorattempteddowngrade'] = 'Installing this module would result in a downgrade.  Operation aborted';
$lang['errorchildcontent'] = 'Content still contains child contents. Please remove them first.';
$lang['errorcopyingtemplate'] = 'Error Copying Template';
$lang['errorcouldnotparsexml'] = 'Error parsing XML file. Please make sure you are uploading a .xml file and not a .tar.gz or zip file.';
$lang['errorcreatingassociation'] = 'Error creating association';
$lang['errorcssinuse'] = 'This Stylesheet is still used by template or pages. Please remove those associations first.';
$lang['errordefaultpage'] = 'Can not delete the current default page. Please set a different one first.';
$lang['errordeletingassociation'] = 'Error deleting association';
$lang['errordeletingcss'] = 'Error deleteing css';
$lang['errordeletingdirectory'] = 'Could not delete directory. Permissions problem?';
$lang['errordeletingfile'] = 'Could not delete file. Permissions Problem?';
$lang['errordirectorynotwritable'] = 'No permission to write in directory.  This could be caused by file permissions and ownership.  Safe mode may also be in effect.';
$lang['errordtdmismatch'] = 'DTD Version missing or incompatible in the XML file';
$lang['errorgettingcssname'] = 'Error getting Stylesheet name';
$lang['errorgettingtemplatename'] = 'Error getting template name';
$lang['errorincompletexml'] = 'XML File is incomplete or invalid';
$lang['uploadxmlfile'] = 'Install module via XML file';
$lang['cachenotwritable'] = 'Cache folder is not writable. Clearing cache will not work. Please make the tmp/cache folder have full read/write/execute permissions (chmod 777).  You may also have to disable safe mode.';
$lang['modulesnotwritable'] = 'The modules folder <em>(and/or the uploads folder)</em> is not writable, if you would like to install modules by uploading an XML file you need ensure that these folders have full read/write/execute permissions (chmod 777).  Safe mode may also be in effect.';
$lang['noxmlfileuploaded'] = 'No file was uploaded. To install a module via XML you must choose and upload an module .xml file from your computer.';
$lang['errorinsertingcss'] = 'Error inserting Stylesheet';
$lang['errorinsertinggroup'] = 'Error inserting group';
$lang['errorinsertingtag'] = 'Error inserting user tag';
$lang['errorinsertingtemplate'] = 'Error inserting template';
$lang['errorinsertinguser'] = 'Error inserting user';
$lang['errornofilesexported'] = 'Error exporting files to xml';
$lang['errorretrievingcss'] = 'Error retrieving Stylesheet';
$lang['errorretrievingtemplate'] = 'Error retrieving template';
$lang['errortemplateinuse'] = 'This template is still in use by a page. Please remove it first.';
$lang['errorupdatingcss'] = 'Error updating Stylesheet';
$lang['errorupdatinggroup'] = 'Error updating group';
$lang['errorupdatingpages'] = 'Error updating pages';
$lang['errorupdatingtemplate'] = 'Error updating template';
$lang['errorupdatinguser'] = 'Error updating user';
$lang['errorupdatingusertag'] = 'Error updating user tag';
$lang['erroruserinuse'] = 'This user still owns content pages. Please change ownership to another user before deleting.';
$lang['eventhandlers'] = 'Event Manager';
$lang['editeventhandler'] = 'Edit Event Handler';
$lang['eventhandlerdescription'] = 'Associate user tags with events';
$lang['export'] = 'Export';
$lang['event'] = 'Event';
$lang['false'] = 'False';
$lang['settrue'] = 'Set True';
$lang['filecreatedirnodoubledot'] = 'Directory cannot contain \'..\'.';
$lang['filecreatedirnoname'] = 'Cannot create a directory with no name.';
$lang['filecreatedirnoslash'] = 'Directory cannot contain \'/\' or \'\\\'.';
$lang['filemanagement'] = 'File Management';
$lang['filename'] = 'Filename';
$lang['filenotuploaded'] = 'File could not be uploaded. This could be a permissions or Safe mode problem?';
$lang['filesize'] = 'File Size';
$lang['firstname'] = 'First Name';
$lang['groupmanagement'] = 'Group Management';
$lang['grouppermissions'] = 'Group Permissions';
$lang['handler'] = 'Handler (user defined tag)';
$lang['headtags'] = 'Head Tags';
$lang['help'] = 'Help';
$lang['new_window'] = 'new window';
$lang['helpwithsection'] = '%s Help';
$lang['helpaddtemplate'] = '<p>A template is what controls the look and feel of your site\'s content.</p><p>Create the layout here and also add your CSS in the Stylesheet section to control the look of your various elements.</p>';
$lang['helplisttemplate'] = '<p>This page allows you to edit, delete, and create templates.</p><p>To create a new template, click on the <u>Add New Template</u> button.</p><p>If you wish to set all content pages to use the same template, click on the <u>Set All Content</u> link.</p><p>If you wish to duplicate a template, click on the <u>Copy</u> icon and you will be prompted to name the new duplicate template.</p>';
$lang['home'] = 'Home';
$lang['homepage'] = 'Homepage';
$lang['hostname'] = 'Hostname';
$lang['idnotvalid'] = 'The given id is not valid';
$lang['imagemanagement'] = 'Image Manager';
$lang['informationmissing'] = 'Information missing';
$lang['install'] = 'Install';
$lang['invalidcode'] = 'Invalid code entered.';
$lang['illegalcharacters'] = 'Invalid characters in field %s.';
$lang['invalidcode_brace_missing'] = 'Uneven amount of braces';
$lang['invalidtemplate'] = 'The template is not valid';
$lang['itemid'] = 'Item ID';
$lang['itemname'] = 'Item Name';
$lang['language'] = 'Language';
$lang['lastname'] = 'Last Name';
$lang['logout'] = 'Logout';
$lang['loginprompt'] = 'Enter a valid user credential to get access to the Admin Console.'; // needs translation
$lang['logintitle'] = 'Login to CMS Made Simple'; // needs translation
$lang['menutext'] = 'Menu Text';
$lang['missingparams'] = 'Some parameters were missing or invalid';
$lang['modifygroupassignments'] = 'Modify Group Assignments';
$lang['moduleabout'] = 'About the %s module';
$lang['modulehelp'] = 'Help for the %s module';
$lang['msg_defaultcontent'] = 'Add code here that should appear as the default content of all new pages';
$lang['msg_defaultmetadata'] = 'Add code here that should appear in the metadata section of all new pages';
$lang['wikihelp'] = 'Community Help';
$lang['moduleinstalled'] = 'Module already installed';
$lang['moduleinterface'] = '%s Interface';
$lang['modules'] = 'Modules';
$lang['move'] = 'Move';
$lang['name'] = 'Name';
$lang['needpermissionto'] = 'You need the \'%s\' permission to perform that function.';
$lang['needupgrade'] = 'Needs Upgrade';
$lang['newtemplatename'] = 'New Template Name';
$lang['next'] = 'Next';
$lang['noaccessto'] = 'No Access to %s';
$lang['nocss'] = 'No Stylesheet';
$lang['noentries'] = 'No Entries';
$lang['nofieldgiven'] = 'No %s given!';
$lang['nofiles'] = 'No Files';
$lang['nopasswordmatch'] = 'Passwords do not match';
$lang['norealdirectory'] = 'No real directory given';
$lang['norealfile'] = 'No real file given';
$lang['notinstalled'] = 'Not Installed';
$lang['overwritemodule'] = 'Overwrite existing modules';
$lang['owner'] = 'Owner';
$lang['pagealias'] = 'Page Alias';
$lang['pagedefaults'] = 'Page Defaults';
$lang['pagedefaultsdescription'] = 'Set default values for new pages';
$lang['parent'] = 'Parent';
$lang['password'] = 'Password';
$lang['passwordagain'] = 'Password (again)';
$lang['permission'] = 'Permission';
$lang['permissions'] = 'Permissions';
$lang['permissionschanged'] = 'Permissions have been updated.';
$lang['pluginabout'] = 'About the %s tag';
$lang['pluginhelp'] = 'Help for the %s tag';
$lang['pluginmanagement'] = 'Plugin Management';
$lang['prefsupdated'] = 'Preferences have been updated.';
$lang['preview'] = 'Preview';
$lang['previewdescription'] = 'Preview changes';
$lang['previous'] = 'Previous';
$lang['remove'] = 'Remove';
$lang['removeconfirm'] = 'This action will permanently remove the files making up this module from this installation.\nAre you sure you want to proceed?';
$lang['removecssassociation'] = 'Remove Stylesheet Assocation';
$lang['saveconfig'] = 'Save Config';
$lang['send'] = 'Send';
$lang['setallcontent'] = 'Set All Pages';
$lang['setallcontentconfirm'] = 'Are you sure you want to set all pages to use this template?';
$lang['showinmenu'] = 'Show in Menu';
$lang['showsite'] = 'Show Site';
$lang['sitedownmessage'] = 'Site Down Message';
$lang['siteprefs'] = 'Global Settings';
$lang['status'] = 'Status';
$lang['stylesheet'] = 'Stylesheet';
$lang['submit'] = 'Submit';
$lang['submitdescription'] = 'Save changes';
$lang['tags'] = 'Tags';
$lang['template'] = 'Template';
$lang['templateexists'] = 'Template name already exists';
$lang['templatemanagement'] = 'Template Management';
$lang['title'] = 'Title';
$lang['tools'] = 'Tools';
$lang['true'] = 'True';
$lang['setfalse'] = 'Set False';
$lang['type'] = 'Type';
$lang['typenotvalid'] = 'Type is not valid';
$lang['uninstall'] = 'Uninstall';
$lang['uninstallconfirm'] = 'Are you sure you want to uninstall this module? Name:';
$lang['up'] = 'Up';
$lang['upgrade'] = 'Upgrade';
$lang['upgradeconfirm'] = 'Are you sure you want to upgrade this?';
$lang['uploadfile'] = 'Upload File';
$lang['url'] = 'URL';
$lang['useadvancedcss'] = 'Use Advanced Stylesheet Management';
$lang['user'] = 'User';
$lang['userdefinedtags'] = 'User Defined Tags';
$lang['usermanagement'] = 'User Management';
$lang['username'] = 'Username';
$lang['usernameincorrect'] = 'Username or password incorrect';
$lang['userprefs'] = 'User Preferences';
$lang['usersassignedtogroup'] = 'Users Assigned to Group %s';
$lang['usertagexists'] = 'A tag with this name already exists. Please choose another.';
$lang['usewysiwyg'] = 'Use WYSIWYG editor for content';
$lang['version'] = 'Version';
$lang['view'] = 'View';
$lang['welcomemsg'] = 'Welcome %s';
$lang['directoryabove'] = 'directory above current level';
$lang['nodefault'] = 'No Default Selected';
$lang['blobexists'] = 'Global Content Block name already exists';
$lang['blobmanagement'] = 'Global Content Block Management';
$lang['errorinsertingblob'] = 'There was an error inserting the Global Content Block';
$lang['addhtmlblob'] = 'Add Global Content Block';
$lang['edithtmlblob'] = 'Edit Global Content Block';
$lang['edithtmlblobsuccess'] = 'Global content block updated';
$lang['tagtousegcb'] = 'Tag to Use this Block';
$lang['gcb_wysiwyg'] = 'Enable GCB WYSIWYG';
$lang['gcb_wysiwyg_help'] = 'Enable the WYSIWYG editor while editing Global Content Blocks';
$lang['filemanager'] = 'File Manager';
$lang['imagemanager'] = 'Image Manager';
$lang['encoding'] = 'Encoding';
$lang['clearcache'] = 'Clear Cache';
$lang['clear'] = 'Clear';
$lang['cachecleared'] = 'Cache Cleared';
$lang['apply'] = 'Apply';
$lang['applydescription'] = 'Save changes and continue to edit';
$lang['none'] = 'None';
$lang['wysiwygtouse'] = 'Select WYSIWYG to use';
$lang['syntaxhighlightertouse'] = 'Select syntax highlighter to use'; 
$lang['cachable'] = 'Cachable';
$lang['hasdependents'] = 'Has Dependents';
$lang['missingdependency'] = 'Missing Dependency';
$lang['minimumversion'] = 'Minimum Version';
$lang['minimumversionrequired'] = 'Minimum CMSMS Version Required';
$lang['maximumversion'] = 'Maximum Version';
$lang['maximumversionsupported'] = 'Maximum CMSMS Version Supported';
$lang['depsformodule'] = 'Dependencies for %s Module';
$lang['installed'] = 'Installed';
$lang['author'] = 'Author';
$lang['changehistory'] = 'Change History';
$lang['moduleerrormessage'] = 'Error Message for %s Module';
$lang['moduleupgradeerror'] = 'There was an error upgrading the module.';
$lang['moduleinstallmessage'] = 'Install Message for %s Module';
$lang['moduleuninstallmessage'] = 'Uninstall Message for %s Module';
$lang['admintheme'] = 'Administration Theme';
$lang['addstylesheet'] = 'Add a Stylesheet';
$lang['editstylesheet'] = 'Edit Stylesheet';
$lang['addcssassociation'] = 'Add Stylesheet Association';
$lang['attachstylesheet'] = 'Attach This Stylesheet';
$lang['attachtemplate'] = 'Attach to this Template';
$lang['main'] = 'Main'; //needs translation
$lang['pages'] = 'Pages'; //needs translation
$lang['page'] = 'Page'; //needs translation
$lang['files'] = 'Files'; //needs translation
$lang['layout'] = 'Layout'; //needs translation
$lang['usersgroups'] = 'Users &amp; Groups'; //needs translation
$lang['extensions'] = 'Extensions'; //needs translation
$lang['preferences'] = 'Preferences'; //needs translation
$lang['admin'] = 'Site Admin'; //needs translation
$lang['viewsite'] = 'View Site'; //needs translation
$lang['templatecss'] = 'Assign Templates to Stylesheet'; //needs translation
$lang['plugins'] = 'Plugins'; //needs translation
$lang['movecontent'] = 'Move Pages'; //needs translation
$lang['module'] = 'Module'; //needs translation
$lang['usertags'] = 'User Defined Tags'; //needs translation
$lang['htmlblobs'] = 'Global Content Blocks'; //needs translation
$lang['adminhome'] = 'Administration Home'; //needs translation
$lang['liststylesheets'] = 'Style Sheets'; //needs translation
$lang['preferencesdescription'] = 'This is where you set various site-wide preferences.'; //needs translation
$lang['adminlogdescription'] = 'Shows a log of who did what in the admin.'; //needs translation
$lang['mainmenu'] = 'Main Menu'; //needs translation
$lang['users'] = 'Users'; //needs translation
$lang['usersdescription'] = 'This is where you manage users.'; //needs translation
$lang['groups'] = 'Groups'; //needs translation
$lang['groupsdescription'] = 'This is where you manage groups.'; //needs translation
$lang['groupassignments'] = 'Group Assignments'; //needs translation
$lang['groupassignmentdescription'] = 'Here you can assign users to groups.'; //needs translation
$lang['groupperms'] = 'Group Permissions'; //needs translation
$lang['grouppermsdescription'] = 'Set permissions and access levels for groups'; //needs translation
$lang['pagesdescription'] = 'This is where we add and edit pages and other content.'; //needs translation
$lang['htmlblobdescription'] = 'Global Content Blocks are chunks of content you can place in your pages or templates.'; //needs translation
$lang['templates'] = 'Templates'; //needs translation
$lang['templatesdescription'] = 'This is where we add and edit templates. Templates define the look and feel of your site.'; //needs translation
$lang['stylesheets'] = 'Stylesheets'; //needs translation
$lang['stylesheetsdescription'] = 'Stylesheet management is an advanced way to handle cascading Stylesheets (CSS) separately from templates.'; //needs translation
$lang['filemanagerdescription'] = 'Upload and manage files.'; //needs translation
$lang['imagemanagerdescription'] = 'Upload/edit and remove images.'; //needs translation
$lang['moduledescription'] = 'Modules extend CMS Made Simple to provide all kinds of custom functionality.'; //needs translation
$lang['tagdescription'] = 'Tags are little bits of functionality that can be added to your content and/or templates.'; //needs translation
$lang['usertagdescription'] = 'Tags that you can create and modify yourself to perform specific tasks, right from your browser.'; //needs translation
$lang['installdirwarning'] = '<em><strong>Warning:</strong></em> install directory still exists. Please remove it completely.'; //needs translation
$lang['subitems'] = 'Subitems'; //needs translation
$lang['extensionsdescription'] = 'Modules, tags, and other assorted fun.'; //needs translation
$lang['usersgroupsdescription'] = 'User and Group related items.'; //needs translation
$lang['layoutdescription'] = 'Site layout options.'; //needs translation
$lang['admindescription'] = 'Site Administration functions.'; //needs translation
$lang['contentdescription'] = 'This is where we add and edit content.'; //needs translation
$lang['enablecustom404'] = 'Enable Custom 404 Message'; //needs translation
$lang['enablesitedown'] = 'Enable Site Down Message'; //needs translation
$lang['bookmarks'] = 'Shortcuts'; //needs translation
$lang['user_created'] = 'Custom Shortcuts';
$lang['forums'] = 'Forums';
$lang['wiki'] = 'Wiki';
$lang['irc'] = 'IRC';
$lang['module_help'] = 'Module Help';
$lang['managebookmarks'] = 'Manage Shortcuts'; //needs translation
$lang['editbookmark'] = 'Edit Shortcut'; //needs translation
$lang['addbookmark'] = 'Add Shortcut'; //needs translation
$lang['recentpages'] = 'Recent Pages'; //needs translation
$lang['groupname'] = 'Group Name'; // needs translation
$lang['selectgroup'] = 'Select Group'; //needs translation
$lang['updateperm'] = 'Update Permissions'; //needs translation
$lang['admincallout'] = 'Administration Shortcuts'; //needs translation
$lang['showbookmarks'] = 'Show Admin Shortcuts'; //needs translation
$lang['hide_help_links'] = 'Hide help links';
$lang['hide_help_links_help'] = 'Check this box to disable the wiki and module help links in page headers.';
$lang['showrecent'] = 'Show Recently Used Pages'; //needs translation
$lang['attachtotemplate'] = 'Attach Stylesheet to Template'; //needs translation
$lang['attachstylesheets'] = 'Attach Stylesheets'; //needs translation
$lang['indent'] = 'Indent Pagelist to Emphasize Hierarchy'; // needs translation
$lang['adminindent'] = 'Content Display'; // needs translation
$lang['contract'] = 'Collapse Section'; // needs translation
$lang['expand'] = 'Expand Section'; // needs translation
$lang['expandall'] = 'Expand All Sections'; // needs translation;
$lang['contractall'] = 'Collapse All Sections'; // needs translation;
$lang['menu_bookmarks'] = '[+]'; //needs translation
$lang['globalconfig'] = 'Global Settings'; //needs translation
$lang['adminpaging'] = 'Number of Content Items to show per/page in Page List'; //needs translation
$lang['nopaging'] = 'Show All Items'; //needs translation
$lang['myprefs'] = 'My Preferences'; //needs translation
$lang['myprefsdescription'] = 'This is where you can customize the site admin area to work the way you want.'; //needs translation
$lang['myaccount'] = 'My Account'; //needs translation
$lang['myaccountdescription'] = 'This is where you can update your personal account details.'; //needs translation
$lang['adminprefs'] = 'User Preferences'; //needs translation
$lang['adminprefsdescription'] = 'This is where you set your specific preferences for site administration.'; //needs translation
$lang['managebookmarksdescription'] = 'This is where you can manage your administration shortcuts.'; //needs translation
$lang['options'] = 'Options'; //needs translation
$lang['langparam'] = 'Parameter is used to specify what language to use for display on the frontend. Not all modules support or need this.'; //needs translation
$lang['parameters'] = 'Parameters'; //needs translation
$lang['mediatype'] = 'Media Type'; //needs translation
$lang['mediatype_'] = 'None set : will affect everywhere ';
$lang['mediatype_all'] = 'all : Suitable for all devices.';
$lang['mediatype_aural'] = 'aural : Intended for speech synthesizers.';
$lang['mediatype_braille'] = 'braille : Intended for braille tactile feedback devices.';
$lang['mediatype_embossed'] = 'embossed : Intended for paged braille printers.';
$lang['mediatype_handheld'] = 'handheld : Intended for handheld devices';
$lang['mediatype_print'] = 'print : Intended for paged, opaque material and for documents viewed on screen in print preview mode.';
$lang['mediatype_projection'] = 'projection : Intended for projected presentations, for example projectors or print to transparencies.';
$lang['mediatype_screen'] = 'screen : Intended primarily for color computer screens.';
$lang['mediatype_tty'] = 'tty : Intended for media using a fixed-pitch character grid, such as teletypes and terminals.';
$lang['mediatype_tv'] = 'tv : Intended for television-type devices.';
$lang['assignmentchanged'] = 'Group Assignments have been updated.'; //needs translation
$lang['stylesheetexists'] = 'Stylesheet Exists'; //needs translation
$lang['errorcopyingstylesheet'] = 'Error Copying Stylesheet'; //needs translation
$lang['copystylesheet'] = 'Copy Stylesheet'; //needs translation
$lang['newstylesheetname'] = 'New Stylesheet Name'; //needs translation
$lang['target'] = 'Target'; //needs translation
$lang['xml'] = 'XML';
$lang['xmlmodulerepository'] = 'URL of ModuleRepository soap server';
$lang['metadata'] = 'Metadata';
$lang['globalmetadata'] = 'Global Metadata';
$lang['titleattribute'] = 'Description (title attribute)';
$lang['tabindex'] = 'Tab Index';
$lang['accesskey'] = 'Access Key';
$lang['sitedownwarning'] = '<strong>Warning:</strong> Your site is currently showing a "Site Down for Maintenence" message. Remove the %s file to resolve this.';
$lang['deletecontent'] = 'Delete Content';
$lang['deletepages'] = 'Delete these pages?';
$lang['selectall'] = 'Select All';
$lang['selecteditems'] = 'With Selected';
$lang['inactive'] = 'Inactive';
$lang['deletetemplates'] = 'Delete Templates';
$lang['templatestodelete'] = 'These templates will be deleted';
$lang['wontdeletetemplateinuse'] = 'These templates are in use and will not be deleted';
$lang['deletetemplate'] = 'Delete Templates';
$lang['stylesheetstodelete'] = 'These stylesheets will be deleted';
$lang['sitename'] = 'Site Name';
// Only used by admintheme::ShowHeader
$lang['siteadmin'] = $lang['admin'];
$lang['images'] = $lang['imagemanager'];
$lang['blobs'] = $lang['htmlblobs'];
$lang['groupmembers'] = $lang['groupassignments'];
// Used in adminTheme:showErrors
$lang['troubleshooting'] = '(Troubleshooting)';
$lang['originator'] = 'Originator';
$lang['event_desc_loginpost'] = 'Sent after a user logs into the admin panel';
$lang['event_desc_logoutpost'] = 'Sent after a user logs out of the admin panel';
$lang['event_desc_adduserpre'] = 'Sent before a new user is created';
$lang['event_desc_adduserpost'] = 'Sent after a new user is created';
$lang['event_desc_edituserpre'] = 'Sent before edits to a user are saved';
$lang['event_desc_edituserpost'] = 'Sent after edits to a user are saved';
$lang['event_desc_deleteuserpre'] = 'Sent before a user is deleted from the system';
$lang['event_desc_deleteuserpost'] = 'Sent after a user is deleted from the system';
$lang['event_desc_addgrouppre'] = 'Sent before a new group is created';
$lang['event_desc_addgrouppost'] = 'Sent after a new group is created';
$lang['event_desc_changegroupassignpre'] = 'Sent before group assignments are saved';
$lang['event_desc_changegroupassignpost'] = 'Sent after group assignments are saved';
$lang['event_desc_editgrouppre'] = 'Sent before edits to a group are saved';
$lang['event_desc_editgrouppost'] = 'Sent after edits to a group are saved';
$lang['event_desc_deletegrouppre'] = 'Sent before a group is deleted from the system';
$lang['event_desc_deletegrouppost'] = 'Sent after a group is deleted from the system';
$lang['event_desc_addstylesheetpre'] = 'Sent before a new stylesheet is created';
$lang['event_desc_addstylesheetpost'] = 'Sent after a new stylesheet is created';
$lang['event_desc_editstylesheetpre'] = 'Sent before edits to a stylesheet are saved';
$lang['event_desc_editstylesheetpost'] = 'Sent after edits to a stylesheet are saved';
$lang['event_desc_deletestylesheetpre'] = 'Sent before a stylesheet is deleted from the system';
$lang['event_desc_deletestylesheetpost'] = 'Sent after a stylesheet is deleted from the system';
$lang['event_desc_addtemplatepre'] = 'Sent before a new template is created';
$lang['event_desc_addtemplatepost'] = 'Sent after a new template is created';
$lang['event_desc_edittemplatepre'] = 'Sent before edits to a template are saved';
$lang['event_desc_edittemplatepost'] = 'Sent after edits to a template are saved';
$lang['event_desc_deletetemplatepre'] = 'Sent before a template is deleted from the system';
$lang['event_desc_deletetemplatepost'] = 'Sent after a template is deleted from the system';
$lang['event_desc_templateprecompile'] = 'Sent before a template is sent to smarty for processing';
$lang['event_desc_templatepostcompile'] = 'Sent after a template has been processed by smarty';
$lang['event_desc_addglobalcontentpre'] = 'Sent before a new global content block is created';
$lang['event_desc_addglobalcontentpost'] = 'Sent after a new global content block is created';
$lang['event_desc_editglobalcontentpre'] = 'Sent before edits to a global content block are saved';
$lang['event_desc_editglobalcontentpost'] = 'Sent after edits to a global content block are saved';
$lang['event_desc_deleteglobalcontentpre'] = 'Sent before a global content block is deleted from the system';
$lang['event_desc_deleteglobalcontentpost'] = 'Sent after a global content block is deleted from the system';
$lang['event_desc_globalcontentprecompile'] = 'Sent before a global content block is sent to smarty for processing';
$lang['event_desc_globalcontentpostcompile'] = 'Sent after a global content block has been processed by smarty';
$lang['event_desc_contenteditpre'] = 'Sent before edits to content are saved';
$lang['event_desc_contenteditpost'] = 'Sent after edits to content are saved';
$lang['event_desc_contentdeletepre'] = 'Sent before content is deleted from the system';
$lang['event_desc_contentdeletepost'] = 'Sent after content is deleted from the system';
$lang['event_desc_contentstylesheet'] = 'Sent before the stylesheet is sent to the browser';
$lang['event_desc_contentprecompile'] = 'Sent before content is sent to smarty for processing';
$lang['event_desc_contentpostcompile'] = 'Sent after content has been processed by smarty';
$lang['event_desc_contentpostrender'] = 'Sent before the combined html is sent to the browser';
$lang['event_desc_smartyprecompile'] = 'Sent before any content destined for smarty is sent to for processing';
$lang['event_desc_smartypostcompile'] = 'Sent after any content destined for smarty has been processed';
$lang['event_help_loginpost'] = '<p>Sent after a user logs into the admin panel.</p>
<h4>Parameters</h4>
<ul>
<li>\'user\' - Reference to the affected user object.</li>
</ul>
';
$lang['event_help_logoutpost'] = '<p>Sent after a user logs out of the admin panel.</p>
<h4>Parameters</h4>
<ul>
<li>\'user\' - Reference to the affected user object.</li>
</ul>
';
$lang['event_help_adduserpre'] = '<p>Sent before a new user is created.</p>
<h4>Parameters</h4>
<ul>
<li>\'user\' - Reference to the affected user object.</li>
</ul>
';
$lang['event_help_adduserpost'] = '<p>Sent after a new user is created.</p>
<h4>Parameters</h4>
<ul>
<li>\'user\' - Reference to the affected user object.</li>
</ul>
';
$lang['event_help_edituserpre'] = '<p>Sent before edits to a user are saved.</p>
<h4>Parameters</h4>
<ul>
<li>\'user\' - Reference to the affected user object.</li>
</ul>
';
$lang['event_help_edituserpost'] = '<p>Sent after edits to a user are saved.</p>
<h4>Parameters</h4>
<ul>
<li>\'user\' - Reference to the affected user object.</li>
</ul>
';
$lang['event_help_deleteuserpre'] = '<p>Sent before a user is deleted from the system.</p>
<h4>Parameters</h4>
<ul>
<li>\'user\' - Reference to the affected user object.</li>
</ul>
';
$lang['event_help_deleteuserpost'] = '<p>Sent after a user is deleted from the system.</p>
<h4>Parameters</h4>
<ul>
<li>\'user\' - Reference to the affected user object.</li>
</ul>
';
$lang['event_help_addgrouppre'] = '<p>Sent before a new group is created.</p>
<h4>Parameters</h4>
<ul>
<li>\'group\' - Reference to the affected group object.</li>
</ul>
';
$lang['event_help_addgrouppost'] = '<p>Sent after a new group is created.</p>
<h4>Parameters</h4>
<ul>
<li>\'group\' - Reference to the affected group object.</li>
</ul>
';
$lang['event_help_changegroupassignpre'] = '<p>Sent before group assignments are saved.</p>
<h4>Parameters></h4>
<ul>
<li>\'group\' - Reference to the group object.</li>
<li>\'users\' - Array of references to user objects belonging to the group.</li>
';
$lang['event_help_changegroupassignpost'] = '<p>Sent after group assignments are saved.</p>
<h4>Parameters></h4>
<ul>
<li>\'group\' - Reference to the affected group object.</li>
<li>\'users\' - Array of references to user objects now belonging to the affected group.</li>
';
$lang['event_help_editgrouppre'] = '<p>Sent before edits to a group are saved.</p>
<h4>Parameters</h4>
<ul>
<li>\'group\' - Reference to the affected group object.</li>
</ul>
';
$lang['event_help_editgrouppost'] = '<p>Sent after edits to a group are saved.</p>
<h4>Parameters</h4>
<ul>
<li>\'group\' - Reference to the affected group object.</li>
</ul>
';
$lang['event_help_deletegrouppre'] = '<p>Sent before a group is deleted from the system.</p>
<h4>Parameters</h4>
<ul>
<li>\'group\' - Reference to the affected group object.</li>
</ul>
';
$lang['event_help_deletegrouppost'] = '<p>Sent after a group is deleted from the system.</p>
<h4>Parameters</h4>
<ul>
<li>\'group\' - Reference to the affected group object.</li>
</ul>
';
$lang['event_help_addstylesheetpre'] = '<p>Sent before a new stylesheet is created.</p>
<h4>Parameters</h4>
<ul>
<li>\'stylesheet\' - Reference to the affected stylesheet object.</li>
</ul>
';
$lang['event_help_addstylesheetpost'] = '<p>Sent after a new stylesheet is created.</p>
<h4>Parameters</h4>
<ul>
<li>\'stylesheet\' - Reference to the affected stylesheet object.</li>
</ul>
';
$lang['event_help_editstylesheetpre'] = '<p>Sent before edits to a stylesheet are saved.</p>
<h4>Parameters</h4>
<ul>
<li>\'stylesheet\' - Reference to the affected stylesheet object.</li>
</ul>
';
$lang['event_help_editstylesheetpost'] = '<p>Sent after edits to a stylesheet are saved.</p>
<h4>Parameters</h4>
<ul>
<li>\'stylesheet\' - Reference to the affected stylesheet object.</li>
</ul>
';
$lang['event_help_deletestylesheetpre'] = '<p>Sent before a stylesheet is deleted from the system.</p>
<h4>Parameters</h4>
<ul>
<li>\'stylesheet\' - Reference to the affected stylesheet object.</li>
</ul>
';
$lang['event_help_deletestylesheetpost'] = '<p>Sent after a stylesheet is deleted from the system.</p>
<h4>Parameters</h4>
<ul>
<li>\'stylesheet\' - Reference to the affected stylesheet object.</li>
</ul>
';
$lang['event_help_addtemplatepre'] = '<p>Sent before a new template is created.</p>
<h4>Parameters</h4>
<ul>
<li>\'template\' - Reference to the affected template object.</li>
</ul>
';
$lang['event_help_addtemplatepost'] = '<p>Sent after a new template is created.</p>
<h4>Parameters</h4>
<ul>
<li>\'template\' - Reference to the affected template object.</li>
</ul>
';
$lang['event_help_edittemplatepre'] = '<p>Sent before edits to a template are saved.</p>
<h4>Parameters</h4>
<ul>
<li>\'template\' - Reference to the affected template object.</li>
</ul>
';
$lang['event_help_edittemplatepost'] = '<p>Sent after edits to a template are saved.</p>
<h4>Parameters</h4>
<ul>
<li>\'template\' - Reference to the affected template object.</li>
</ul>
';
$lang['event_help_deletetemplatepre'] = '<p>Sent before a template is deleted from the system.</p>
<h4>Parameters</h4>
<ul>
<li>\'template\' - Reference to the affected template object.</li>
</ul>
';
$lang['event_help_deletetemplatepost'] = '<p>Sent after a template is deleted from the system.</p>
<h4>Parameters</h4>
<ul>
<li>\'template\' - Reference to the affected template object.</li>
</ul>
';
$lang['event_help_templateprecompile'] = '<p>Sent before a template is sent to smarty for processing.</p>
<h4>Parameters</h4>
<ul>
<li>\'template\' - Reference to the affected template text.</li>
</ul>
';
$lang['event_help_templatepostcompile'] = '<p>Sent after a template has been processed by smarty.</p>
<h4>Parameters</h4>
<ul>
<li>\'template\' - Reference to the affected template text.</li>
</ul>
';
$lang['event_help_addglobalcontentpre'] = '<p>Sent before a new global content block is created.</p>
<h4>Parameters</h4>
<ul>
<li>\'global_content\' - Reference to the affected global content block object.</li>
</ul>
';
$lang['event_help_addglobalcontentpost'] = '<p>Sent after a new global content block is created.</p>
<h4>Parameters</h4>
<ul>
<li>\'global_content\' - Reference to the affected global content block object.</li>
</ul>
';
$lang['event_help_editglobalcontentpre'] = '<p>Sent before edits to a global content block are saved.</p>
<h4>Parameters</h4>
<ul>
<li>\'global_content\' - Reference to the affected global content block object.</li>
</ul>
';
$lang['event_help_editglobalcontentpost'] = '<p>Sent after edits to a global content block are saved.</p>
<h4>Parameters</h4>
<ul>
<li>\'global_content\' - Reference to the affected global content block object.</li>
</ul>
';
$lang['event_help_deleteglobalcontentpre'] = '<p>Sent before a global content block is deleted from the system.</p>
<h4>Parameters</h4>
<ul>
<li>\'global_content\' - Reference to the affected global content block object.</li>
</ul>
';
$lang['event_help_deleteglobalcontentpost'] = '<p>Sent after a global content block is deleted from the system.</p>
<h4>Parameters</h4>
<ul>
<li>\'global_content\' - Reference to the affected global content block object.</li>
</ul>
';
$lang['event_help_globalcontentprecompile'] = '<p>Sent before a global content block is sent to smarty for processing.</p>
<h4>Parameters</h4>
<ul>
<li>\'global_content\' - Reference to the affected global content block text.</li>
</ul>
';
$lang['event_help_globalcontentpostcompile'] = '<p>Sent after a global content block has been processed by smarty.</p>
<h4>Parameters</h4>
<ul>
<li>\'global_content\' - Reference to the affected global content block text.</li>
</ul>
';
$lang['event_help_contenteditpre'] = '<p>Sent before edits to content are saved.</p>
<h4>Parameters</h4>
<ul>
<li>\'global_content\' - Reference to the affected content object.</li>
</ul>
';
$lang['event_help_contenteditpost'] = '<p>Sent after edits to content are saved.</p>
<h4>Parameters</h4>
<ul>
<li>\'content\' - Reference to the affected content object.</li>
</ul>
';
$lang['event_help_contentdeletepre'] = '<p>Sent before content is deleted from the system.</p>
<h4>Parameters</h4>
<ul>
<li>\'content\' - Reference to the affected content object.</li>
</ul>
';
$lang['event_help_contentdeletepost'] = '<p>Sent after content is deleted from the system.</p>
<h4>Parameters</h4>
<ul>
<li>\'content\' - Reference to the affected content object.</li>
</ul>
';
$lang['event_help_contentstylesheet'] = '<p>Sent before the sytlesheet is sent to the browser.</p>
<h4>Parameters</h4>
<ul>
<li>\'content\' - Reference to the affected stylesheet text.</li>
</ul>
';
$lang['event_help_contentprecompile'] = '<p>Sent before content is sent to smarty for processing.</p>
<h4>Parameters</h4>
<ul>
<li>\'content\' - Reference to the affected content text.</li>
</ul>
';
$lang['event_help_contentpostcompile'] = '<p>Sent after content has been processed by smarty.</p>
<h4>Parameters</h4>
<ul>
<li>\'content\' - Reference to the affected content text.</li>
</ul>
';
$lang['event_help_contentpostrender'] = '<p>Sent before the combined html is sent to the browser.</p>
<h4>Parameters</h4>
<ul>
<li>\'content\' - Reference to the html text.</li>
</ul>
';
$lang['event_help_smartyprecompile'] = '<p>Sent before any content destined for smarty is sent to for processing.</p>
<h4>Parameters</h4>
<ul>
<li>\'content\' - Reference to the affected text.</li>
</ul>
';
$lang['event_help_smartypostcompile'] = '<p>Sent after any content destined for smarty has been processed.</p>
<h4>Parameters</h4>
<ul>
<li>\'content\' - Reference to the affected text.</li>
</ul>
';
$lang['filterbymodule'] = 'Filter By Module';
$lang['showall'] = 'Show All';
$lang['core'] = 'Core';
$lang['defaultpagecontent'] = 'Default Page Content';
$lang['file_url'] = 'Link to file (instead of URL)';
$lang['no_file_url'] = 'None (Use URL Above)';
$lang['none'] = 'none';
$lang['defaultparentpage'] = 'Default Parent Page';
$lang['error_udt_name_whitespace'] = 'Error: User Defined Tags cannot have spaces in their name.';
$lang['warning_safe_mode'] = '<strong><em>WARNING:</em></strong> PHP Safe mode is enabled.  This will cause difficulty with files uploaded via the web browser interface, including images, theme and module XML packages.  You are advised to contact your site administrator to see about disabling safe mode.';
$lang['test'] = 'Test';
$lang['results'] = 'Results';
$lang['untested'] = 'Not Tested';
$lang['owner'] = 'Owner';
$lang['permissions'] = 'Permissions';
$lang['unknown'] = 'Unknown';
$lang['download'] = 'Download';
$lang['frontendwysiwygtouse']="Frontend wysiwyg";
$lang['all_groups'] = 'All Groups'; //needs translation
$lang['error_type'] = 'Error Type';
$lang['contenttype_errorpage'] = 'Error Page';
$lang['errorpagealreadyinuse'] = 'Error Code Already in Use';
$lang['404description'] = 'Page Not Found';
$lang['usernotfound'] = 'User Not Found.';
$lang['passwordchange'] = 'Please, provide the new password';
$lang['recoveryemailsent'] = 'Email sent to recorded address.  Please check your inbox for further instructions.';
$lang['errorsendingemail'] = 'There was an error sending the email.  Contact your administrator.';
$lang['passwordchangedlogin'] = 'Password changed.  Please log in using the new credentials.';
$lang['nopasswordforrecovery'] = 'No email address set for this user.  Password recovery is not possible.  Please contact your administrator.';
$lang['lostpw'] = 'Forgot your password?';
$lang['lostpwemailsubject'] = '[%s] Password Recovery';
$lang['lostpwemail'] = <<<EOT
You are recieving this e-mail because a request has been made to change the (%s) password associated with this user account (%s).  If you would like to reset the password for this account simply click on the link below or paste it into the url field on your favorite browser:
%s

If you feel this is incorrect or made in error, simply ignore the email and nothing will change.
EOT;
?>
