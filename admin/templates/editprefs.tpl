{$header_name}
	<form method="post" action="editprefs.php" name="prefsform">
	<div id="page_tabs">
		<ul>
			<li><a href="#content"><span>Content</span></a></li>
		</ul>
	    <div id="content">
			{admin_input type='select' label='wysiwygtouse' id='wysiwyg' name='wysiwyg' options=$wysiwyg_options selected=$wysiwyg}
			{admin_input type='select' label='syntaxhighlightertouse' id='syntaxhighlighter' name='syntaxhighlighter' options=$syntaxhighlighter_options selected=$syntaxhighlighter}			
			<div class="row">
				<label>{lang string='gcb_wysiwyg'}:</label>
				<input class="checkbox" type="checkbox" name="gcb_wysiwyg" {if isset($gcb_wysiwyg)}checked="checked"{/if} /><span class="tooltip_info">{lang string='gcb_wysiwyg_help'}</span>
			</div>
			{admin_input type='select' label='language' id='default_cms_lang' name='default_cms_lang' options=$default_cms_lang_options selected=$default_cms_lang}			
			<div class="row">
				<label>{lang string='date_format_string'}:</label>
					<input type="text" name="date_format_string" value="{$date_format_string}" size="20" maxlength="20" /><span class="tooltip_info">{lang string='date_format_string_help'}</span>
			</div>
			{admin_input type='select' label='admintheme' id='admintheme' name='admintheme' options=$admintheme_options selected=$admintheme}			

		<div class="row">
			<label>{lang string='admincallout'}:</label>
			<input class="checkbox" type="checkbox" name="bookmarks" {if $bookmarks}checked="checked"{/if} /><span class="tooltip_info">{lang string='showbookmarks'}</span>
		</div>
		<div class="row">
			<label>{lang string='hide_help_links'}:</label>
			<input class="checkbox" type="checkbox" name="hide_help_links" {if $hide_help_links}checked="checked"{/if} /><span class="tooltip_info">{lang string='hide_help_links_help'}</span>
		</div>
		<div class="row">
			<label>{lang string='adminindent'}:</label>
			<input class="checkbox" type="checkbox" name="indent" {if $indent}checked="checked"{/if} /><span class="tooltip_info">{lang string='indent'}</span>
		</div>
		<input type="hidden" name="edituserprefs" value="true" />
		<input type="hidden" name="submit_form" value="true" />		
		<input type="hidden" name="old_default_cms_lang" value="{$old_default_cms_lang}" />
	
			
		</div>
	</div>
	{include file='elements/buttons.tpl'}	
</form>

<script type="text/javascript">
<!--
	$('#page_tabs').tabs({$start_tab});
//-->
</script>