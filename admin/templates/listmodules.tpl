<div id="page_tabs">
	{* tab headers *}
	<div id="module_page_tabs">
      <ul>
		<li><a href="#modules"><span>{tr}Modules{/tr}</span></a></li>
		<li><a href="#install"><span>{tr}Install{/tr}</span></a></li>
      </ul>
	</div>

	{* modules tab *}
	<div id="modules" class="fragment">
		<div class="pageoverflow">
		<table class="pagetable">
			<thead>
				<tr>
					<th>{tr}name{/tr}</th>
					<th>{tr}version{/tr}</th>
					<th>{tr}status{/tr}</th>
					<th class="pagepos">{tr}active{/tr}</th>
					<th>{tr}action{/tr}</th>
					<th>{tr}help{/tr}</th>
					<th>{tr}about{/tr}</th>
					<th class="pageicon">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
			{foreach from=$modules item=module}
        	    {cycle values='row1,row2' assign='currow'}
				<tr class="{$currow}">
					<td>{$module.name}</td>
					<td>{$module.version}</td>
					{if $module.use_span}
						<td colspan="3">{$module.status}</td>
					{else}
						<td>{$module.status}</td>
						<td class="pagepos">{$module.active}</td>
						<td>{$module.action}</td>
					{/if}
					<td>{$module.helplink}</td>
					<td>{$module.aboutlink}</td>
					<td>{$module.exportlink}</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
		</div>
	</div>{* modules tab *}

	{* install tab *}
	<div id="install" class="fragment">
		<p class="pageoverflow">
		  {lang string='info_upload_cmsmod'}
		</p>
		<div class="pageoverflow">
		<form id="installform" method="post" action="listmodules.php" enctype="multipart/form-data">
			<div>
				{admin_input type='file' label='Upload:' name='upload' id='upload' size=50 maxlength=255}
			</div>
			<div class="submitrow">
				<input type='submit' value="{tr}Submit{/tr}" name='uploadmodule' class='positive'/>
			</div>
		</form>
		</div>
	</div>{* install tab *}

</div>(* page tabs *}
<script type='text/javascript'>
   <!--
     $('#module_page_tabs > ul').tabs();
   //-->
   </script>
