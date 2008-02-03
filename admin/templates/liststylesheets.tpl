<div class="pagecontainer">
	<div class="pageoverflow">
		{$header_name}
	</div><!-- pageoverflow -->

	<div id="grouplist">
		<div class="pageoverflow">
			<p class="pageoptions">
				{if $modify_layout eq true}
				<a href="addstylesheet.php" class="pageoptions">{adminicon icon='newobject.gif' alt_lang='addstylesheet'}</a>
				<a href="addstylesheet.php" class="pageoptions">{tr}addstylesheet{/tr}</a>
				{/if}
			</p>
		</div><!-- pageoverflow -->

		<table cellspacing="0" class="pagetable">
			<thead>
				<tr>
					<th class="pagew60">{tr}name{/tr}</th>
					<th class="pagepos">{tr}active{/tr}</th>
					{if $modify_layout eq true}
					<th class="pageicon">&nbsp;</th>
					<th class="pageicon">&nbsp;</th>
					<th class="pageicon">&nbsp;</th>
					<th class="pageicon">&nbsp;</th>
					{/if}
				</tr>
			</thead>
			<tbody>
				{if count($stylesheets) gt 0}
				{foreach from=$stylesheets item='current'}
				{cycle values='row1,row2' assign='currow'}
				<tr class="{$currow}" onouseover="this.className='{$currow}hover';" onmouseout="this.className='{$currow}';">
					<td>
						{if $modify_layout eq true}
						<a href="editstylesheet.php?css_id={$current->id}">
							{$current->name}
						</a>
						{else}
							{$current->name}
						{/if}
					</td>

					<td class="pagepos">
						{if $modify_layout eq true}
							{if $current->active eq 1}
								<a href="liststylesheets.php?stylesheet_id={$current->id}&amp;makeactive=0">{adminicon icon='true.gif' alt_lang='true'}</a>
							{else}
								<a href="liststylesheets.php?stylesheet_id={$current->id}&amp;makeactive=1">{adminicon icon='false.gif' alt_lang='false'}</a>
							{/if}
						{else}
							{if $current->active eq 1}
							{adminicon icon='true.gif' alt_lang='true'}
							{else}
							{adminicon icon='false.gif' alt_lang='false'}
							{/if}
						{/if}
					</td>

					{if $modify_layout eq true}
					<td class="icons_wide">
						<a href="templatecss.php?id={$current->id}&amp;type=template">{adminicon icon='css.gif' alt_lang='attachtotemplate'}</a>
					</td>
					<td class="pagepos icons_wide">
						<a href="copystylesheet.php?stylesheet_id={$current->id}">{adminicon icon='copy.gif' alt_lang='copy'}</a>
					</td>
					<td class="icons_wide">
						<a href="editstylesheet.php?css_id={$current->id}">{adminicon icon='edit.gif' alt_lang='edit'}</a>
					</td>
					<td class="icons_wide">
						<a href="deletecss.php?css_id=={$current->id}" onclick="return confirm('{tr}deleteconfirm{/tr} - {$current->name} - ?');">{adminicon icon='delete.gif' alt_lang='delete'}</a>
					</td>
					{/if}  
				</tr>
				{/foreach}
				{/if}
			</tbody>
		</table>

		<div class="pageoverflow">
			<p class="pageoptions">
				{if $modify_groups eq true}
				<a href="addstylesheet.php" class="pageoptions">{adminicon icon='newobject.gif' alt_lang='addstylesheet'}</a>
				<a href="addstylesheet.php" class="pageoptions">{tr}addstylesheet{/tr}</a>
				{/if}
			</p>
		</div><!-- pageoverflow -->

	</div><!-- grouplist -->
</div><!-- pagecontainer -->

<p class="pageback"><a class="pageback" href="{$back_url}">&#171; {lang string='back'}</a></p>
