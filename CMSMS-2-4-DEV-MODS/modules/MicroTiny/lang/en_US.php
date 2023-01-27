<?php
// A
$lang['admindescription'] = 'A stripped down, but still powerful implementation of the TinyMCE WYSIWYG editor';

// B
$lang['browse'] = 'Browse';

// C
$lang['cancel'] = 'Cancel';
$lang['class'] = 'Class';
$lang['cmsms_linker'] = 'Link to CMSMS Page';
$lang['css_styles_help'] = 'CSS-stylenames specified here are added to a dropdown box in the editor. Leaving the input field empty will keep the dropdown box hidden (default behavior).';
$lang['css_styles_help2'] = 'The styles can either be just the class name, or a classname with a new name to show.<br />
Must be separated by either commas or newlines.<br />
Example: mystyle1, My style name=mystyle2<br />
Result: a dropdown containing 2 entries, \'mystyle1\' and \'My style name\' resulting in the insertion of mystyle1, and mystyle2 respectively.<br />
Note: No checking for the actual existence of the stylenames is done. They are used blindly.';
$lang['css_styles_text'] = 'CSS Styles';

// D
$lang['description'] = 'Description';
$lang['dimensions'] = 'WxH';
$lang['dimension'] = 'Dimension';
$lang['dirinfo'] = 'Change working directory to';

// E
$lang['edit_image'] = 'Edit Image';
$lang['edit_profile'] = 'Edit Profile';
$lang['error_cantchangesysprofilename'] = 'You cannot change the name of a system profile';
$lang['error_missingparam'] = 'A required parameter was missing';
$lang['error_nopage'] = 'No page alias selected';
$lang['example'] = 'MicroTiny example';

// F
$lang['filepickertitle'] = 'CMSMS File picker';
$lang['friendlyname'] = 'MicroTiny WYSIWYG editor';
$lang['fileview'] = 'File View';
$lang['filename'] = 'File Name';
$lang['filterby'] = 'Filter by';

// H
$lang['height'] = 'Height';
$lang['help'] = <<<EOT
<h3>What Does This Do?</h3>
<p>MicroTiny is a small, restricted version of the <a href="http://www.tinymce.com" target="_blank">TinyMCE</a> editor. allowing content editors a near WYSIWYG appearance for editing content.  It works with content blocks in CMSMS content pages (when a WYSIWYG has been allowed), in module Admin forms where WYSIWYG editors are allowed, and allows restricted capabilities for editing HTML blocks on frontend pages.</p>
</p>In order for MicroTiny to be used as the WYSIWYG editor in the Admin console the MicroTiny WYSIWYG Editor needs to be selected in the users preferences.  Please select &quot;MicroTiny&quot; in the &quot;Select WYSIWYG to Use&quot; option under &quot;My Preferences &gt;&gt; User Preferences&quot; in the CMSMS Admin panel.  Additional options in various modules or in content page templates, and content pages themselves can control whether a text area or a WYSIWYG field is provided in various edit forms.</p>
<p>For Frontend editing capabilities MicroTiny must be selected as the &quot;Frontend WYSIWYG&quot; in the global settings page of the CMSMS Admin console.</p>
<h3>Features:</h3>
<ul>
  <li>Supports a subset of HTML5 block and inline elements.</li>
  <li>Separate profiles for Admin editors and frontend editors.</li>
  <li>A custom file picker for selecting previously uploaded media.</li>
  <li>Custom plugin for creating links to CMSMS content pages <em>(Admin only)</em>.</li>
  <li>Customizable (somewhat) profiles for Admin behavior and frontend behavior.</li>
  <li>Customizable appearance by specifying a stylesheet to use for the editor.</li>
</ul>
<h3>How do I use it</h3>
  <ul>
    <li>Install and configure the module</li>
    <li>Set MicroTiny as your WYSIWYG editor of choice in &quot;My Preferences&quot;</li>
  </ul>
