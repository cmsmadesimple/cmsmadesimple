<?php
$lang['help_function_cms_render_scripts'] = <<<EOT
<h3>What does this do?</h3>
<p>This plugin will use all of the input from various calls to {cms_queue_script}, read each of the files in order that they were queuexd, concatenate them together, and output a single javascript file, along with the appropriate HTML tag to attach the combined file to your HTML output.</p>
<p>This plugin is smart in that it will only generate a new javascrip file if one of the queued script files has changed.  Unless of course, the force flag is enabled.</p>
<p>If defered mode is enabled, then the output HTML will include the defer attribute in the script tag, but will also include support for the special text/cms_javascript script type which allows embedding scripts in module templates, and deferring their execution until after the primary scripts are loaded.</p>
<h3>Usage:</h3>
<ul>
    <li>force - <em>(bool,default=false)</em> - Force the scripts to be concatenated together and a new file output EVEN if the source scripts have not changed.</li>
    <li>nocache - <em>(bool,default=false)</em> - Append information to the output HTML to prevent browsers from caching the output javascript file.</li>
    <li>defer - <em>(bool,optional,default=true)</em> - enable deferred javascript.</li>
    <li>assign - <em>(string,optional)</em> - Optionally assign the output to a smarty variable with this name in local scope.</li>
</ul>

