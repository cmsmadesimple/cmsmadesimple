<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<meta name="Generator" content="CMS Made Simple - Copyright (C) 2004-2008 Ted Kulp. All rights reserved." />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="noindex, nofollow" />
	{cms_config var='root_url' assign='root'}
	<title>{sitename} - {$admin_theme->title}</title>
   
	<!--<link rel="stylesheet" href="{$root}/admin/themes/NCleanGrey2/css/kevin_layout.css" type="text/css" />-->
	<link rel="stylesheet" href="{$root}/admin/themes/NCleanGrey2/css/kevin_tabs.css" type="text/css" />
    
    
	 <link rel="stylesheet" href="{$root}/admin/themes/NCleanGrey2/css/style.css" type="text/css" />
        <!--[if IE]>
		  <script type="text/javascript" src="{$root}/admin/themes/NCleanGrey2/includes/ie7-standard-p.js"></script>
	<![endif]-->	
    <!--[if IE]>
		
	<![endif]-->
    <script type="text/javascript"  src="{$root}/admin/themes/NCleanGrey2/includes/standard.js"></script>


    <script type="text/javascript" src="{$root}/lib/jquery/jquery.js"></script>
	 <script type="text/javascript"  src="{$root}/lib/jquery/jquery.color.js"></script>
	 <script type="text/javascript"  src="{$root}/lib/jquery/jquery.jcontext.1.0.js"></script>
	 <script type="text/javascript"  src="{$root}/lib/jquery/ui/ui.tabs.js"></script>
	 <script type="text/javascript"  src="{$root}/lib/jquery/ui/ui.accordion.js"></script>
	 <script type="text/javascript"  src="{$root}/lib/jquery/ui/ui.mouse.js"></script>
	 <script type="text/javascript"  src="{$root}/lib/jquery/ui/ui.draggable.js"></script>
	 <script type="text/javascript"  src="{$root}/lib/jquery/ui/ui.droppable.js"></script>
	 <script type="text/javascript"  src="{$root}/lib/jquery/ui/ui.sortable.js"></script>	
	
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
		
{if $theme_object->has_errors()}
				<p class="errors">
					{foreach from=$theme_object->errors item='one_error'}
						{$one_error}<br />
					{/foreach}
				</p>
			{/if}
			{if $theme_object->has_messages()}
				<p class="messages">
					{foreach from=$theme_object->messages item='one_message'}
						{$one_message}<br />
					{/foreach}
				</p>
			{/if}
			{$admin_content}
			<div class="clearb"></div>
		


	<div id="HelpContent"></div>	

</div><!-- end MainContent -->

<div id="footer"><a rel="external" href="http://www.cmsmadesimple.org"><b>CMS Made Simple</b></a> {cms_version} "{cms_versionname}"<br /><b>CMS Made Simple</b> is free software released under the General Public Licence.</div>
</div><!--end clean-container-->
        
        
</body>
</html>