<h3>About HTML, TinyMCE, and content editing:</h3>
  <ul>
    <li>WYSIWYG-like editor:
       <p>This editor provides the ability to edit content in an environment that is similar <em>(but not necessarily identical to)</em> to the intended output on the website frontend. Numerous factors can influence differences, including:</p>
       <ul>
         <li>Incomplete or incorrect stylesheets</li>
         <li>Use of advanced styling that the editor cannot understand</li>
         <li>Use of HTML elements that the WYSIWYG does not understand.</li>
       </ul>
    </li>

    <li>Subset of HTML elements:
      <p>As a simple content editor this editor does not support all of the HTML elements (particularly the new HTML5 block level elements.  Any elements that the editor does not understand or support will be stripped from the content upon save.  As a general rule of thumb <em>(not including &lt;div&gt;)</em> you can assume that the editor supports only the elements that are directly available via the various menu and toolbar options.<p>
    </li>

    <li>Edit blocks of content, not the entire page:
      <p>As CMS Made Simple is a heavily templated environment using the Smarty template element, it is intended that the WYSIWYG editor is used only for specific blocks of content or data elements (i.e: the main content area of a page, or the description for a News or Blog article).   This module <em>(and CMSMS)</em> do not support full page editing.</p>
    </li>

    <li>Intended for simple content editing not design:
      <p>The intent and purpose of this module is to provide a WYSIWYG-like environment where editors can insert and edit content within specific blocks with limited formatting capabilities that will not interfere with, or override the styling of the page template.  It is not intended and for will not be supported as a general HTML editor or layout manipulator.</p>
      <p>Website developers should understand the points above, assume that content editors can and WILL be editing within a WYSIWYG area and ensure that only simple content is there.  If advanced layout techniques are needed for a specific area, then developers should modify the appropriate templates so that the restricted functionality editor will work properly.</p>
    </li>

    <li>Separation of Logic, Functionality and Design from Content.
      <p>This editor is built with the assumption that content for a specific area of a page (or a blog article, news article, or product description, ...) is data.  The data is styled by the appropriate templates, and should not be mixed with design elements, or functionality of the website.</p>
      <p>As a simple example.  If you are insisting that editors use certain classes for images, layout their images in a certain manner, or insert block elements such as &lt;div&gt; or &lt;section&gt; into their content for proper styling then this is not the editor module for you.  Such styling concerns should be taken care of in stylesheets and templates, such that your editor can enter text without having to remember rules.</p>
      <p>This module is not designed to handle special cases where advanced HTML is required.  In such pages the WYSIWYG editor should be disabled, and editing access to the page restricted to those with the ability to understand and edit HTML code manually.</p>
      <p>As this module is intended to provide a restricted editor for specific blocks, for use by editors without HTML knowledge. Since the WYSIWYG editor does not understand the Smarty logic, you should NOT (as a general rule) mix Smarty logic or module calls within WYSIWYG enabled areas.  It is best to disable the WYSIWYG for these areas/pages and restrict edit access to those pages.</p>
    </li>
  </ul>
<h3>About Images and Media:</h3>
  <p>Each profile has the ability to enable, and disable the ability for the editor to graphically insert image or media elements into the edited content.  This is useful in highly structured environments where images and other media can be included in final output via other means.  Particularly on frontend editing forms, where the identify of the user cannot necessarily be trusted it is recommended that users not have the ability to insert images or other media.</p>
  <p><strong>Note:</strong> This module does not provide the ability to upload or otherwise manipulate files, images or media.  That functionality is handled elsewhere in CMSMS.</p>

<h3>About Frontend Editing:</h3>
  <p>This module provides a unique &quot;profile&quot; for configuring the WYSIWYG editor on frontend requests.  By default the frontend profile is highly limited.</p>
  <p>To enable frontend WYSIWYG editors, the <code>{cms_init_editor}</code> tag must be included in the head part of the template.  Additionally, this module must be set as the &quot;Frontend WYSIWYG&quot; in the global settings page of the CMSMS admin console.</p>

<h3>About Styles and Colors:</h3>
  <p>This module provides the <em>(optional)</em> ability to associate a stylesheet with the profile.  This provides the ability to style the edit portion WYSIWYG editor in a manner similar to the website style.  Providing a more WYSIWYG like experience for the content editor.</p>
  <p>Additionally, in conjunction with the <code>classname</code> parameter of the <code>{cms_textarea}</code> and <code>{content}</code> tags this module allows the content editor module to override the specified stylesheet differently for each content block.  This allows the ability to style each WYSIWYG area differently, if there are multiple WYSIWYG areas on the page.  This functionality is restricted to the Admin interface only.</p>
  <p>For example, in a page template adding the cssname parameter to the {content} tag allows specifying a CMSMS stylesheet to use to customize the appearance of that content block.  i.e: <code>{content block='second block' cssname='whiteonblack'}</code>
  <p>Additionally, a setting in the content editing section of the &quot;Global Settings&quot; page allows automatically supplying the css name parameter with the name of the content block.</p>

  <h4>Styles for the WYSIWYG editor</h4>
    <p>The stylesheet for the WYSIWYG editor area should style everything from the body element downwards. It is only necessary to style the elements available to, and used by the content editor.  Here is a simple example of a stylesheet for a white-on-black theme:</p>
<pre><code>
body {
 background: black;
 color: white;
}
p {
 margin-bottom: 0.5em;
}
h1 {
 color: yellow;
 margin-bottom: 1em;
}
h2 {
 color: cyan;
 margin-bottom: 0.75em;
}
</code></pre>

<h3>FAQ:</h3>
  <dl>
   <dt>Q: Where is the support for <em style="color: red;">&quot;some functionality&quot;</em> in the editor, and how do I activate it?</dt>
      <dd>A: You don't.  The version of TinyMCE distributed with MicroTiny is a trimmed down, custom package.  We have added our own custom plugins, but don't support the addition of custom plugins or the ability to customize the configuration in any way other than the edit profile form.  If you require additional functionality in a WYSIWYG editor you may have some success in a third party module.</dd>
    <br/>
    <dt>Q: Which HTML/HTML5 tags are supported by this module, and how do I change that?</dt>
      <dd>A: The list of supported elements in the default TinyMCE editor can be found on the TinyMCE website <em>(though we don't have a correct link at the moment)</em>.  There is no mechanism in the MicroTiny module to extend that.</dd>
    <br/>
    <dt>Q: I cannot get the MicroTiny editor to work in the Admin interface, what can I do</dt>
      <dd>A: There are a few steps you can follow to diagnose this issue:
        <ol>
          <li>Check the CMSMS Admin log, your PHP error log, and the JavaScript console for indications of a problem.</li>
          <li>Ensure that the example WYSIWYG area works in the MicroTiny Admin panel under &quot;Extensions >> MicroTiny WYSIWYG Editor&quot;.  If this does not work, recheck your PHP error log and JavaScript console.</li>
          <li>Ensure that MicroTiny is selected as the &quot;WYSIWYG to use&quot; in your user preferences.</li>
          <li>Check other content pages. If MicroTiny works on one or more of those then that indicates that a flag to disable WYSIWYG editors on all content blocks may be set on some content pages.</li>
          <li>Check the page template(s). The wysiwyg=false parameter may be specified on one or more content blocks in the page template(s) which will disable the WYSIWYG editor.</li>
        </ol>
      </dd>
    <dt>Q: How do I insert a &lt;br/&gt; instead of create new paragraphs?</dt>
      <dd>A: Press [shift]+Enter instead of just the Enter key.</dd>
    <br/>
    <dt>Q: Why is <em style="color: red;">&quot;some functionality&quot;</em> available in the menubar, and not the toolbar?</dt>
      <dd>A: For this most part this is done intentionally to allow web developers the ability to further restrict the functionality of certain editor profiles.  The menubar can be toggled off in different profiles thus denying the user the functionality only available in the menubar.</dd>
  </dl>
<h3>Caching:</h3>
  <p>In an effort to improve performance, MicroTiny will attempt to cache the generated JavaScript files unless something has changed. This functionality can be disabled by setting the special config entry <code>mt_disable_cache</code> to true. i.e: adding <code>\$config[&quot;mt_disable_cache&quot;] = true;</code> to the config.php file.</p>
<h3>See Also:</h3>
<ul>
  <li><code>{content}</code> tag in &quot;Extensions >> Tags&quot;</li>
  <li><code>{cms_textarea}</code> tag in &quot;Extensions >> Tags&quot;</li>
  <li><code>{cms_init_editor}</code> tag in &quot;Extensions >> Tags&quot;</li>
  <li>The <a href="http://www.tinymce.com" target="_blank">TinyMCE</a> editor itself.</li>
</ul>
EOT;

// I
$lang['image'] = 'Image';
$lang['info_linker_autocomplete'] = 'This is an auto complete field. Begin by typing a few characters of the desired page alias, menu text, or title. Any matching items will be displayed in a list.';

// L
$lang['loading_info'] = 'Loading...';

// M
$lang['mailto_image'] = 'Create a mail image';
$lang['mailto_text'] = 'Create a mail link';
$lang['mailto_title'] = 'Create a mail link';
$lang['msg_cancelled'] = 'Operation canceled';
$lang['mthelp_allowcssoverride'] = 'If enabled, then any code that initializes a MicroTiny WYSIWYG area will be able to specify the name of a stylesheet to use instead of the default stylesheet specified above.';
$lang['mthelp_dfltstylesheet'] = 'Associate a stylesheet with editors using this profile.  This allows the WYSIWYG editor to appear similar to the website appearance.';
$lang['mthelp_profileallowimages'] = 'Allow the editor to embed images and videos into the text area.  For very tightly controlled designs the content editors may only be able to select images, or videos for specific areas of a web page.';
$lang['mthelp_profileallowtables'] = 'Allow the editor to embed and manipulate tables for tabular data.  Note:  This should not be used for controlling page layout, but only for tabular data.';
$lang['mthelp_profilelabel'] = 'A description for this profile.  The description cannot be edited for system profiles.';
$lang['mthelp_profilename'] = 'The name for this profile.  The name of system profiles cannot be edited.';
$lang['mthelp_profilemenubar'] = 'Indicates if the menubar should be enabled in the viewable profiles.  The menubar typically has more options than the toolbar';
$lang['mthelp_profilestatusbar'] = 'This flag indicates if the statusbar at the bottom of the WYSIWYG area should be enabled.  The status bar displays some useful scope information for advanced editors, and other useful information';
$lang['mthelp_profileresize'] = 'This flag indicates if the WYSIWYG area can be resized.  In order for resize abilities to work the statusbar must be enabled';

// N
$lang['newwindow'] = 'New window';
$lang['none'] = 'None';

// O
$lang['ok'] = 'Ok';

// P
$lang['prompt_linker'] = 'Enter Page title';
$lang['prompt_linktext'] = 'Link Text';
$lang['prompt_profiles'] = 'Profiles';
$lang['prompt_selectedalias'] = 'Selected Page alias';
$lang['profiledesc___admin__'] = 'This profile is used by all users who are authorized to use this editor, and have chosen this editor as their WYSIWYG editor';
$lang['profiledesc___frontend__'] = 'This profile is used for all frontend requests where this WYSIWYG editor is allowed';
$lang['profile_admin'] = 'Admin Editors';
$lang['profile_allowcssoverride'] = 'Allow blocks to override the selected stylesheet';
$lang['profile_allowimages'] = 'Allow images';
$lang['profile_allowresize'] = 'Allow resize';
$lang['profile_allowtables'] = 'Allow tables';
$lang['profile_dfltstylesheet'] = 'Stylesheet for editor';
$lang['profile_frontend'] = 'Frontend Editors';
$lang['profile_label'] = 'Label';
$lang['profile_name'] = 'Profile name';
$lang['profile_menubar'] = 'Show menubar';
$lang['profile_showstatusbar'] = 'Show statusbar';
$lang['prompt_name'] = 'Name';
$lang['prompt_target'] = 'Target';
$lang['prompt_class'] = 'Class attribute';
$lang['prompt_email'] = 'Email address';
$lang['prompt_insertmailto'] = 'Insert/edit a mail link';
$lang['prompt_anchortext'] = 'Anchor text';
$lang['prompt_rel'] = 'Rel attribute';
$lang['prompt_texttodisplay'] = 'Text to display';

// S
$lang['savesettings'] = 'Save settings';
$lang['settings'] = 'Settings';
$lang['settingssaved'] = 'Settings saved';
$lang['size'] = 'Size';
$lang['submit'] = 'Submit';
$lang['switchgrid'] = 'Switch to grid view';
$lang['switchlist'] = 'Switch to list view';
$lang['switchimage'] = 'Show image files';
$lang['switchvideo'] = 'Show video files';
$lang['switchaudio'] = 'Show audio files';
$lang['switcharchive'] = 'Show archive files';
$lang['switchfiles'] = 'Show files';
$lang['switchreset'] = 'Show all';

// T
$lang['tooltip_selectedalias'] = 'This field is read only';
$lang['title_cmsms_linker'] = 'Create a link to a CMSMS content page';
$lang['title_cmsms_filebrowser'] = 'Select a file';
$lang['title_edit_profile'] = 'Edit profile';
$lang['tmpnotwritable'] = 'The configuration could not be written to the tmp directory! Please fix this...';
$lang['tab_general_title'] = 'General';
$lang['tab_advanced_title'] = 'Advanced';
$lang['type'] = 'Type';

// U
$lang['usestaticconfig_help'] = 'This generates a static configuration file instead of the dynamic one. Works better on some servers (for instance when running PHP as CGI)';
$lang['usestaticconfig_text'] = 'Use static config';

// W
$lang['width'] = 'Width';

// V
$lang['view_source'] = 'View Source';

// Y
$lang['youareintext'] = 'Current Directory';

?>