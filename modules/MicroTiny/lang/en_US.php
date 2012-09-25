<?php
$lang['admindescription'] = 'A stripped down, but still powerfull implementation of the TinyMCE WYSIWYG editor.';
$lang['force_blackonwhite'] = 'Force black text on white background';
$lang['help_force_blackonwhite'] = 'Force the MicroTiny editor to have black text on a white background.  This may assist in displaying text in the wysiwyg editor in environments with dark backgrounds and light foregrounds.';
$lang['strip_background'] = 'Strip Background CSS Effects';
$lang['help_strip_background'] = 'Strip background effects from effected stylesheets.  This may assist in displaying text in the wysiwyg editor in environments with dark backgrounds and light foregrounds';
$lang['show_statusbar'] = 'Show Statusbar';
$lang['help_show_statusbar'] = 'Enable a status bar on the bottom of the wysiwyg area.  This is applicable only in an admin view.';
$lang['allow_resize'] = 'Allow Resize';
$lang['help_allow_resize'] = 'Allow resizing of the wysiwyg area.  Requires that the statusbar be enabled';
$lang['view_html'] = 'View HTML';
$lang["friendlyname"]="MicroTiny WYSIWYG editor";
$lang["help"]=<<<EOT
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
<p>From time to time it may be necessary to provide a wysiwyg text area with limited functionality to frontend editors.   To do this, you will need to follow two steps <em>(subject to change in future versions of CMSMS).</em>:
<ul>
  <li>Set MicroTiny as the Frontend Wysiwyg in the &quot;Site Admin &gt;&gt; Gobal Settings&quot; page on the &quot;General Settings&quot; tab.</li>
  <li>Add the tag {MicroTiny action=enablewysiwg} call to the pages where the wysiwhg editor will be used.  This can either be done in the head section of the page template, in the global metadata, or in the page specific metadata sections.  This tag takes no additional parameters.</li>
</ul>
</p>
EOT;

$lang["example"]="MicroTiny example";
$lang["settings"]="Settings";

$lang["youareintext"]="You are in";
$lang["dimensions"]="WxH";
$lang["size"]="Size";
$lang["filepickertitle"]="File picker";
$lang["cmsmslinker"]="CMSMS Linker";
$lang["tmpnotwritable"]="The configuration could not be written to the tmp-dir! Please fix that!";
$lang["css_styles_text"]="CSS Styles";
$lang["css_styles_help"]="CSS-stylenames specified here are added to a dropdownbox in the editor. Leaving the input field empty will keep the dropdown-box hidden (default behavior).";

$lang["css_styles_help2"]="The styles can either be just the class name, or a classname with a new name to show.
Must be sepereated by either commas or newlines.
<br/>Example: mystyle1, My style name=mystyle2
<br/>Result: a dropdown containing 2 entries, 'mystyle1' and 'My style name' resulting in the insertion of mystyle1, and mystyle2 respectively.
<br/>Note: No checking for the actual existence of the stylenames is done. They are used blindly.";

$lang["usestaticconfig_text"]="Use static config";
$lang["usestaticconfig_help"]="This generates a static configuration file instead of the dynamic one. Works better on some servers (for instance when running PHP as CGI)";

$lang["allowimages_text"]="Allow images";
$lang["allowimages_help"]="This enables an image button on the toolbar, allowing inserting an image into the content";

$lang["settingssaved"]="Settings saved";
$lang["savesettings"]="Save settings";
?>
