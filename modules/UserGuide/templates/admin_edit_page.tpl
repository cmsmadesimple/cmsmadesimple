

<h3>{if $isNewPage}{$mod->Lang('add_item')}{else}{$mod->Lang('edit_item')}{/if}</h3>

{form_start pid=$page->id class='user-guide-edit-page'}
<div class="pageoverflow">
    <p class="pageinput">
        <input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>
        <input type="submit" name="{$actionid}cancel" value="{$mod->Lang('cancel')}"/>
        <input type="submit" name="{$actionid}apply" value="{$mod->Lang('apply')}"/>
    </p>
</div>
<div class="pageoverflow">
    <p class="pagetext">{$mod->Lang('title_title')}:</p>
    <p class="pageinput">
        <input type="text" name="{$actionid}title" value="{$page->title}" size="50"/>
    </p>
</div>
<div class="pageoverflow">
    <p class="pagetext">{$mod->Lang('title_active')}:</p>
    <p class="pageinput">{$input_active}</p>
</div>
<div class="pageoverflow">
    <p class="pagetext">{$mod->Lang('title_admin')}:</p>
    <p class="pageinput">
        <input type="checkbox" name="{$actionid}admin" id="{$actionid}admin" value="1" {if $page->admin}checked{/if}>
        <label for="{$actionid}admin"> {$mod->Lang('prompt_admin')}</label>
    </p>
</div>

<div class="pageoverflow">
   <p class="pagetext">{$mod->Lang('title_content')}:</p>
   <p class="pageinput">{$input_content}</p>
</div>
<br>



<div class="pageoverflow">
    <p class="pagetext">{$mod->Lang('title_embed_type')}:</p>
    <p class="pageinput">
        <select class="cms_dropdown embed-type" name="{$actionid}embed_type"
        {foreach $embed_options as $option => $option_text}data-{$option}="{$mod->Lang("embed_type_prompt_{$option}")}" {/foreach}>
            {html_options options=$embed_options selected=$embed_type}
        </select>
    </p>
</div>

<div class="pageoverflow">
    <p class="pagetext">{$mod->Lang('title_embed_code')}:</p>

    <div id="embed_code_input_group" class="input-group collapse {if $embed_type!='code'}show{/if}">{strip}
        <span id="embed_code_prefix" class="input-group-text" data-vimeo="{$mod::EMBED_PREFIX_VIMEO}" data-youtube="{$mod::EMBED_PREFIX_YOUTUBE}" data-local="{uploads_url}/">
            {if $embed_type=='vimeo'}
                {$mod::EMBED_PREFIX_VIMEO}
            {elseif $embed_type=='youtube'}
                {$mod::EMBED_PREFIX_YOUTUBE}
            {elseif $embed_type=='local'}
                {uploads_url}/
            {/if}{/strip}</span>
        <input id="embed_code_input" type="text" name="{if $embed_type!='code'}{$actionid}embed_code{/if}" class="embed-code form-control" rows="5" size="100" value="{if $embed_type!='code'}{$page->embed_code}{/if}" data-name="{$actionid}embed_code">
    </div>
    <div>
        <textarea id="embed_code_textarea" name="{if $embed_type=='code'}{$actionid}embed_code{/if}" class="embed-code collapse {if $embed_type=='code'}show{/if}" rows="5" cols="100" data-name="{$actionid}embed_code">{if $embed_type=='code'}{$page->embed_code}{/if}</textarea>
    </div>
    <span class="embed-code-prompt">{$mod->Lang("embed_type_prompt_{$embed_type}")}</span><br>

    {* <p class="pageinput">
        <textarea name="{$actionid}embed_code" class="embed-code {if $embed_type!='code'}small{/if}" rows="5" cols="100">{$page->embed_code}</textarea>
    </p> *}
</div>
<br>

<div class="pageoverflow">
    <p class="pageinput">
        <input type="checkbox" name="{$actionid}embed_first" id="{$actionid}embed_first" value="1" {if $page->embed_first}checked{/if}>
        <label for="{$actionid}embed_first"> {$mod->Lang('title_embed_first')}</label>
    </p>
</div>


<p>&nbsp;</p>
{form_end}