<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<meta name="Generator" content="CMS Made Simple - Copyright (C) 2004-2009 Ted Kulp. All rights reserved." />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="noindex, nofollow" />
	{cms_config var='root_url' assign='root'}
	<title>{sitename} - {$admin_theme->title}</title>
   
	<!--<link rel="stylesheet" href="{$root}/admin/themes/NCleanGrey2/css/kevin_layout.css" type="text/css" />-->
	<!--<link rel="stylesheet" href="{$root}/admin/themes/default/css/tabs.css" type="text/css" />-->
	<link rel="stylesheet" href="{$root}/admin/themes/default/css/style.css" type="text/css" />

	<script type="text/javascript" src="../lib/jquery/jquery.js"></script>
	<script type="text/javascript"  src="../lib/jquery/json2.js"></script>
	<script type="text/javascript"  src="../lib/jquery/jquery.ui.js"></script>
	<script type="text/javascript"  src="../lib/jquery/jquery.css.js"></script>
	<script type="text/javascript"  src="../lib/jquery/jquery.metadata.js"></script>
	<script type="text/javascript"  src="../lib/jquery/jquery.cookie.js"></script>
	<script type="text/javascript"  src="../lib/jquery/jquery.hotkeys.js"></script>
	<script type="text/javascript"  src="../lib/jquery/jquery.cmsms.js"></script>

	<!--
	<script type="text/javascript" src="../lib/jquery/tree/tree_component.js"></script>
	<link rel="stylesheet" type="text/css" href="../lib/jquery/tree/tree_component.css" />
	-->

	<script type="text/javascript"  src="../lib/jquery/jstree/jquery.tree.js"></script>
	<script type="text/javascript"  src="../lib/jquery/jstree/plugins/jquery.tree.cookie.js"></script>
	<script type="text/javascript"  src="../lib/jquery/jstree/plugins/jquery.tree.contextmenu.js"></script>
	<script type="text/javascript"  src="../lib/jquery/jstree/plugins/jquery.tree.metadata.js"></script>
	<script type="text/javascript"  src="../lib/jquery/jstree/plugins/jquery.tree.checkbox.js"></script>
	
	{literal}	
	 <script type="text/javascript">
    //<![CDATA[
		// form handling stuff
		$(document).ready(function() {
			// disable all buttons
			$('button').addClass("disabled").attr("disabled", true);
			// but enable cancel
			$('button[@name="cancel"]').removeClass("disabled").attr("disabled", false);
	
			// assign event for every input
			$('form :input').one("change", function() {
				// on change call enable form
				enableForm(this.form);
				// mark in title
				if(document.title[0] != "*") {
					document.title = "*"+document.title;
				}
			});

			// fill the default help text
			var help = $('#default_helptext').html();	
			$('#HelpContent').html(help);

			// assign an event for every form row
			$("div[id*='formrow']").bind("click", function() {
				var help = $(this).children("input[id*='help_']").val();
				$('#HelpContent').html(help);
			});
			
		});

		function enableForm(form) {
			var input = $('button', form);
			input.attr('disabled', false).removeClass('disabled');
		}

	//]]></script>
	{/literal}
	
	{$headtext}
	
	<base href="{$baseurl}" />

</head>
<body>
	
<div id="clean-container">

    {$admin_topmenu}

    <div id="MainContent">

        <div class="pageerrorcontainer">
            {$admin_errors}
        </div>

        <div class="pagemessagecontainer">
            {$admin_messages}
        </div>

        {$admin_content}

        <div class="clearb"></div>

        <div id="HelpContent"></div>

    </div><!-- end MainContent -->

    <div id="footer">
        <a rel="external" href="http://www.cmsmadesimple.org"><b>CMS Made Simple</b></a> {cms_version} "{cms_versionname}"<br /><b>CMS Made Simple</b> is free software released under the General Public Licence.
    </div>

</div><!--end clean-container-->
        
        
</body>
</html>
