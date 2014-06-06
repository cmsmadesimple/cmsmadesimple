<div class="pagecontainer">

{$theme->StartTabHeaders()}
	{$theme->SetTabHeader('database',lang('sysmaintab_database'),isset($active_database))}
	{$theme->SetTabHeader('content',lang('sysmaintab_content'),isset($active_content))}
	{if isset($changelog)}
		{$theme->SetTabHeader('changelog',lang('sysmaintab_changelog'),isset($active_changelog))}
	{/if}
{$theme->EndTabHeaders()}

{$theme->StartTabContent()}

	{$theme->StartTab('database')}
		<form action="{$formurl}" method="post">
			<fieldset>
				<legend>{'sysmain_database_status'|lang}:&nbsp;</legend>
				<p>{$tablecount} {'sysmain_tablesfound'|lang:$nonseqcount}</p>

				{if $errorcount==0}
					<p class='green'><strong>{'sysmain_nostr_errors'|lang}</strong></p>
				{else}
					<p class='red'><strong>{$errorcount} {if $errorcount>1}{'sysmain_str_errors'|lang}{else}{'sysmain_str_error'|lang}{/if}:  {$errortables}</strong></p>
				{/if}

				<div class="pageoverflow">
					<p class="pagetext">{'sysmain_optimizetables'|lang}:</p>
					<p class="pageinput">
						<input class="pagebutton" type="submit" name="optimizeall" value="{'sysmain_optimize'|lang}"/>
					</p>
				</div>
				<div class="pageoverflow">
					<p class="pagetext">{'sysmain_repairtables'|lang}:</p>
					<p class="pageinput">
						<input class="pagebutton" type="submit" name="repairall" value="{'sysmain_repair'|lang}"/>
					</p>
				</div>
			</fieldset>
		</form>
	{$theme->EndTab()}


	{$theme->StartTab('content')}
		<form action="{$formurl}" method="post">
			<fieldset>
				<legend>{'sysmain_cache_status'|lang}&nbsp;</legend>
				<div class="pageoverflow">
					<p class="pagetext">{'clearcache'|lang}:</p>
					<p class="pageinput">
						<input class="pagebutton" type="submit" name="clearcache" value="{'clear'|lang}"/>
					</p>
				</div>
			</fieldset>
		</form>

		<fieldset>
			<legend>{'sysmain_content_status'|lang}&nbsp;</legend>
			<form action="{$formurl}" method="post" onsubmit="return confirm('{'sysmain_confirmupdatehierarchy'|lang|escape:'javascript'}')" >
				{$pagecount} {'sysmain_pagesfound'|lang}

				<div class="pageoverflow">
					<p class="pagetext">{'sysmain_updatehierarchy'|lang}:</p>
					<p class="pageinput">
						<input class="pagebutton" type="submit" name="updatehierarchy" value="{'sysmain_update'|lang}"/>
					</p>
				</div>
			</form>

			<form action="{$formurl}" method="post" onsubmit="return confirm('{'sysmain_confirmupdateurls'|lang|escape:'javascript'}')" >
				<div class="pageoverflow">
					<p class="pagetext">{'sysmain_updateurls'|lang}:</p>
					<p class="pageinput">
						<input class="pagebutton" type="submit" name="updateurls" value="{'sysmain_update'|lang}"/>
					</p>
				</div>
			</form>

			{if $withoutaliascount!="0"}
				<form action="{$formurl}" method="post" onsubmit="return confirm('{'sysmain_confirmfixaliases'|lang|escape:'javascript'}')" >
					<div class="pageoverflow">
						<p class="pagetext">{$withoutaliascount} {'sysmain_pagesmissinalias'|lang}:</p>
						<p class="pageinput">
							{foreach from=$pagesmissingalias item='page'}
								{*{$page.count}.*} {$page.content_name}<br/>
							{/foreach}
							<br />
							<input class="pagebutton" type="submit" name="addaliases" value="{'sysmain_fixaliases'|lang}"/>
						</p>
					</div>
				</form>
			{/if}

			{if $invalidtypescount!="0"}
				<form action="{$formurl}" method="post" onsubmit="return confirm('{'sysmain_confirmfixtypes'|lang|escape:'javascript'}')" >
					<div class="pageoverflow">
						<p class="pagetext">{$invalidtypescount} {'sysmain_pagesinvalidtypes'|lang}:</p>
						<p class="pageinput">
							{foreach from=$pageswithinvalidtype item='page'}
								{$page.content_name} <em>({$page.content_alias}) - {$page.type}</em><br/>
							{/foreach}
							<br />
							<input class="pagebutton" type="submit" name="fixtypes" value="{'sysmain_fixtypes'|lang|escape:'javascript'}"/>
						</p>
					</div>
				</form>
			{/if}

			{if $invalidtypescount=="0" && $withoutaliascount==""}
				<p class='green'><strong>{'sysmain_nocontenterrors'|lang}</strong></p>
			{/if}

		</fieldset>
	{$theme->EndTab()}


	{if isset($changelog)}
		{$theme->StartTab('changelog')}
			<p class='file'>{$changelogfilename}</p>
			<div class="changelog">{$changelog}</div>
		{$theme->EndTab()}
	{/if}

{$theme->EndTabContent()}

</div>