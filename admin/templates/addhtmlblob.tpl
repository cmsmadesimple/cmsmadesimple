{validation_errors for=$gcb_object}
{$header_name}
<form method="post" name="gcbform" id="cssform" action="{$action}">
	<div id="page_tabs">
		<ul>
			<li><a href="#content"><span>Content</span></a></li>
			<li><a href="#permissions"><span>Permissions</span></a></li>
		</ul>
	    <div id="content">
			{admin_input type='input' label='name' id='gcb_name' name='gcb[name]' value=$gcb_object->name}
			{admin_input type='textarea' label='content' id='gcb_text' name='gcb[content]' value=$gcb_object->value}
		</div>
		<div id="permissions">
			<div class="row">
				<label>{lang string='additionaleditors'}</label>
				<select name="additional_editors[]" multiple="multiple" size="3">
					{$addt_users}
				</select>
			</div>			
		</div>
	</div>
	<div class="submitrow">
		<input type="hidden" name="addhtmlblob" value="true" />
		<input type="submit" name="submitbutton" value="{lang string='submit'}" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
		<input type="submit" name="cancel" value="{lang string='cancel'}" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
	</div>	
</form>

<script type="text/javascript">
<!--
	$('#page_tabs').tabs({$start_tab});
//-->
</script>