<h3>{$mod->Lang('prompt_bulk_changeowner')}:</h3>

{form_start multicontent=$multicontent}
<div class="pageoverflow">
	<ul>
		{foreach from=$displaydata item='rec'}
		<li>({$rec.id}) : {$rec.name} <em>({$rec.alias})</em></li>
		{/foreach}
	</ul>
</div>

<div class="pageoverflow">
	<p class="pagetext"><label for="owner_ctl">{$mod->Lang('prompt_owner')}:</label></p>
	<p class="pageinput">
		<select id="owner_ctl" name="{$actionid}owner">
			{html_options options=$userlist selected=$userid}
		</select></p>
</div>

<div class="pageoverflow">
	<p class="pagetext">{$mod->Lang('prompt_confirm_operation')}:</p>
	<p class="pageinput">
		<input type="checkbox" id="confirm1" value="1" name="{$actionid}confirm1">
		&nbsp; <label for="confirm1">{$mod->Lang('prompt_confirm1')}</label>
		<br/>
		<input type="checkbox" id="confirm2" value="1" name="{$actionid}confirm2">
		&nbsp; <label for="confirm2">{$mod->Lang('prompt_confirm2')}</label></p>
</div>

<div class="pageoverflow">
	<p class="pagetext"></p>
	<p class="pageinput">
		<input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>
		<input type="submit" name="{$actionid}cancel" value="{$mod->Lang('cancel')}"/>
	</p>
</div>
{form_end}