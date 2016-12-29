{form_start}
<input type="hidden" name="{$actionid}id" value="{$profile->id|default:-1}"/>
<input type="hidden" name="{$actionid}_id" value="{$profile->id|default:-1}"/>
<div class="pageoverflow">
  <p class="pageinput">
    <input type="submit" id="submit" name="{$actionid}submit" value="{lang('submit')}"/>
	<input type="submit" id="cancel" name="{$actionid}cancel" value="{lang('cancel')}"/>
    <input type="submit" id="apply" name="{$actionid}apply" value="{lang('apply')}"/>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('ProfileName')}:&nbsp;{cms_help key2='HelpPopup_ProfileName' title=$mod->Lang('HelpPopupTitle_ProfileName')}</p>
  <p class="pageinput"><input type="text" id="{$profile->name}" name="{$actionid}name" value="{$profile->name}"/></p>
</div>
{foreach $profile->params as $param}
  <div class="pageoverflow">
    <p class="pagetext">{$mod->Lang("Profile_{$param.name}")}:&nbsp;{cms_help key2="HelpPopup_{$param.name}" title=$mod->Lang("HelpPopupTitle_{$param.name}")}</p>
    <p class="pageinput">
    {if $param.type == 0} {* Text Input *}
      <input type="text" id="{$param.name}" name="{$actionid}{$param.name}" value="{$param.value}"/>
    {/if} 
    {if $param.type == 1} {* Text Area *}
      <input type="text" id="{$param.name}" name="{$actionid}{$param.name}" value="{$param.value}"/>
    {/if} 
    {if $param.type == 2} {* Dropdown *}
      {html_options name="{$actionid}{$param.name}" options=$param.options selected=$param.value}
    {/if} 
    {if $param.type == 3} {* Multiselect *}
      <select name="{$actionid}{$param.name}[]" size="{$param.options|count}" multiple>
        {html_options options=$param.options selected=explode(',', $param.value)}
      </select>      
    {/if} 
    {if $param.type == 4} {* Checkbox *}
      <input type="checkbox" name="{$actionid}{$param.name}" {if $param.value} checked="checked"{/if} />
    {/if} 
    </p>
  </div>
{/foreach}

<div class="pageoverflow">
  <p class="pageinput">
    <input type="submit" id="submit" name="{$actionid}submit" value="{lang('submit')}"/>
    <input type="submit" id="cancel" name="{$actionid}cancel" value="{lang('cancel')}"/>
	<input type="submit" id="apply" name="{$actionid}apply" value="{lang('apply')}"/>
  </p>
</div>
{form_end}