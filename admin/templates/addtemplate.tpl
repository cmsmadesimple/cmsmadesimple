{validation_errors for=$template_object}
<div class="pagecontainer">
<div class="pageoverflow">
{$header_name}
</div><!-- pageoverflow -->
<form method="post" name="templateform" id="templateform" action="{$action}">
	<div id="page_tabs">
		<ul>
			<li><a href="#content"><span>Content</span></a></li>
			<li><a href="#advanced"><span>Advanced</span></a></li>
		</ul>
		<div id="content">    
		    {* Name *}
			{admin_input type='input' label='name' id='template_name' name='template[name]' value=$template_object->name}

		    {* Content *}
				<div class="row">
					<label>*{lang string='content'}:</label>
					  {$content_box}
				</div>

		    {* Preview *}
		    {if $showpreview eq true}
				<iframe name="previewframe" class="preview" id="previewframe" src="{$previewfname}"></iframe>
		    {/if}
		</div>
		<div id="advanced">
		    {* Active *}
			{admin_input type='checkbox' label='active' id='template_active' name='template[active]' selected=$template_object->active}	
   
		    {html_hidden name='is_postback' value='true'}
		    {html_hidden name='template[id]' value=$template_object->id}
		    {html_hidden name='template_id' value=$template_object->id}
		</div>
	</div><!-- End Tabs -->
	{include file='elements/buttons.tpl'}
</form>
</div>
<script type="text/javascript">
<!--
	$('#page_tabs').tabs({$start_tab});
//-->
</script>