<h3>Example:</h3>
<p>The following example illustrates queueing the inclusion of a jquery library from a file located in /assets/js/jquery.min.js and then rendering the script tags.   On news detail pages the user should be presented with an alert message using deferred execution via the text/cms_javascript script type.</p> 
<h4>In News detail template:</h4>
<pre><code>&lt;script type="text/cms_javascript"&gt;
$(function(){
    alert('this is a test');
};
&lt;/script&gt;</code></pre>
<h4>In your page template:</h4>
<pre><code>{cms_queue_script file="{assets_url}/js/jquery.min.js"}
{cms_render_scripts}&lt;body&gt;
</code></pre>
EOT;
$lang['help_function_cms_queue_script'] = <<<EOT
<h3>What does this do?</h3>
<p>this plugin will take a relative or absolute <strong>FILE</strong> reference <em>(note: it does not understand URLS)</em> and queue it for inclusion via usage of the {cms_render_scripts} plugin.  This is useful for attaching necessary scripts to your rendered HTML output.</p>
<p>A special note, that {cms_queue_script} can be called from within a module action template, to attach scripts that are only relevant to that module action.</p>
<h3>Usage:</h3>
<ul>
   <li>file - <em>(string)</em> - An absolute path to a script file, or a path relative to the CMSMS Assets path, uploads_path or CMSMS root path in that order.</li>
</ul>
<p>Note: This plugin produces no output.</p>

<h3>Example:</h3>
<pre><code>{cms_queue_script file='js/app.js'}</code>
EOT;
$lang['help_function_assets_url'] = <<<EOT
<h3>What does this do?</h3>
<p>This smarty plugin returns the full URL string to the CMSMS assets directory, as configured.  It is useful for referencing stylesheets, fonts, scripts and other assets relative to the assets directory.</p>
<h3>Usage:</h3>
<ul>
   <li>assign - <em>(string,optional)</em> - Optionally assign the output to a smarty variable with this name in local scope.</li>
</ul>

<h3>Example:</h3>
<pre><code>&lt;script src="{assets_url}/js/app.js"&gt;&lt;/script&gt;</code></pre>
EOT;
$lang['help_block_admin_headtext'] = <<<EOT
<h3>What does this do?</h3>
<p>This is a block plugin useful only in admin side actions that allows the module action template to insert HTML content into the HEAD section of the rendered HTML output.  A module action template can insert javascript, or inline styles, or meta tags.</p>
<p>Note: This plugin produces no output.</p>
<h3>Example:</h3>
<pre><code>{admin_headtext}&lt;!-- this text will be injected into the HEAD output --&gt;{/admin_headtext}</code></pre>
EOT;
$lang['help_function_disable_template'] = <<<EOT
<h3>What does this do?</h3>
<p>This smarty plugin is usable under specific conditions, when pre-processing a module action, to disable further processing of the page template.</p>
<p>Particularly for actions that provide a REST service, or output a subset of HTML or javascript, this function can be used to optimize performance so that page template processing is not performed.</p>
<p>This plugin is a wrapper around the <code>CmsApp::disable_template_processing()</code> method.  and is only really useful when the <code>\$config['content_processing_mode']</code> is set to 2.
<p>Note: This plugin produces no output.</p>
<h3>Example:</h3>
<pre><code>{disable_template}</code></pre>
EOT;
$lang['help_function_theme_root'] = <<<EOT
<h3>What does this do?</h3>
<p>This admin plugin provides the ability to get a URL string to the top level of the admin theme directory.  This is useful in admin theme templates to locate images, scripts, css, and other resources.</p>
<h3>Usage:</h3>
<ul>
   <li>assign - <em>(string,optional)</em> - Optionally assign the output to a smarty variable with this name in local scope.</li>
</ul>
EOT;
$lang['help_function_page_selector'] = <<<EOT
<h3>What does this do?</h3>
<p>This admin plugin provides a control to allow selecting a content page, or other item.  This is suitable for allowing a site administrator to select a page that will be stored in a preference.</p>
<h3>Usage:</h3>
<pre><code>{page_selector name=dfltpage value=\$currentpage}</code></pre>
<h3>What Parameters Does it Take?</h3>
<ul>
  <li>name - <em>(string)</em> - The name of the input field.</p>
  <li>value - <em>(int)</em> - The id of the currently selected page.</p>
  <li>allowcurrent - <em>(bool)</em> - Whether or not to allow the currently selected item to be re-selected.  The default value is false.</li>
  <li>allow_all - <em>(bool)</em> - Whether or not to allow inactive content items, or content items that do not have usable links to be selected. The default value is false</li>
  <li>for_child - <em>(bool)</em> - Indicates that we are selecting a parent page for a new content item.  The default value is false.</p>
  </li>
</ul>
EOT;
$lang['help_function_cms_html_options'] = <<<EOT
<h3>What does this do?</h3>
<p>This is a powerful plugin to render options for select elements into html &lt;option&gt; and &lt;optgroup&gt; tags.  Each option can have child elements, it's own title tag, and it's own class attribute.</p>
<h3>Usage:</h3>
<pre><code>{cms_html_options options=\$options [selected=value]}</code></pre>
<h3>What Parameters Does it Take?</h3>
<ul>
  <li>options - <em>(array)</em> - An array of option definitions.</li>
  <li>selected - <em>(string)</em> - The value to automatically select in the dropdown.  must correspond to the value of one of the options.</li>
</ul>
<h4>Options</h4>
<p>Each option is an associative array with two or more of the following members:</p>
<ul>
  <li>label - <em>(<strong>required</strong> string)</em> A label for the option (this is what is presented to the user)</li>
  <li>value - <em>(<strong>required</strong> mixed)</em> Either a string value for the option, or an array of option definitions.
    <p>If the value of an option definition is itself an array of options, then the label will be rendered as an optgroup with children.</p>
  </li>
  <li>title - <em>(string)</em> A title attribute for the option.</li>
  <li>class - <em>(string)</em> A class name for the option.</li>
</ul>

<h3>Example:</h3>
<pre><code>
{\$opts[]=['label'=>'Bird','value'=>'b','title'=>'I have a pet bird']}
{\$opts[]=['label'=>'Fish','value'=>'f']}
{\$sub[]=['label'=>'Small Dog','value'=>'sd']}
{\$sub[]=['label'=>'Medium Dog','value'=>'md']}
{\$sub[]=['label'=>'Large Dog','value'=>'ld']}
{\$opts[]=['label'=>'Dog','value'=>\$sub]}
{\$opts[]=['label'=>'Cat','value'=>'c','class'=>'cat']}
&lt;select name="pet"&gt;
  {cms_html_options options=\$opts selected='md'}
&lt;/select&gt;</code></pre>
EOT;

$lang['help_modifier_cms_date_format'] = <<<EOT
<h3>What does this do?</h3>
<p>This modifier is used to format dates in a suitable format. It uses the standard strftime parameters. If no format string is specified, the system will use the date format string user preference (for logged in users) or the system date format preference.</p>
<p>This modifier is capable of understanding dates in many formats.  i.e: date-time strings output from the database or integer timestamps generated by the time() function.</p>
<h3>Usage:</h3>
<pre><code>{\$some_date_var|cms_date_format[:&lt;format string&gt;]}</code></pre>
<h3>Example:</h3>
<pre><code>{'2012-03-24 22:44:22'|cms_date_format}</code></pre>
EOT;

$lang['help_modifier_cms_escape'] = <<<EOT
<h3>What does this do?</h3>
<p>This modifier is used to escape the string in one of many ways.  This can be used for converting the string to multiple different display formats, or to make user entered data with special characters displayable on a standard web page.</p>
<h3>Usage:</h3>
<pre><code>{\$some_var_with_text|cms_escape[:&lt;escape type&gt;|[&lt;character set&gt;]]}</code></pre>
<h4>Valid escape types:</h4>
<ul>
<li>html <em>(default)</em> - use htmlspecialchars.</li>
<li>htmlall - use htmlentities.</li>
<li>url - raw url encode all entities.</li>
<li>urlpathinfo - Similar to the url escape type, but also encode /.</li>
<li>quotes - Escape unescaped single quotes.</li>
<li>hex - Escape every character into hex.</li>
<li>hexentity - Hex encode every character.</li>
<li>decentity - Decimal encode every character.</li>
<li>javascript - Escape quotes, backslashes, newlines etc.</li>
<li>mail - Encode an email address into something that is safe to display.</li>
<li>nonstd - Escape non standard characters, such as document quotes.</li>
</ul>
<h4>Character Set::</h4>
<p>If the character set is not specified, utf-8 is assumed. The character set is only applicable to the &quot;html&quot; and &quot;htmlall&quot; escape types.</p>
EOT;

$lang['help_modifier_relative_time'] = <<<EOT
<h3>What does this do?</h3>
  <p>This modifier converts an integer timestamp, or time/date string into a human readable amount of time from, or to now.  i.e:  &quot;3 hours ago.&quot;</p>
<h3>What parameters does it take?</h3>
 <p>This modifier does not accept any optional parameters.</p>
<h3>Example:</h3>
  <code><pre>{\$some_timestamp|relative_time}</code></pre>
EOT;

$lang['help_modifier_summarize'] = <<<EOT
<h3>What does this do?</h3>
<p>This modifier is used to truncate a long sequence of text to a limited number of &quot;words&quot;.</p>
<h3>Usage:</h3>
<pre><code>{\$some_var_with_long_text|summarize:&lt;number&gt;}</code></pre>
<h3>Example:</h3>
<p>The following example would strip all html tags from the content and truncate it after 50 words.</p>
<pre><code>{content|strip_tags|summarize:50}</code></pre>
EOT;

$lang['help_function_admin_icon'] = <<<EOT
<h3>What does this do?</h3>
<p>This is an admin side only plugin to allow modules to easily display icons from the current admin theme.  These icons are useful in link building or in displaying status information.</p>
<h3>What parameters does it take?</h3>
<ul>
  <li>icon - <strong>(required)</strong> - The filename of the icon. i.e: run.gif</li>
  <li>height - <em>(optional)</em> - The height (in pixels) of the icon.</li>
  <li>width - <em>(optional)</em> - The width (in pixels) of the icon.</li>
  <li>alt - <em>(optional)</em> - Optional text for the img tag if the filename specified is not available.</li>
  <li>rel - <em>(optional)</em> - An optional rel attribute for the img tag.</li>
  <li>class - <em>(optional)</em> - An optional class attribute for the img tag.</li>
  <li>id - <em>(optional)</em> - An optional id attribute for the img tag.</li>
  <li>title - <em>(optional)</em> - An optional title attribute for the img tag.</li>
  <li>accesskey - <em>(optional)</em> - An optional access key character for the img tag.</li>
  <li>assign - <em>(optional)</em> - Assign the tag output to the named smarty variable.</li>
</ul>
<h3>Example:</h3>
<pre><code>{admin_icon icon='edit.gif' class='editicon'}</code></pre>
EOT;

$lang['help_function_cms_action_url'] = <<<EOT
<h3>What does this do?</h3>
<p>This is a smart plugin useful for generating a URL to a module action. This plugin is useful for module developers who are generating links (either for Ajax or or in the admin interface) to perform different functionality or display different data.</p>
<h3>What parameters does it take?</h3>
<ul>
  <li>module - <em>(optional)</em> - The module name to generate a URL for.  This parameter is not necessary if generating a URL from within a module action to an action within the same module.</li>
  <li>action - <strong>(required)</strong> - The action name to generate a URL to.</li>
  <li>returnid - <em>(optional)</em> - The integer pageid to display the results of the action in.  This parameter is not necessary if the action is to be displayed on the current page, or if the URL is to an admin action from within an admin action.</li>
  <li>mid - <em>(optional)</em> - The module action id.  This defaults to &quot;m1_&quot; for admin actions, and &quot;cntnt01&quot; for frontend actions.</li>
  <li>forjs - <em>(optional)</em> - An optional integer indicating that the generated URL should be suitable for use in JavaScript.</li>
  <li>assign - <em>(optional)</em> - Assign the output URL to the named smarty variable.</li>
</ul>
<p><strong>Note:</strong> Any other parameters not accepted by this plugin are automatically passed to the called module action on the generated URL.</p>
<h3>Example:</h3>
<pre><code>{cms_action_url module=News action=defaultadmin}</code><pre>
EOT;

$lang['help_function_cms_admin_user'] = <<<EOT
<h3>What does this do?</h3>
<p>This admin only plugin outputs information about the specified admin user id.</p>
<h3>What parameters does it take?</h3>
<ul>
  <li>uid - <strong>required</strong> - An integer user id representing a valid admin account.</li>
  <li>mode - <em>(optional)</em> - The operating mode.  Possible values are:
    <ul>
      <li>username <strong>default</strong> - output the username for the specified uid.</li>
      <li>email - output the email address for the specified uid.</li>
      <li>firstname - output the first name for the specified uid.</li>
      <li>lastname - output the surname name for the specified uid.</li>
      <li>fullname - output the full name for the specified uid.</li>
    </ul>
  </li>
  <li>assign - <em>(optional)</em> - Assign the output to the named smarty variable.</li>
</ul>
<h3>Example:</h3>
<pre><code>{cms_admin_user uid=1 mode=email}</code></pre>
EOT;

$lang['help_function_cms_get_language'] = <<<EOT
<h3>What does this do?</h3>
<p>This plugin returns the current CMSMS language name. The language is used for translation strings and date formatting.</p>
<h3>What parameters does it take?</h3>
<ul>
<li><em>(optional)assign</em> - Assign the output of the plugin to the named smarty variable.</li>
</ul>
EOT;

$lang['help_function_cms_help'] = <<<EOT
<h3>What does this do?</h3>
<p>This is an admin only plugin to use to generate a link that when clicked will generate popup help for a particular item.</p>
<p>This plugin is typically used from module admin templates to display end user help in a popup window for an input field, column, or other important information.</p>
<h3>What parameters does it take?</h3>
<ul>
<li>key - <strong>(required string)</strong> - The second part in a unique key to identify the help string to display.  This is usually the key from the appropriate realms lang file.</li>
<li>realm - <em>(optional string)</em> - The first part in a unique key to identify the help string.  If this parameter is not specified, and this plugin is called from within a module action then the current module name is used.  If no module name can be found then &quot;help&quot; is used as the lang realm.</li>
<li>title - <em>(optional string)</em> - Help box title</li>
<li>assign - <em>(optional string)</em> - Assign the output to the named smarty variable.</li>
</ul>
<h3>Example:</h3>
<pre><code>{cms_help key2='help_field_username' title=&#36;foo}</code></pre>
EOT;

$lang['help_function_cms_init_editor'] = <<<EOT
<h3>What does this do?</h3>
  <p>This plugin is used to initialize the selected WYSIWYG editor for display when WYSIWYG functionalities are required for frontend data submission.  This module will find the selected frontend WYSIWYG <em>(see global settings).</em>, determine if it has been requested, and if so generate the appropriate html code <em>(usually JavaScript links)</em> so that the WYSIWYG will initialize properly when the page is loaded.  If no WYSIWYG editors have been requested for the frontend request this plugin will produce no output.</p>
<h3>How do I use it?</h3>
<p>The first thing you must do is select the frontend WYSIWYG editor to use in the global settings page of the admin console.  Next If you use frontend WYSIWYG editors on numerous pages, it may be best to place the {cms_init_editor} plugin directly into your page template.  If you only require the WYSIWYG editor to be enabled on a limited amount of pages you may just place it into the &quot;Page Specific Metadata&quot; field in each page.</p>
<h3>What parameters does it take?</h3>
<ul>
<li><em>(optional)assign</em> - Assign the output of the plugin to the named smarty variable.</li>
</ul>
EOT;

$lang['help_function_cms_lang_info'] = <<<EOT
<h3>What does this do?</h3>
<p>This plugin returns an object containing the information that CMSMS has about the selected language.  This can include locale information, encodings, language aliases etc.</p>
<h3>What parameters does it take?</h3>
<ul>
<li><em>(optional)lang</em> - The language to return information for. If the lang parameter is not specified then the information for the current CMSMS language is used.</li>
<li><em>(optional)assign</em> - Assign the output of the plugin to the named smarty variable.</li>
</ul>
<h3>Example:</h3>
<pre>{cms_lang_info assign='nls'}{\$nls->locale()}</pre>
<h3>See Also:</h3>
<p>the CmsNls class documentation.</p>
EOT;

$lang['help_function_cms_pageoptions'] = <<<EOT
<h3>What does this do?</h3>
 <p>This is a simple plugin to generate a sequence of &lt;option&gt; tags for a dropdown list that represent page numbers in a pagination.</p>
 <p>Given the number of pages, and the current page this plugin will generate a list of page numbers that allow quick navigation to a subset of the pages.</p>
<h3>What parameters does it take?</h3>
  <ul>
    <li>numpages - <strong>required integer</strong> - The total number of available pages to display.</li>
    <li>curpage - <strong>required integer</strong> - The current page number (must be greater than 0 and less than or equal to &quot;numpages&quot;</li>
    <li>surround - <em>(optional integer)</em> - The number of items to surround the current page by.  The default value for this parameter is 3.</li>
    <li>bare - <em>(optional boolean)</em> - Do not output &lt;option&gt; tags,  Instead output just a simple array suitable for further manipulation in smarty.</li>
  </ul>
<h3>Example:</h3>
<pre><code>&lt;select name="{\$actionid}pagenum"&gt;{cms_pageoptions numpages=50 curpage=14}&lt;/select&gt;</code></pre>
EOT;

$lang['help_function_share_data'] = <<<EOT
<h3>What does this do?</h3>
<p>This plugin is used to copy one, or more active smarty variables to the parent or global scope.</p>
<h3>What parameters does it take?</h3>
<ul>
<li>scope - <strong>optional string</strong> - The target scope to copy variables to.  Possible values are &quot;parent&quot; <em>(the default)</em> or &quot;global&quot; to copy the data to the global smarty object for subsequent use throughout the page.</li>
<li>vars - <strong>required mixed</strong> - Either an array of string variable names, or a comma separated list of string variable names.</li>
</ul>
<h3>Example:</h3>
<pre><code>{share_data scope=global data='title,canonical'}</code></pre>
<h3>Note:</h3>
<p>This plugin will not accept array accessors or object members as variable names.  i.e: <code>]\$foo[1]</code> or <code>{\$foo->bar}</code> will not work.</p>
EOT;

$lang['help_function_cms_yesno'] = <<<EOT
<h3>What does this do?</h3>
<p>This is a simple plugin used in form generation to create a set of options for a &lt;select&gt; representing a yes/no choice.</p>
<p>This plugin will generate translated yes/no options, with the proper selected value.</p>
<h3>What parameters does it take?</h3>
<ul>
  <li>selected - <em>(optional integer)</em> - either 0 <em>(no)</em> or 1 <em>(yes)</em></li>
  <li>assign - <em>(optional string)</em> - Assign the output to the named smarty variable.</li>
</ul>
<h3>Example:</h3>
<pre><code>&lt;select name=&quot;{\$actionid}opt&quot;&gt;{cms_yesno selected=\$opt}&lt;/select&gt;</code></pre>
EOT;

$lang['help_function_module_available'] = <<<EOT
<h3>What does this do?</h3>
<p>A plugin to test whether a given module (by name) is installed, and available for use.</p>
<h3>What parameters does it take?</h3>
<ul>
<li><strong>(required)module</strong> - (string) The name of the module.</li>
<li><em>(optional)assign</em> - Assign the output of the plugin to the named smarty variable.</li>
</ul>
<h3>Example:</h3>
{module_available module='News' assign='havenews'}{if \$havenews}{cms_module module=News}{/if}
<h3>Note:</h3>
<p>You cannot use the short form of the module call, i.e: <em>{News}</em> in this type of expression.</p>
EOT;

$lang['help_function_cms_set_language'] = <<<EOT
<h3>What does this do?</h3>
<p>This plugin attempts to set the current language for use by translation strings and date formatting to the desired language.  The language specified must be known to CMSMS (The nls file must exist).  When this function is called, (and unless overridden in the config.php) an attempt will be made to set the locale to the local associated with the language.  The locale for the language must be installed on the server.</p>
<h3>What parameters does it take?</h3>
<ul>
<li><strong>(required)lang</strong> - The desired language.  The language must be known to the CMSMS installation (nls file must exist).</li>
</ul>
EOT;

$lang['help_function_browser_lang'] = <<<EOT
<h3>What does this do?</h3>
  <p>This plugin detects and outputs the language that the users browser accepts, and cross references it with a list of allowed languages to determine a language value for the session.</p>
<h3>How do I use it?</h3>
<p>Insert the tag early into your page template <em>(it can go above the &lt;head&gt; section if you want)</em> and provide it the name of the default language, and the accepted languages (only two character language names are accepted), then do something with the result.  i.e:</p>
	     <pre><code>{browser_lang accepted=&quot;de,fr,en,es&quot; default=en assign=tmp}{session_put var=lang val=\$tmp}</code></pre>
<p><em>({session_put} is a plugin provided by the CGSimpleSmarty module)</em></p>
<h3>What Parameters does it Take?</h3>
<ul>
<li><strong>accepted <em>(required)</em></strong><br/> - A comma separated list of two character language names that are accepted.</li>
<li>default<br/>- <em>(optional)</em> A default language to output if no accepted language was supported by the browser.  en is used if no other value is specified.</li>
<li>assign<br/>- <em>(optional)</em> The name of the smarty variable to assign the results to.  If not specified the results of this function are returned.</li>
</ul>
EOT;

$lang['help_function_content_module'] = <<<EOT
<h3>What does this do?</h3>
<p>This content block type allows interfacing with different modules to create different content block types.</p>
<p>Some modules can define content block types for use in module templates.  i.e: The FrontEndUsers module may define a group list content block type.  It will then indicate how you can use the content_module tag to utilize that block type within your templates.</p>
<p><strong>Note:</strong> This block type must be used only with compatible modules.  You should not use this in any way except for as guided by add-on modules.</p>
<p>This tag accepts a few parameters, and passes all other parameters to the module for processing.</p>
<p>Parameters:
 <ul>
 <li><strong>(required)</strong>module - The name of the module that will provide this content block. This module must be installed and available</li>
 <li><strong>(required)</strong>block  - The name of the content block.</li>
 <li><em>(optional)</em>label - A label for the content block for use when editing the page.</li>
 <li><em>(optional)</em> required - Allows specifying that the content block must contain some text.</em></li>
 <li><em>(optional)</em> tab - The desired tab to display this field on in the edit form..</li>
 <li><em>(optional)</em> priority (integer) - Allows specifying an integer priority for the block within the tab.</li>
 <li><em>(optional)</em> assign (string) - Assign the results to a smarty variable with that name.</li>
 </ul>
</p>
EOT;

$lang['help_function_cms_stylesheet'] = <<<EOT
	<h3>What does this do?</h3>
  <p>A replacement for the {stylesheet} tag, this tag provides caching of css files by generating static files in the tmp/cache directory, and smarty processing of the individual stylesheets.</p>
  <p>This plugin retrieves stylesheet information from the system.  By default, it grabs all of the stylesheets attached to the current template in the order specified by the designer, and combines them into a single stylesheet tag.</p>
  <p>Generated stylesheets are uniquely named according to the last modification date in the database, and are only generated if a stylesheet has changed.</p>
  <p>This tag is the replacement for the {stylesheet} tag.</p>
  <h3>How do I use it?</h3>
  <p>Just insert the tag into your template/page's head section like: <code>{cms_stylesheet}</code></p>
  <h3>What parameters does it take?</h3>
  <ul>
  <li><em>(optional)</em> name - Instead of getting all stylesheets for the given page, it will only get one specifically named one, whether it's attached to the current template or not.</li>
  <li><em>(optional)</em> nocombine - (boolean, default false) If enabled, and there are multiple stylesheets associated with the template, the stylesheets will be output as separate tags rather than combined into a single tag.</li>
  <li><em>(optional)</em> nolinks - (boolean, default false) If enabled, the stylesheets will be output as a URL without &lt;link&gt; tag.</li>
  <li><em>(optional)</em> designid - If designid is defined, this will return stylesheets associated with that design instead of the current one.</li>
  <li><em>(optional)</em> media - <strong>[deprecated]</strong> - When used in conjunction with the name parameter this parameter will allow you to override the media type for that stylesheet.  When used in conjunction with the templateid parameter, the media parameter will only output stylesheet tags for those stylesheets that are marked as compatible with the specified media type.</li>
  </ul>
  <h3>Smarty Processing</h3>
  <p>When generating css files this system passes the stylesheets retrieved from the database through smarty.  The smarty delimiters have been changed from the CMSMS standard { and } to [[ and ]] respectively to ease transition in stylesheets.  This allows creating smarty variables i.e.: [[assign var='red' value='#900']] at the top of the stylesheet, and then using these variables later in the stylesheet, i.e:</p>
<pre>
<code>
h3 .error { color: [[\$red]]; }<br/>
</code>
</pre>
<p>Because the cached files are generated in the tmp/cache directory of the CMSMS installation, the CSS relative working directory is not the root of the website.  Therefore any images, or other tags that require a url should use the [[root_url]] tag to force it to be an absolute url. i.e:</p>
<pre>
<code>
h3 .error { background: url([[root_url]]/uploads/images/error_background.gif); }<br/>
</code>
</pre>
<p><strong>Note:</strong> Due to the caching nature of the plugin, smarty variables should be placed at the top of EACH stylesheet that is attached to a template.</p>
EOT;

$lang['help_function_page_attr'] = <<<EOT
<h3>What does this do?</h3>
<p>This tag can be used to return the value of the attributes of a certain page.</p>
<h3>How do I use it?</h3>
<p>Insert the tag into the template like: <code>{page_attr key="extra1"}</code>.</p>
<h3>What parameters does it take?</h3>
<ul>
  <li><em>(optional)</em> page (int|string) - An optional page id or alias to fetch the content from.  If not specified, the current page is assumed.</li>
  <li><strong>key [required]</strong> The key to return the attribute of.
    <p>The key can either be a block name, or from a set of standard properties associated with a content page.  Some of the accepted standard properties are:</p>
    <ul>
      <li>_dflt_ - (string) The value for the default content block (an alias for content_en).</li>
      <li>title</li>
      <li>description</li>
      <li>alias - (string) The unique page alias.</li>
      <li>pageattr - (string) The value of the page specific smarty data attribute./li>
      <li>id - (int) The unique page id.</li>
      <li>created_date - (string date) Date of the creation of the content object.</li>
      <li>modified_date - (string date) Date of the last modification of the content object.</li>
      <li>last_modified_by - (int) UID of the user who last modified the page.</li>
      <li>owner - (int) UID of the page owner.</li>
      <li>image - (string) The path to the image assocated with the content page.</li>
      <li>thumbnail - (string) The path to the thumbnail assocated with the content page.</li>
      <li>extra1 - (string) The value of the extra1 attribute.</li>
      <li>extra2 - (string) The value of the extra2 attribute.</li>
      <li>extra3 - (string) The value of the extra3 attribute.</li>
      <li>pageattr - (string) The value of the page specific smarty data attribute./li>
    </ul>
    <p><strong>Note:</strong> The list above is not inclusive.  You can also retrieve the unparsed contents of additional content blocks or properties added by third party 
  </li>
  <li><em>(optional)</em> inactive (boolean) - Allows reading page attributes from inactive pages.</li>
  <li><em>(optional)</em> assign (string) - Assign the results to a smarty variable with that name.</li>
</ul>
<h3>Returns:</h3>
<p><strong>string</strong> - The actual value of the content block from the database for the specified block and page.</p>
<p><strong>Note:</strong> - The output of this plugin is not passed through smarty or cleaned for display.   If displaying the data you must convert string data to entities, and/or pass it through smarty.</p>
EOT;

$lang['help_function_page_image'] = <<<EOT
<h3>What does this do?</h3>
<p>This tag can be used to return the value of the image or thumbnail fields of a certain page.</p>
<h3>How do I use it?</h3>
<p>Insert the tag into the template like: <code>{page_image}</code>.</p>
<h3>What parameters does it take?</h3>
<ul>
  <li><em>(optional)</em> thumbnail (bool) - Optionally display the value of the thumbnail property instead of the image property.</li>
  <li><em>(optional)</em> full (bool)- Optionally output the full URL to the image relative to the image uploads path.</li>
   <li><em>(optional)</em> tag (bool) - Optionally output a full image tag, if the property value is not empty.  If the tag argument is enabled, full is implied.</li>
  <li><em>(optional)</em> assign (string) - Assign the results to a smarty variable with that name.</li>
</ul>
<h3>More...</h3>
<p>If the tag argument is enabled, and the property value is not empty, this will trigger a full HTML img tag to be output.  Any arguments to the plugin not listed above will automatically be included in the resulting img tag.  i.e:  <code>{page_image tag=true class="pageimage" id="someid" title="testing"}</code>.</p>
<p>If the plugin is outputting a full img tag, and the alt argument has not been provided, then the value of the property will be used for the alt attribute of the img tag.</p>
EOT;

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
<li><em>(optional)</em> assign (string) - Assign the results to a smarty variable with that name.</li>
</ul>
EOT;

$lang['help_function_content_image'] = <<<EOT
<h3>What does this do?</h3>
<p>This plugin allows template designers to prompt users to select an image file when editing the content of a page. It behaves similarly to the content plugin, for additional content blocks.</p>
<h3>How do I use it?</h3>
<p>Just insert the tag into your page template like: <code>{content_image block='image1'}</code>.</p>
<h3>What parameters does it take?</h3>
<ul>
  <li><strong>(required)</strong> block (string) - The name for this additional content block.
    <p>Example:</p>
    <pre>{content_image block='image1'}</pre><br/>
  </li>
  <li><em>(optional)</em> label (sring) - A label or prompt for this content block in the edit content page.  If not specified, the block name will be used.</li>
  <li><em>(optional)</em> dir (string) - The name of a directory (relative to the uploads directory, from which to select image files. If not specified, the preference from the global settings page will be used.  If that preference is empty, the uploads directory will be used.
  <p>Example: use images from the uploads/images directory.</p>
  <pre><code>{content_image block='image1' dir='images'}</code></pre><br/>
  </li>
  <li><em>(optional)</em> default (string) - Use to set a default image used when no image is selected.</li>
  <li><em>(optional)</em> urlonly (bool) - output only the url to the image, ignoring all parameters like id, name, width, height, etc.</li>
  <li><em>(optional)</em> tab (string) The desired tab to display this field on in the edit form..</li>
  <li><em>(optional)</em> exclude (string) - Specify a prefix of files to exclude.  i.e: thumb_ </li>
  <li><em>(optional)</em> sort (bool) - optionally sort the options. Default is to not sort.</li>
  <li><em>(optional)</em> priority (integer) - Allows specifying an integer priority for the block within the tab.</li>
  <li><em>(optional)</em> assign (string) - Assign the results to a smarty variable with that name.</li>
</ul>
<h3>More...</h3>
<p><strong>Note:</strong> As of version 2.2, if this content block contains no value, then no output is generated.</p>
                                                            <p>In addition to the arguments listed above, this plugin will accept any number of additional arguments and forward them directly to the generated img tag if any.  i.e: <code>{content_image block='img1' id="id_img1" class="page-image" title='an image block' data-foo=bar}</code>
EOT;

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
<p><em>(optional)</em> assign (string) - Assign the results to a smarty variable with that name.</p>
EOT;

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
		<li><em>(optional)</em> <tt>assign</tt> - Assign the results to the named smarty variable.</li>
	</ul>
EOT;

$lang['help_function_tab_end'] = <<<EOT
<h3>What does this do?</h3>
  <p>This plugin outputs the HTML code to denote the end of a content area.</p>
<h3>How do I use it?</h3>
<p>The following code creates a tabbed content area with two tabs.</p>
<pre><code>{tab_header name='tab1' label='Tab One'}
{tab_header name='tab2' label='Tab Two'}
{tab_start name='tab1'}
&lt;p&gt;This is tab One&lt;/p&gt;
{tab_start name='tab2'}
&lt;p&gt;This is tab Two&lt;/p&gt;
<span style="color: blue;">{tab_end}</span></code></pre>
<h3>What parameters does it take?</h3>
<ul>
   <li>assign - <em>(optional)</em> - Assign the output to the named smarty variable.</li>
</ul>
<h3>See Also:</h3>
  <ul>
    <li>{tab_header}</li>
    <li>{tab_start}</li>
  </ul>
EOT;

$lang['help_function_tab_header'] = <<<EOT
<h3>What does this do?</h3>
  <p>This tag generates the HTML code to delimit the header for a single tab in a tabbed content area.</p>
<h3>How do I use it?</h3>
<p>The following code creates a tabbed content area with two tabs.</p>
<pre><code><span style="color: blue;">{tab_header name='tab1' label='Tab One'}</span>
<span style="color: blue;">{tab_header name='tab2' label='Tab Two'}</span>
{tab_start name='tab1'}
&lt;p&gt;This is tab One&lt;/p&gt;
{tab_start name='tab2'}
&lt;p&gt;This is tab Two&lt;/p&gt;
{tab_end}</code></pre>
<p><strong>Note:</strong> <code>{tab_start}</code> must be called with the names in the same order that they were provided to <code>{tab_header}</code></p>
<h3>What parameters does it take?</h3>
<ul>
   <li><strong>name - required string</strong> - The name of the tab.  Must match the name of a tab passed to {tab_header}</li>
   <li>label - <em>optional string</em> - The human readable label for the tab.  If not specified, the tab name will be used.</li>
   <li>active - <em>optional mixed./em> - Indicates whether this is the active tab or not.  You may pass in the name (string) of the active tab in a sequence of tab headers, or a boolean value.</li>
   <li>assign - <em>(optional)</em> - Assign the output to the named smarty variable.</li>
</ul>
<h3>See Also:</h3>
  <ul>
    <li>{tab_start}</li>
    <li>{tab_end}</li>
  </ul>
EOT;

$lang['help_function_tab_start'] = <<<EOT
<h3>What does this do?</h3>
  <p>This plugin provides the html code to delimit the start of content for a specific tab in a tabbed content area.</p>
<h3>How do I use it?</h3>
<p>The following code creates a tabbed content area with two tabs.</p>
<pre><code>{tab_header name='tab1' label='Tab One'}
{tab_header name='tab2' label='Tab Two'}
<span style="color: blue;">{tab_start name='tab1'}</span>
&lt;p&gt;This is tab One&lt;/p&gt;
<span style="color: blue;">{tab_start name='tab2'}</span>
&lt;p&gt;This is tab Two&lt;/p&gt;
{tab_end}</code></pre>
<p><strong>Note:</strong> <code>{tab_start}</code> must be called with the names in the same order that they were provided to <code>{tab_header}</code></p>
<h3>What parameters does it take?</h3>
<ul>
   <li><strong>name - required</strong> - The name of the tab.  Must match the name of a tab passed to {tab_header}</li>
   <li>assign - <em>(optional)</em> - Assign the output to the named smarty variable.</li>
</ul>
<h3>See Also:</h3>
  <ul>
    <li>{tab_header}</li>
    <li>{tab_end}</li>
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
        <p><strong>Deprecated:</strong> This function is deprecated and will be removed in later versions of CMSMS.</p>
	<p>Gets stylesheet information from the system.  By default, it grabs all of the stylesheets attached to the current template.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page's head section like: <code>{stylesheet}</code></p>
	<h3>What parameters does it take?</h3>
	<ul>
		<li><em>(optional)</em>name - Instead of getting all stylesheets for the given page, it will only get one specifically named one, whether it's attached to the current template or not.</li>
		<li><em>(optional)</em>media - If name is defined, this allows you set a different media type for that stylesheet.</li>
    <li><em>(optional)</em>templateid - If templateid is defined, this will return stylesheets associated with that template instead of the current one.</li>
	<li><em>(optional)</em> <tt>assign</tt> - Assign the results to the named smarty variable.</li>
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

$lang['help_function_cms_textarea'] = <<<EOT
  <h3>What does this do?</h3>
  <p>This smarty plugin is used when building admin forms to generate a textarea field.  It has various parameter which allow controlling whether a WYSIWYG plugin is used <em>(if available)</em> or a syntax highlighter, and for influencing the behavior of those modules, and the size and appearance of the textarea.</p>
  <h3>How do I use it?</h3>
    <p>The simplest way to use this plugin is by specifying <code>{cms_textarea name=&quot;something&quot;}</code>.  This will create a simple text area without WYSIWYG or syntax highlighter modules enabled, with the specified name.</p>
    <p>Next you can specify the default value for the text area by using the &quot;text&quot; or &quot;value&quot; parameters.</p>
  <h3>What parameters does it take?</h3>
  <ul>
    <li>name - required string : name attribute for the text area element.</li>
    <li>prefix - optional string : optional prefix for the name attribute.</li>
    <li>class - optional string : class attribute for the text area element.  Additional classes may be added automatically.</li>
    <li>classname - alias for the class parameter.</li>
    <li>forcemodule - optional string : used to specify the WYSIWYG or syntax highlighter module to enable.  If specified, and available, the module name will be added o the class attribute.</li>
    <li>enablewysiwyg - optional boolean : used to specify whether a WYSIWYG textarea is required.  Sets the language to &quot;html&quot;</li>
    <li>wantedsyntax - optional string used to specify the language (html,css,php,smarty...) to use.  If non empty indicates that a syntax highlighter module is requested.</li>
    <li>type - alias for the wantedsyntax parameter.</li>
    <li>cols - optional integer : columns of the text area (admin theme css or the syntax/WYSIWYG module may override this).</li>
    <li>width - alias for the cols parameter.</li>
    <li>rows - optional integer : rows of the text area (admin theme css or the syntax/WYSIWYG module may override this).</li>
    <li>height - alias for the rows parameter.</li>
    <li>maxlength - optional integer : maxlength attribute of the text area (syntax/WYSIWYG module may ignore this).</li>
    <li>required  - optional boolean : indicates a required field.</li>
    <li>disabled  - optional boolean : indicates a disabled field.</li>
    <li>readonly  - optional boolean : indicates a readonly field.</li>
    <li>placeholder - optional string : placeholder attribute of the text area (syntax/WYSIWYG module may ignore this).</li>
    <li>value - optional string : default text for the text area, will undergo entity conversion.</li>
    <li>text - alias for the value parameter</li>
    <li>cssname - optional string : pass this stylesheet name to the WYSIWYG module if a WYSIWYG module is enabled.</li>
    <li>addtext - optional string : additional text to add to the textarea tag.</li>
    <li>assign - optional string : assign the output html to the named smarty variable.</li>
  </ul>
EOT;

$lang['help_function_root_url'] = <<<EOT
	<h3>What does this do?</h3>
	<p>Prints the root url location for the site.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{root_url}</code></p>
	<h3>What parameters does it take?</h3>
	<p><em>(optional)</em> assign (string) - Assign the results to a smarty variable with that name.</p>
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
  <li><em>(optional)</em> assign (string) - Assign the results to a smarty variable with that name.</li>
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
 	 <li><p><em>(optional)</em> showtitle='true' - Shows the title attribute if it exists as well (true|false).</p><p>Example: {recently_updated showtitle='true'}</p></li>
	 <li><p><em>(optional)</em> css_class='some_name' - Warp a div tag with this class around the list.</p><p>Example: {recently_updated css_class='some_name'}</p></li>
	 <li><p><em>(optional)</em> dateformat='d.m.y h:m' - default is d.m.y h:m , use the format you whish (php -date- format)</p><p>Example: {recently_updated dateformat='D M j G:i:s T Y'}</p></li>
	 <li><em>(optional)</em> <tt>assign</tt> - Assign the results to the named smarty variable.</li>
	</ul>
	<p>or combined:</p>
	<pre>{recently_updated number='15' showtitle='false' leadin='Last Change: ' css_class='my_changes' dateformat='D M j G:i:s T Y'}</pre>
EOT;

$lang['help_function_print'] = <<<EOT
	<h3>What does this do?</h3>
	<p>This is actually just a wrapper tag for the CMSPrinting module to make the tag syntax easier.
	Instead of having to use <code>{cms_module module='CMSPrinting'}</code> you can now just use <code>{print}</code> to insert the module on pages and templates.
	</p>
	<h3>How do I use it?</h3>
	<p>Just put <code>{print}</code> on a page or in a template. For help about the CMSPrinting module, what parameters it takes etc., please refer to the CMSPrinting module help.</p>
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
                <li><em>(optional)</em>assign - Assign the results to the named smarty variable.</li>
        </ul>
EOT;

$lang['help_function_metadata'] = <<<EOT
	<h3>What does this do?</h3>
	<p>Displays the metadata for this page. Both global metadata from the global settings page and metadata for each page will be shown.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template like: <code>{metadata}</code></p>
	<h3>What parameters does it take?</h3>
	<ul>
		<li><em>(optional)</em>showbase (true/false) - If set to false, The base tag will not be output.</li>
		<li><em>(optional)</em> <tt>assign</tt> - Assign the results to the named smarty variable.</li>
	</ul>
EOT;

$lang['help_function_menu_text'] = <<<EOT
	<h3>What does this do?</h3>
	<p>Prints the menu text of the page.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{menu_text}</code></p>
	<h3>What parameters does it take?</h3>
	<p><em>(optional)</em> assign (string) - Assign the results to a smarty variable with that name.</p>
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
				<li><em>(optional)</em> <tt>assign</tt> - Assign the results to the named smarty variable.</li>
        </ul>
EOT;

$lang['help_function_image'] = <<<EOT
  <h3>What does this do?</h3>
  <p>Creates an image tag to an image stored within your images directory</p>
  <h3>How do I use it?</h3>
  <p class="warning">This plugin is deprecated and will be removed from the core at a later date.</p>
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
	 <li><em>(optional)</em> <tt>assign</tt> - Assign the results to the named smarty variable.</li>
  </ul>
EOT;

$lang['help_function_html_blob'] = <<<EOT
	<h3>What does this do?</h3>
	<p>See the help for global_content for a description.</p>
EOT;

$lang['help_function_google_search'] = <<<EOT
	<h3>What does this do?</h3>
	<p>Search's your website using Google's search engine.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{google_search}</code><br />
	<br />
	Note: Google needs to have your website indexed for this to work. You can submit your website to Google <a href="http://www.google.com/addurl.html">here</a>.</p>
	<h3>What if I want to change the look of the textbox or button?</h3>
	<p>The look of the textbox and button can be changed via css. The textbox is given an id of textSearch and the button is given an id of buttonSearch.</p>

	<h3>What parameters does it take?</h3>
	<ul>
		<li><em>(optional)</em> domain - This tells google the website domain to search. This script tries to determine this automatically.</li>
		<li><em>(optional)</em> buttonText - The text you want to display on the search button. The default is "Search Site".</li>
		<li><em>(optional)</em> <tt>assign</tt> - Assign the results to the named smarty variable.</li>
	</ul>
EOT;

$lang['help_function_global_content'] = <<<EOT
	<h3>What does this do?</h3>
	<p>Inserts a global content block into your template or page.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{global_content name='myblock'}</code>, where name is the name given to the block when it was created.</p>
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
	<p><em>(optional)</em> assign (string) - Assign the results to a smarty variable with that name.</p>
EOT;

$lang['help_function_page_error'] = <<<EOT
<h3>What does this do?</h3>
<p>This is an admin plugin that displays an error in a CMSMS admin page.</p>
<h3>What parameters does it take?</h3>
<ul>
  <li>msg - <strong>required string</strong> - The error message to display.</li>
  <li>assign - <em>(optional)</em> - Assign the output to the named smarty variable.</li>
</ul>
<h3>Example:</h3>
<pre><code>{page_error msg='Error Encountered'}</code></pre>
EOT;

$lang['help_function_page_warning'] = <<<EOT
<h3>What does this do?</h3>
<p>This is an admin plugin that displays a warning in a CMSMS admin page.</p>
<h3>What parameters does it take?</h3>
<ul>
  <li>msg - <strong>required string</strong> - The warning message to display.</li>
  <li>assign - <em>(optional)</em> - Assign the output to the named smarty variable.</li>
</ul>
<h3>Example:</h3>
<pre><code>{page_warning msg='Something smells fishy'}</code></pre>
EOT;

$lang['help_function_uploads_url'] = <<<EOT
	<h3>What does this do?</h3>
	<p>Prints the uploads url location for the site.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{uploads_url}</code></p>
	<h3>What parameters does it take?</h3>
	<p><em>(optional)</em> assign (string) - Assign the results to a smarty variable with that name.</p>
EOT;

$lang['help_function_embed'] = <<<EOT
	<h3>What does this do?</h3>
	<p>Enable inclusion (embedding) of any other application into the CMS. The most usual use could be a forum.
	This implementation is using IFRAMES so older browsers can have problems. Sorry bu this is the only known way
	that works without modifying the embedded application.</p>
	<h3>How do I use it?</h3>
        <ul>
        <li>a) Add <code>{embed header=true}</code> into the head section of your page template, or into the metadata section in the options tab of a content page.  This will ensure that the required JavaScript gets included.   If you insert this tag into the metadata section in the options tab of a content page you must ensure that <code>{metadata}</code> is in your page template.</li>
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

$lang['help_function_description'] = <<<EOT
	<h3>What does this do?</h3>
	<p>Prints the description (title attribute) of the page.</p>
	<h3>How do I use it?</h3>
	<p>Just insert the tag into your template/page like: <code>{description}</code></p>
	<h3>What parameters does it take?</h3>
	<p><em>(optional)</em> assign (string) - Assign the results to a smarty variable with that name.</p>
EOT;

$lang['help_function_created_date'] = <<<EOT
        <h3>What does this do?</h3>
        <p>Prints the date and time the page was created.  If no format is given, it will default to a format similar to 'Jan 01, 2004'.</p>
        <h3>How do I use it?</h3>
        <p>Just insert the tag into your template/page like: <code>{created_date format="%A %d-%b-%y %T %Z"}</code></p>
        <h3>What parameters does it take?</h3>
        <ul>
                <li><em>(optional)</em>format - Date/Time format using parameters from php's strftime function.  See <a href="http://php.net/strftime" target="_blank">here</a> for a parameter list and information.</li>
                <li><em>(optional)</em>assign - Assign the results to the named smarty variable.</li>
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
		<li><em>(optional)</em> wysiwyg (true/false) - If set to false, then a WYSIWYG will never be used while editing this block. If true, then it acts as normal.  Only works when block parameter is used.</li>
		<li><em>(optional)</em> oneline (true/false) - If set to true, then only one edit line will be shown while editing this block. If false, then it acts as normal.  Only works when block parameter is used.</li>
        <li><em>(optional)</em> size (positive integer) - Applicable only when the oneline option is used this optional parameter allows you to specify the size of the edit field.  The default value is 50.</li>
		<li><em>(optional)</em> maxlength (positive integer) - Applicable only when the oneline option is used this optional parameter allows you to specify the maximum length of input for the edit field.  The default value is 255.</li>
        <li><em>(optional)</em> default (string) - Allows you to specify default content for this content blocks (additional content blocks only).</li>
		<li><em>(optional)</em> label (string) - Allows specifying a label for display in the edit content page.</li>
        <li><em>(optional)</em> required (true/false) - Allows specifying that the content block must contain some text.</li>
        <li><em>(optional)</em> placeholder (string) - Allows specifying placeholder text.</li>
        <li><em>(optional)</em> priority (integer) - Allows specifying an integer priority for the block within the tab.</li>
        <li><em>(optional)</em> tab (string) - The desired tab to display this field on in the edit form..</li>
        <li><em>(optional)</em> cssname (string) - A hint to the WYSIWYG editor module to use the specified stylesheet name for extended styles.</li>
        <li><em>(optional)</em> noedit (true/false) - If set to true, then the content block will not be available for editing in the content editing form.  This is useful for outputting a content block to page content that was created via a third party module.</li>
        <li><em>(optional)</em> data-xxxx (string) - Allows passing data attributes to the generated textarea for use by syntax hilighter and WYSIWYG modules.
            <p>i.e.: <code>{content data-foo="bar"}</code></p>
        </li>
        <li><em>(optional)</em> adminonly (true/false) - If set to true, only members of the special &quot;Admin&quot; group (gid==1) will be able to edit this content block.</li>
		<li><em>(optional)</em> assign - Assigns the content to a smarty parameter, which you can then use in other areas of the page, or use to test whether content exists in it or not.
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
  <p>You can use the module FormBuilder instead.</p>
EOT;

$lang['help_function_cms_versionname'] = <<<EOT
	<h3>What does this do?</h3>
	<p>This tag is used to insert the current version name of CMS into your template or page.  It doesn't display any extra besides the version name.</p>
	<h3>How do I use it?</h3>
	<p>This is just a basic tag plugin.  You would insert it into your template or page like so: <code>{cms_versionname}</code>
	<h3>What parameters does it take?</h3>
	<p><em>(optional)</em> assign (string) - Assign the results to a smarty variable with that name.</p>
EOT;

$lang['help_function_cms_version'] = <<<EOT
	<h3>What does this do?</h3>
	<p>This tag is used to insert the current version number of CMS into your template or page.  It doesn't display any extra besides the version number.</p>
	<h3>How do I use it?</h3>
	<p>This is just a basic tag plugin.  You would insert it into your template or page like so: <code>{cms_version}</code></p>
	<h3>What parameters does it take?</h3>
	<p><em>(optional)</em> assign (string) - Assign the results to a smarty variable with that name.</p>
EOT;

$lang['help_function_cms_selflink'] = <<<EOT
		<h3>What does this do?</h3>
		<p>Creates a link to another CMSMS content page inside your template or content.</p>
		<h3>How do I use it?</h3>
		<p>Just insert the tag into your template/page like: <code>{cms_selflink page=&quot;1&quot;}</code> or  <code>{cms_selflink page=&quot;alias&quot;}</code></p>
		<h3>What parameters does it take?</h3>
		<ul>
		<li><em>(optional)</em> <tt>page</tt> - Page ID or alias to link to.</li>
		<li><em>(optional)</em> <tt>anchorlink</tt> - Specifies an anchor to add to the generated URL.</li>
		<li><em>(optional)</em> <tt>urlparam</tt> - Specify additional parameters to the URL.  <strong>Do not use this in conjunction with the <em>anchorlink</em> parameter</strong></li>
		<li><em>(optional)</em> <tt>tabindex =&quot;a value&quot;</tt> - Set a tabindex for the link.</li> <!-- Russ - 22-06-2005 -->
		<li><em>(optional)</em> <tt>dir start/next/prev/up (previous)</tt> - Links to the default start page or the next or previous page, or the parent page (up). If this is used <tt>page</tt> should not be set.</li>
		</ul>
		<strong>Note!</strong> Only one of the above may be used in the same cms_selflink statement!!
		<ul>
		<li><em>(optional)</em> <tt>text</tt> - Text to show for the link.  If not given, the Page Name is used instead.</li>
		<li><em>(optional)</em> <tt>menu 1/0</tt> - If 1 the Menu Text is used for the link text instead of the Page Name</li>
		<li><em>(optional)</em> <tt>target</tt> - Optional target for the a link to point to.  Useful for frame and JavaScript situations.</li>
		<li><em>(optional)</em> <tt>class</tt> - Class for the &lt;a&gt; link. Useful for styling the link.</li>
		<li><em>(optional)</em> <tt>id</tt> - Optional css_id for the &lt;a&gt; link.</li>
		<li><em>(optional)</em> <tt>more</tt> - place additional options inside the &lt;a&gt; link.</li>
		<li><em>(optional)</em> <tt>label</tt> - Label to use in with the link if applicable.</li>
		<li><em>(optional)</em> <tt>label_side left/right</tt> - Side of link to place the label (defaults to "left").</li>
		<li><em>(optional)</em> <tt>title</tt> - Text to use in the title attribute.  If none is given, then the title of the page will be used for the title.</li>
		<li><em>(optional)</em> <tt>rellink 1/0</tt> - Make a relational link for accessible navigation.  Only works if the dir parameter is set and should only go in the head section of a template.</li>
												       <li><em>(optional)</em> <tt>href</tt> - Specifies that only the result URL to the page alias specified will be returned.  This is essentially equal to {cms_selflink page=&quot;alias&quot; urlonly=1}. <strong>Example:</strong> &lt;a href=&quot;{cms_selflink href=&quot;alias&quot;}&quot;&gt;&lt;img src=&quot;&quot;&gt;&lt;/a&gt;.</li>
		<li><em>(optional)</em> <tt>urlonly</tt> - Specifies that only the resulting url should be output.  All parameters related to generating links are ignored.</li>
		<li><em>(optional)</em> <tt>image</tt> - A url of an image to use in the link. <strong>Example:</strong> {cms_selflink dir=&quot;next&quot; image=&quot;next.png&quot; text=&quot;Next&quot;}</li>
		<li><em>(optional)</em> <tt>alt</tt> - Alternative text to be used with image (alt="" will be used if no alt parameter is given).</li>
		<li><em>(optional)</em> <tt>width</tt> - Width to be used with image (no width attribute will be used on output img tag if not provided.).</li>
		<li><em>(optional)</em> <tt>height</tt> - Height to be used with image (no height attribute will be used on output img tag if not provided.).</li>
		<li><em>(optional)</em> <tt>imageonly</tt> - If using an image, whether to suppress display of text links. If you want no text in the link at all, also set lang=0 to suppress the label. <strong>Example:</strong> {cms_selflink dir=&quot;next&quot; image=&quot;next.png&quot; text=&quot;Next&quot; imageonly=1}</li>
        <li><em>(optional)</em> <tt>assign</tt> - Assign the results to the named smarty variable.</li>
		</ul>
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

$lang['help_function_cms_module_hint'] = <<<EOT
<h3>What does this do?</h3>
<p>This function plugin can be used to provide hints for module behavior if various parameters cannot be specified on the URL.  I.e: In a situation when a site is configured to use pretty urls for SEO purposes it is often impossible to provide additional module parameters like a detailtemplate or sort order on a URL.  This plugin can be used in page templates, GCBs or in a page specific way to give hints as to how modules should behave.</p>
<p><strong>Note:</strong> Any parameters that are specified on the URL will override matching module hints.   i.e:  When using News and a detailtemplate parameter is specified on a News detail url, any detailtemplate hints will have no effect.</p>
<p><strong>Note:</strong> In order to ensure proper behavior, module hints must be created before the {content} tag is executed in the CMSMS page template.  Therefore they should (normally) be created very early in the page template process.  An ideal location for page specific hints is in the &quot;Smarty data or logic that is specific to this page:&quot; textarea on the editcontent form.</p>
<h3>Parameters:</h3>
<ul>
  <li>module - <strong>required string</strong> - The module name that you are adding a hint for.</i>
</ul>
<p>Any further parameters to this tag are stored as hints.</p>
<h3>Example:</h3>
<p>When using the News module, and pretty urls are configured.  You wish to display news articles for a specific category on one page, and would like to use a non standard detail template to display the individual articles on a different page.  I.e: perhaps on your &quot;Sports&quot; page you are calling News like: <code>{News category=sports detailpage=sports_detail}</code>.  However, using pretty urls it may be impossible to specify a detailtemplate on the links that will generate the detail views.  The solution is to use the {cms_module_hint} tag on the <u>sports_detail</u> page to provide some hints as to how News should behave on that page.</p>
<p>When editing the <u>sports_detail</u> page on the options tab, in the textarea entitled &quot;Smarty data or logic that is specific to this page:&quot; you could enter a tag such as: <code>{cms_module_hint module=News detailtemplate=sports}</code>.  Now when a user clicks on a link from the News summary display on your &quot;sports&quot; page he will be directed to the <u>sports_detail</u> page, and the News detail template entitled &quot;sports&quot; will be used to display the article.</p>
<h3>Usage:</h3>
<p><code>{cms_module_hint module=ModuleName paramname=value ...}</code></p>
<p><strong>Note:</strong> It is possible to specify multiple parameter hints to a single module in one call to this plugin.</p>
<p><strong>Note:</strong> It is possible to call this module multiple times to provide hints to different modules.</p>
EOT;

$lang['help_function_breadcrumbs'] = <<<EOT
<h3 style="font-weight:bold;color:#f00;">REMOVED - Use now &#123nav_breadcrumbs&#125 or &#123Navigator action='breadcrumbs'&#125</h3>
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
	<li><em>(optional)</em> <tt>assign</tt> - Assign the results to the named smarty variable.</li>
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
<p>Simply insert this tag into your page or template: <code>{redirect_page page='some-page-alias'}</code></p>
EOT;


$lang['help_function_cms_jquery'] = <<<EOT
<h3>What does this do?</h3>
 <p>This plugin allows you output the JavaScript libraries and plugins used from the admin.</p>
<h3>How do I use it?</h3>
<p>Simply insert this tag into your page or template: <code>{cms_jquery}</code></p>

<h3>Sample</h3>
<pre><code>{cms_jquery cdn='true' exclude='jquery-ui' append='uploads/NCleanBlue/js/ie6fix.js' include_css=0}</code></pre>
<h4><em>Outputs</em></h4>
<pre><code>&lt;script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"&gt;&lt;/script&gt;
&lt;script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/jquery-ui.min.js"&gt;&lt;/script&gt;
&lt;script type="text/javascript" src="http://localhost/1.10.x/lib/jquery/js/jquery.json-2.3.js"&gt;&lt;/script&gt;
&lt;script type="text/javascript" src="uploads/NCleanBlue/js/ie6fix.js"&gt;&lt;/script&gt;
</code></pre>

<h3>>Known Scripts:</h3>
<ul>
	<li><tt>jQuery</tt></li>
	<li><tt>jQuery-UI</tt></li>
	<li><tt>nestedSortable</tt></li>
	<li><tt>json</tt></li>
	<li><tt>migrate</tt></li>
</ul>

<h3>What parameters does it take?</h3>
<ul>
	<li><em>(optional) </em><tt>exclude</tt> - use comma seperated value(CSV) list of scripts you would like to exclude. <code>'jquery-ui,migrate'</code></li>
	<li><em>(optional) </em><tt>append</tt> - use comma seperated value(CSV) list of script paths you would like to append. <code>'/uploads/jquery.ui.nestedSortable.js,http://code.jquery.com/jquery-1.7.1.min.js'</code></li>
	<li><em>(optional) </em><tt>cdn</tt> - cdn='true' will insert jQuery and jQueryUI Frameworks using Google's Content Delivery Netwok. Default is false.</li>
	<li><em>(optional) </em><tt>ssl</tt> - use to use the ssl_url as the base path.</li>
	<li><em>(optional) </em><tt>custom_root</tt> - use to set any base path wished.<code>custom_root='http://test.domain.com/'</code> <br/>NOTE: overwrites ssl option and works with the cdn option</li>
	<li><em>(optional) </em><tt>include_css <em>(boolean)</em></tt> - use to prevent css from being included with the output.  Default value is true.</li>
	<li><em>(optional)</em> <tt>assign</tt> - Assign the results to the named smarty variable.</li>
	</ul>
EOT;

$lang['help_function_cms_filepicker'] = <<<EOT
<h3>What does this do?</h3>
<p>This plugin will create an input field that is controlled by the <em>(current)</em> file picker module to allow selecting a file.  This is an admin only plugin useful for module templates, and other admin forms.</p>
<p>This plugin should be used in a module's admin template, and the output created by selecting a file should be handled in the normal way in the modules action php file.</p>
<p>Note: This plugin will detect (using internal mechanisms) the currently preferred filepicker module, which may be different than the CMSMS core file picker module, and that filepicker module may ignore some of these parameters.</p>
<h3>Usage:</h3>
<ul>
  <li>name - <strong>required</strong> string - The name for the input field.</li>
  <li>prefix - <em>(optional)</em> string - A prefix for the name of the input field.</li>
  <li>value - <em>(optional)</em> string - The current value for the input field..</li>
  <li>profile - <em>(optional)</em> string - The name of the profile to use.  The profile must exist within the selected file picker module, or a default profile may be used.</li>
  <li>top - <em>(optional)</em> string - A top directory, relative to the uploads directory.  This should override any top value already specified in the profile.</li>
  <li>type - <em>(optional)</em> string - An indication of the file type that can be selected.
      <p>Possible values are: image,audio,video,media,xml,document,archive,any</p>
  </li>
  <li>required - <em>(optional)</em> boolean - Indicates whether or not the input field is required.</li>
</ul>
<h3>Example:</h3>
<p>Create a filepicker field to allow selecting images in the images/apples directory.</p>
<pre><code>{cms_filepicker prefix=\$actionid name=article_image top='images/apples' type='image'}</code></pre>
EOT;

$lang['help_function_thumbnail_url'] = <<<EOT
<h3>What does this do?</h3>
<p>This tag generates a URL to a thumbnail image when an actual image file relative to the uploads directory is specified .</p>
<p>This tag will return an empty string if the file specified does not exist, the thumbnail does not exist,  or there are permissions propblems.</p>
<h3>Usage:</h3>
<ul>
  <li>file - <strong>required</strong> - The filename and path relative to the uploads directory.</li>
  <li>dir - <em>(optional)</em> - An optional directory prefix to prepend to the filename.</li>
  <li>assign - <em>(optional)</em> - Optionally assign the output to the named smarty variable.</li>
</ul>
<h3>Example:</h3>
<pre><code>&lt;img src="{thumbnail_url file='images/something.jpg'}" alt="something.jpg"/&gt;</code></pre>
<h3>Tip:</h3>
<p>It is a trivial process to create a generic template or smarty function that will use the <code>{file_url}</code> and <code>{thumbnail_url}</code> plugins to generate a thumbnail and link to a larger image.</p>
EOT;

$lang['help_function_file_url'] = <<<EOT
<h3>What does this do?</h3>
<p>This tag generates a URL to a file within the uploads path of the CMSMS installation.</p>
<p>This tag will return an empty string if the file specified does not exist or there are permissions propblems.</p>
<h3>Usage:</h3>
<ul>
  <li>file - <strong>required</strong> - The filename and path relative to the uploads directory.</li>
  <li>dir - <em>(optional)</em> - An optional directory prefix to prepend to the filename.</li>
  <li>assign - <em>(optional)</em> - Optionally assign the output to the named smarty variable.</li>
</ul>
<h3>Example:</h3>
<pre><code>&lt;a href="{file_url file='images/something.jpg'}"&gt;view file&lt;/a&gt;</code></pre>
<h3>Tip:</h3>
<p>It is a trivial process to create a generic template or smarty function that will use the <code>{file_url}</code> and <code>{thumbnail_url}</code> plugins to generate a thumbnail and link to a larger image.</p>
EOT;

$lang['help_function_form_end'] = <<<EOT
<h3>What does this do?</h3>
<p>This tag creates an end form tag.</p>
<h3>What parameters does it take?</p>
<ul>
  <li>assign - <em>(optional)</em> - Assign the results of this tag to the named smarty variable.</li>
</ul>
<h3>Usage:</h3>
<pre><code>{form_end}</code></pre>
<h3>See Also:</h3>
<p>See the {form_start} tag which is the complement to this tag.</p>
EOT;

$lang['help_function_form_start'] = <<<EOT
<h3>What does this do?</h3>
  <p>Thie tag creates a &lt;form&gt; tag for a module action.  It is useful in module templates and is part of the separation of design from logic principle that is at the heart of CMSMS.</p>
  <p>This tag accepts numerous parameters that can accept the &lt;form&gt; tag, and effect its styling.</p>
<h3>What parameters does it take?</h3>
<ul>
  <li>module - <em>(optional string)</em>
    <p>The module that is the destination for the form data.  If this parameter is not specified then an attempt is made to determine the current module.<p>
  </li>
  <li>action - <em>(optional string)</em>
    <p>The module action that is the destination for the form data.  If not specified, &quot;default&quot; is assumed for a frontend request, and &quot;defaultadmin&quot; for an admin side request.</p>
  </li>
  <li>mid = <em>(optional string)</em>
    <p>The module actionid that is the destination for the form data.  If not specified, a value is automatically calculated.</p>
  </li>
  <li>returnid = <em>(optional integer)</em>
    <p>The content page id that the form should be submitted to.  If not specified, the current page id is used for frontend requests.   For admin requests this attribute is not required.</p>
  </li>
  <li>inline = <em>(optional integer)</em>
    <p>A boolean value that indicates that the form should be submitted inline (form processing output replaces the original tag) or not (form processing output replaces the {content} tag).  This parameter is only applicable to frontend requests, and defaults to false for frontend requests.</p>
  </li>
  <li>method = <em>(optional string)</em>
    <p>Possible values for this field are GET and POST.  The default value is POST.</p>
  </li>
  <li>url = <em>(optional string)</em>
    <p>Allows specifying the action attribute for the form tag.  This is useful for building forms that are not destined to a module action.  A complete URL is required.</p>
  </li>
  <li>enctype = <em>(optional string)</em>
    <p>Allows specifying the encoding type for the form tag.  The default value for this field is multipart/form-data.</p>
  </li>
  <li>id = <em>(optional string)</em>
    <p>Allows specifying the id attribute for the form tag.</p>
  </li>
  <li>class = <em>(optional string)</em>
    <p>Allows specifying the class attribute for the form tag.</p>
  </li>
  <li>extraparms = <em>(optional associative array)</em>
    <p>Allows specifying an associative (key/value) array with extra parameters for the form tag.
  </li>
  <li>assign = <em>(optional string)</em>
    <p>Assign the output of the tag to the named smarty variable.</p>
  </li>
</ul>
<p>You may also provide extra attributes to the &lt;form&gt; tag by prepending the attribute with the &quot;form-&quot;prefix.  i.e:</p>
<pre><code>{form_start form-data-foo="bar" form-novalidate=""}</code></pre>
<p><strong>Note:</strong> Smarty shorthand attributes are not permitted.  Each attribute provided must have a value, even if it is empty.</p>
<h3>Usage:</h3>
<p>In a module template the following code will generate a form tag to the current action.</p>
<pre><code>{form_start}</code></pre>
<p>This code, in a module template will generate a form tag to the named action.</p>
<pre><code>{form_start action=myaction}</code></pre>
<p>This code will generate a form tag to the named action in the named module.</p>
<pre><code>{form_start module=News action=default}</code></pre>
<p>This code will generate a form tag to the same action, but set an id, and class.</p>
<pre><code>{form_start id="myform" class="form-inline"}</code></pre>
<p>This code will generate a form tag to the named url, and set an id, and class.</p>
<pre><code>{form_start url="/products" class="form-inline"}</code></pre>
<h3>See Also:</h3>
<p>See the {form_end} tag that complements this tag.</p>
<h3>Example 1:</h3>
<p>The following is a sample form for use in a module.  This hypothetical form will submit to the action that generated the form, and allow the user to specify an integer pagelimit.</p>
<pre><code>{form_start}
&lt;select name="{\$actionid}pagelimit"&gt;
&lt;option value="10"&gt;10&lt;/option&gt;
&lt;option value="25"&gt;25&lt;/option&gt;
&lt;option value="50"&gt;50&lt;/option&gt;
&lt;select&gt;
&lt;input type="submit" name="{\$actionid}submit" value="Submit"/&gt;
{form_end}</code></pre>
<h3>Example 2:</h3>
<p>The following is a sample form for use in the frontend of a website.  Entered into page content, this hypothetical form will gather a page limit, and submit it to the News module.</p>
<pre><code>{form_start method="GET" class="form-inline"}
&lt;select name="pagelimit"&gt;
&lt;option value="10"&gt;10&lt;/option&gt;
&lt;option value="25"&gt;25&lt;/option&gt;
&lt;option value="50"&gt;50&lt;/option&gt;
&lt;select&gt;
&lt;input type="submit" name="submit" value="Submit"/&gt;
{form_end}
{\$pagelimit=25}
{if isset(\$smarty.get.pagelimit)}{\$pagelimit=\$smarty.get.pagelimit}{/if}
{News pagelimit=\$pagelimit}</code></pre>
EOT;

$lang['function'] = 'Functions may perform a task, or query the database, and typically display output.  They can be called like {tagname [attribute=value...]}';
$lang['modifier'] = 'Modifiers take the output of a smarty variable and modify it.  They are called like: {$variable|modifier[:arg:...]}';
$lang['postfilter'] = 'Postfilters are called automatically by smarty after the compilation of every template.  They cannot be called manually.';
$lang['prefilter'] = 'Prefilters are called automatically by smarty before the compilation of every template.  They canot be called manually.';
$lang['tag_about'] = 'Display the history and author information for this plugin, if available';
$lang['tag_adminplugin'] = 'Indicates that the tag is available in the admin interface only, and is usually used in module templates';
$lang['tag_cachable'] = 'Indicates whether the output of the plugin can be cached (when smarty caching is enabled).  Admin plugins, and modifiers cannot be cached.';
$lang['tag_help'] = 'Display the help (if any exists) for this tag';
$lang['tag_name'] = 'This is the name of the tag';
$lang['tag_type'] = 'The tag type (function, modifier, or a pre or post filter)';
$lang['title_admin'] = 'This plugin is only available from the CMSMS admin console..';
$lang['title_notadmin'] = 'This plugin is usable in both the admin console and on the website frontend.';
$lang['title_cachable'] = 'This plugin is cachable';
$lang['title_notcachable'] = 'This plugin is not cachable';
$lang['viewabout'] = 'Display history and author information for this module';
$lang['viewhelp'] = 'Display help for this module';
?>
