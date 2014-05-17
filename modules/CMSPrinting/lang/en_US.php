<?php
// B
$lang["backbuttontext"] = "Go back";

// C
$lang["confirmresetstyle"] = "Are you sure the stylesheet should be reset?";
$lang["confirmresettemplate"] = "Are you sure the template should be reset to its default value?";
$lang["confirmuninstall"] = "Are you sure the module should be uninstalled?";

// D
$lang["defaultlinktext"] = "Print this page";
$lang["description"] = "This module is an easily customizable way of providing printer friendly pages for CMSMS";

// F
$lang["friendlyname"] = "CMSPrinting";

// H
$lang["help"] = "
<b>What does this module do?</b><br />
This allow you to insert a link in pages/templates which directs the visitor to a version of the page better suited for printing.<br />
Please note that unless the parameter <i>includetemplate=true</i> is used, only the main output of the page is outputted.<br />
<br />
<b>How do I use this module?</b><br />
Basically you install the module, access it's administration interface and review/change the templates for the link and for the printable page<br />
In you page content or template you then insert something like:
<pre>
{cms_module module='CMSPrinting' <i>parameters</i>}
</pre>
or simply
<pre>
{print <i>parameters</i>}
</pre>
using the print-plugin<br />
";
$lang["help_class"] = "Class for the link, defaults to 'noprint'";
$lang["help_class_img"] = "Class of &lt;img&gt; tag if showbutton is set";
$lang["help_includetemplate"] = "If set to 'true' this options makes the print process the whole template, not just the main content. This probably requires some work on print-specific styles with the mediatype 'print' enabled.";
$lang["help_linktemplate"] = "Applicable to the default action this parameter allows specifying a non default template to use when displaying the print link.";
$lang["help_more"] = "Place additional options inside the &lt;a&gt; link";
$lang["help_onlyurl"] = "Outputs just the url, not a complete link";
$lang["help_printtemplate"] = "Applicable to the default action, this parameter allows specyfing a non default page template to use when formatting the final output for printing.";
$lang["help_popup"] = "Set to 'true' and page for printing will by opened in new window.";
$lang["help_script"] = "Set to 'true' and in print page JavaScript will be used for automatically show the print-dialog";
$lang["help_showbutton"] = "Set to 'true' to show a graphical button";
$lang["help_src_img"] = "Show this image file instead of the default";
$lang["help_text"] = "Override the default text for the Print link";

// L
$lang["linktemplate"] = "Link template";

// N
$lang["notemplatecontent"] = "No new template content given...";

// O
$lang["overridestyle"] = "Override print stylesheet";
$lang["overridestylereset"] = "The override stylesheet has been reset";
$lang["overridestylesaved"] = "The override stylesheet has been saved";

// P
$lang["postinstall"] = "The module was successfully installed";
$lang["postuninstall"] = "The module was successfully uninstalled";
$lang["printtemplate"] = "Print template";

// R
$lang["reset"] = "Reset to default";

// S
$lang["save"] = "Save";
$lang["savesettings"] = "Save settings";
$lang["savetemplate"] = "Save template";
$lang["stylesheet"] = "Stylesheet";

// T
$lang["template"] = "Template";
$lang["templatereset"] = "The template was reset to its default value";
$lang["templatesaved"] = "The template was saved";
$lang['type_CMSPrinting'] = 'CMSPrinting';
$lang['type_link'] = 'Link Template';
$lang['type_print'] = 'Print Template';

// U
$lang["upgraded"] = "has been upgraded to version %s";

?>
