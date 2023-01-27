{* original form template *}
<h3>{$mod->Lang('title_fesubmit_form')}</h3>

{if isset($error)}
  <div class="error>{$error}</div>
{elseif isset($message)}
  <div class="message>{$message}</div>
{/if}

{form_start category_id=$category_id}
	<div class="row">
		<p class="col4"><label for="news_title">*{$mod->Lang('title')}:</label></p>
		<p class="col8">
			<input id="news_title" type="text" name="{$actionid}title" value="{$title}" size="30" required/>
                </p>
	</div>
	<div class="row">
		<p class="col4"><label for="news_category">{$mod->Lang('category')}:</label></p>
		<p class="col8">
			<select id="news_category" name="{$actionid}input_category">
                        {html_options options=$categorylist selected=$category_id}
			</select>
                </p>
	</div>

{if !isset($hide_summary_field) or $hide_summary_field == 0}
	<div class="row">
		<p class="col4"><label for="news_summary">{$mod->Lang('summary')}:</label></p>
		<p class="col8">
			{$tmp=$actionid|cat:'summary'}
			{cms_textarea enablewysiwyg=true id=news_summary name=$tmp value=$summary required=true}
		</p>
	</div>
{/if}
	<div class="row">
		<p class="col4"><label for="news_content">*{$mod->Lang('content')}:</label></p>
		<p class="col8">
			{$tmp=$actionid|cat:'content'}
			{cms_textarea enablewysiwyg=true id=news_content name=$tmp value=$content required=true}
                </p>
	</div>
	<div class="row">
		<p class="col4"><label for="news_extra">{$mod->Lang('extra')}:</label></p>
		<p class="col8">
			<input id="news_extra" type="text" name="{$actionid}extra" value="{$extra}" size="30"/>
                </p>
	</div>
	<div class="row">
		<p class="col4">{$mod->Lang('startdate')}:</p>
		<p class="col8">
			{$tmp=$actionid|cat:'startdate_'}
			{html_select_date prefix=$tmp time=$startdate end_year="+15"}
			{html_select_time prefix=$tmp time=$startdate}
		</p>
	</div>
	<div class="row">
		<p class="col4">{$mod->Lang('enddate')}:</p>
		<p class="col8">
			{$tmp=$actionid|cat:'enddate_'}
			{html_select_date prefix=$tmp time=$enddate end_year="+15"}
			{html_select_time prefix=$tmp time=$enddate}
		</p>
	</div>
	{if isset($customfields)}
	   {foreach from=$customfields item='field'}
	      <div class="row">
		<p class="col4"><label for="news_fld_{$field->id}">{$field->name}:</label></p>
		<p class="col8">
		{if $field->type == 'file'}
			<input id="news_fld_{$field->id}" type="file" name="{$actionid}news_customfield_{$field->id}"/>
		{elseif $field->type == 'checkbox'}
			<input id="news_fld_{$field->id}" type="checkbox" name="{$actionid}news_customfield_{$field->id}" value="1"/>
		{elseif $field->type == 'textarea'}
			{$tmp1='news_fld_'|cat:$field->id}
			{capture assign='tmp2'}{$actionid}news_customfield_{$field->id}{/capture}
			{cms_textarea id=$tmp1 name=$tmp2 enablewysiwyg=true}
		{elseif $field->type == 'textbox'}
			<input id="news_fld_{$field->id}" type="text"" name="{$actionid}news_customfield_{$field->id}" maxlength="{$field->max_length}"/>
                {/if}
		</p>
	      </div>
	   {/foreach}
	{/if}
	<div class="row">
		<p class="col4">&nbsp;</p>
		<p class="col8">
			<input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>
			<a href="{cms_selflink href=$page_alias}">{$mod->Lang('prompt_redirecttocontent')}</a>
		</p>
	</div>
{form_end}
