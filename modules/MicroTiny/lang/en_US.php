<?php
// A
$lang['admindescription'] = 'A stripped down, but still powerfull implementation of the TinyMCE WYSIWYG editor';

// B
$lang['browse'] = 'Browse';

// C
$lang['cancel'] = 'Cancel';
$lang['class'] = 'Class';
$lang['cmsms_linker'] = 'Link to CMSMS Page';
$lang['css_styles_help'] = 'CSS-stylenames specified here are added to a dropdownbox in the editor. Leaving the input field empty will keep the dropdown-box hidden (default behavior).';
$lang['css_styles_help2'] = 'The styles can either be just the class name, or a classname with a new name to show.<br />
Must be sepereated by either commas or newlines.<br />
Example: mystyle1, My style name=mystyle2<br />
Result: a dropdown containing 2 entries, \'mystyle1\' and \'My style name\' resulting in the insertion of mystyle1, and mystyle2 respectively.<br />
Note: No checking for the actual existence of the stylenames is done. They are used blindly.';
$lang['css_styles_text'] = 'CSS Styles';

// D
$lang['description'] = 'Description';
$lang['dimensions'] = 'WxH';

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

// H
$lang['height'] = 'Height';
$lang['help'] = <<<EOT
<h3>What Does This Do?</h3>
<p>MicroTiny is a small, restricted version of the TinyMCE-editor, formerly the wysiwyg-default of CMS Made Simple. This provides nothing more than the basics of editing, but is still a powerful tool allowing easy changes to content pages.</p>
<p>This module provides very few options and is designed to allow limited functionality to content editors with no knowledge of HTML.  The intent is that they will have very few options to be able to mess with the layout of a page, or the look and feel of a site.</p>
<h3>How Do I Use It?</h3>
<p>The MicroTiny test area should appear automatically (for users with sufficient permission) under the &quot;Extensions &gt;&gt; MicroTiny WYSIWYG Editor&quot; option in the CMSMS admin console.</p>
</p>In order for MicroTiny to be used as the wysiwyg editor when editing pages, the MicroTiny Wysiwyg Editor needs to be selected in the users preferences.  Please select &quot;MicroTiny&quot; in the &quot;Select WYSIWYG to Use&quot; option under &quot;My Preferences &gt;&gt; User Preferences&quot; in the CMSMS Admin panel.  Additional options in various modules or in content page templates, and content pages themselves  can control wether a text area or a wysiwyg field is provided in various edit forms.</p>
<h3>About Styles and Colors</h3>
<p>MicroTiny will read the stylesheets attached to the appropriate template <em>(if no template can be easily determined the default template and its stylesheets are used)</em>. and strip out background images in order to allow visualizing your text in an environment as close as possible to what will appear on the web page.  If your theme uses a dark background, along with background images on your styles you may experience problems.   We suggest that you always include a color on your background specifications.  i.e:
<pre><code>body {
 color: #eee;
 background: <span style="color: blue;">#ddd</span> url(path/to/an/image.jpg);
} 
</pre></code>
<h3>What about Frontend Wysiwygs</h3>
<p>From time to time it may be necessary to provide a wysiwyg text area with limited functionality to frontend editors. To do this, you will need to follow two steps <em>(subject to change in future versions of CMSMS).</em>:
<ul>
  <li>Set MicroTiny as the Frontend Wysiwyg in the &quot;Site Admin &gt;&gt; Gobal Settings&quot; page on the &quot;General Settings&quot; tab.</li>
  <li>Add the tag {MicroTiny action=enablewysiwg} call to the pages where the wysiwhg editor will be used.  This can either be done in the head section of the page template, in the global metadata, or in the page specific metadata sections.  This tag takes no additional parameters.</li>
</ul>
</p>
EOT;

// I
$lang['image'] = 'Image';
$lang['info_linker_autocomplete'] = 'This is an autocomplete field.  Begin by typing a few characters of the desired page alias, menutext, or title.  Any matching items will be displayed in a list.';

// L
$lang['loading_info'] = 'Loading...';

// M
$lang['msg_cancelled'] = 'Operation cancelled';
$lang['mthelp_allowcssoverride'] = 'If enabled, then any code that initializes a MicroTiny WYSIWYG area will be able to specify the name of a stylesheet to use instead of the default stylesheet specified above.';
$lang['mthelp_dfltstylesheet'] = 'Associate a stylesheet with editors using this profile.  This allows the WYSIWYG editor to appear similar to the webiste appearance.';
$lang['mthelp_profileallowimages'] = 'Allow the editor to embed images and videos into the text area.  For very tightly controlled designs the content editors may only be able to select images, or vidoes for specific areas of a web page.';
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
$lang['prompt_selectedalias'] = 'Selected Page alias';
$lang['profiledesc___admin__'] = 'This profile is used by all users who are authorized to use this editor, and have chosen this editor as their WYSIWYG editr';
$lang['profiledesc___frontend__'] = 'This profile is used for all frontend requests where this WYSIWYG editor is allowed';
$lang['profile_admin'] = 'Admin Editors';
$lang['profile_allowcssoverride'] = 'Allow blocks to override the selected stylesheet';
$lang['profile_allowimages'] = 'Allow images';
$lang['profile_allowresize'] = 'Allow resize';
$lang['profile_dfltstylesheet'] = 'Stylesheet for editor';
$lang['profile_frontend'] = 'Frontend Editors';
$lang['profile_label'] = 'Label';
$lang['profile_name'] = 'Profile name';
$lang['profile_menubar'] = 'Show menubar';
$lang['profile_showstatusbar'] = 'Show statusbar';
$lang['prompt_name'] = 'Name';
$lang['prompt_target'] = 'Target';
$lang['prompt_class'] = 'Class attribute';
$lang['prompt_rel'] = 'Rel attribute';
$lang['prompt_texttodisplay'] = 'Text to display';

// S
$lang['savesettings'] = 'Save settings';
$lang['settings'] = 'Settings';
$lang['settingssaved'] = 'Settings saved';
$lang['size'] = 'Size';
$lang['submit'] = 'Submit';

// T
$lang['tooltip_selectedalias'] = 'This field is read only';
$lang['title_cmsms_linker'] = 'Create a link to a CMSMS content page';
$lang['title_edit_profile'] = 'Edit profile';
$lang['tmpnotwritable'] = 'The configuration could not be written to the tmp-dir! Please fix that!';
$lang['tab_general_title'] = 'General';
$lang['tab_advanced_title'] = 'Advanced';

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