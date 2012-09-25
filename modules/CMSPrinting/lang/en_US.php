<?php
$lang["friendlyname"]="CMSPrinting";
$lang["description"]="This module is an easily customizable way of providing printer friendly pages for CMSMS";
$lang["postinstall"]="The module was successfully installed";
$lang["confirmuninstall"]="Are you sure the module should be uninstalled?";
$lang["postuninstall"]="The module was successfully uninstalled";

$lang["linktemplate"]="Link template";
$lang["printtemplate"]="Print template";
$lang["templatesaved"]="The template was saved";
$lang["templatereset"]="The template was reset to its default value";
$lang["confirmresettemplate"]="Are you sure the template should be reset to its default value?";
$lang["reset"]="Reset to default";
$lang["save"]="Save";
$lang["upgraded"]="has been upgraded to version %s";
$lang["savetemplate"]="Save template";
$lang["savesettings"]="Save settings";

$lang["template"]="Template";
$lang["notemplatecontent"]="No new template content given...";
$lang["defaultlinktext"]="Print this page";
$lang["backbuttontext"]="Go back";

$lang["overridestylereset"]="The override stylesheet has been reset";
$lang["overridestylesaved"]="The override stylesheet has been saved";
$lang["overridestyle"]="Override print stylesheet";
$lang["confirmresetstyle"]="Are you sure the stylesheet should be reset?";
$lang["stylesheet"]="Stylesheet";


$lang["help_text"]="Override the default text for the Print link";
$lang["help_popup"]="Set to 'true' and page for printing will by opened in new window.";
$lang["help_script"]="Set to 'true' and in print page javascript will be used for automatically show the print-dialog";
$lang["help_showbutton"]="Set to 'true' to show a graphical button";
$lang["help_class"]="Class for the link, defaults to 'noprint'";
$lang["help_src_img"]="Show this image file instead of the default";
$lang["help_class_img"]="Class of &lt;img&gt; tag if showbutton is set";
$lang["help_more"]="Place additional options inside the &lt;a&gt; link";
$lang["help_onlyurl"]="Outputs just the url, not a complete link";
$lang["help_includetemplate"]="If set to 'true' this options makes the print/pdf process the whole template, not just the main content. This probably requires some work on print-specific styles with the mediatype 'print' enabled.";

$lang["help"]="
<b>What does this module do?</b>
<br />
This allow you to insert a link in pages/templates which directs the 
visitor to a version of the page better suited for printing.
<br />
Please note that unless the parameter <i>includetemplate=true</i> is used, only the main output of the page is outputted. 
<br /><br />
<b>How do I use this module?</b>
<br />
Basically you install the module, access it's administration interface and review/change the templates for the
link and for the printable page
<br/>
In you page content or template you then insert something like:
<pre>
{cms_module module='CMSPrinting' <i>params</i>}
</pre>
or simply
<pre>
{print <i>params</i>}
</pre>
using the print-plugin
<br />
";
?>