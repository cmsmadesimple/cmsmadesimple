<div class="pagecontainer">
<div class="pageoverflow">
{$header_name} 
  </div><!-- pageoverflow -->
	
<form method="post" action="editprefs.php">
	<div id="page_tabs">
		<ul>
			<li><a href="#content"><span>Admin Panel</span></a></li>
		</ul>
	    <div id="content">
			{admin_input type='select' label='wysiwygtouse' id='wysiwyg' name='wysiwyg' options=$wysiwyg_options selected=$wysiwyg}
			{admin_input type='select' label='syntaxhighlightertouse' id='syntaxhighlighter' name='syntaxhighlighter' options=$syntaxhighlighter_options selected=$syntaxhighlighter}			
			{admin_input type='checkbox' label='gcb_wysiwyg' id='gcb_wysiwyg' name='gcb_wysiwyg' selected=$gcb_wysiwyg tooltip='gcb_wysiwyg_help'}						
			{admin_input type='select' label='language' id='default_cms_lang' name='default_cms_lang' options=$default_cms_lang_options selected=$default_cms_lang}			
			{admin_input type='input' label='date_format_string' id='date_format_string' name='date_format_string' value=$date_format_string tooltip='date_format_string_help'}						
			{admin_input type='select' label='admintheme' id='admintheme' name='admintheme' options=$admintheme_options selected=$admintheme}			
			{admin_input type='checkbox' label='admincallout' id='bookmarks' name='bookmarks' selected=$bookmarks tooltip='showbookmarks'}						
			{admin_input type='checkbox' label='hide_help_links' id='hide_help_links' name='hide_help_links' selected=$hide_help_links tooltip='hide_help_links_help'}									
			{admin_input type='checkbox' label='adminindent' id='indent' name='indent' selected=$indent tooltip='indent'}	
            
            	
                {admin_input type='checkbox' label='Enable user notifications in the admin section' id='enablenotifications' name='enablenotifications' selected=$enablenotifications}
           
        <div class="row">
        <label for="{tr}enablenotifications{/tr}"> {tr}enablenotifications{/tr} </label>
       {$txt}
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
</script></div>