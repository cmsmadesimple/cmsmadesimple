 Advlink plugin for TinyMCE
-----------------------------

About:
  This is a more advanced link dialog mostly based on code contributed by Michael Keck.
  This one supports popup windows and targets.

Installation instructions:
  * Copy the advlink directory to the plugins directory of TinyMCE (/jscripts/tiny_mce/plugins).
  * Add plugin to TinyMCE plugin option list example: plugins : "advlink".
  * Add this "img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout]" to extended_valid_elements option.

Initialization example:
  tinyMCE.init({
    theme : "advanced",
    mode : "textareas",
    plugins : "advlink",
    extended_valid_elements : "img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout]"
  });
