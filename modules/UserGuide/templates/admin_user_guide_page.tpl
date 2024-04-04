{* admin_user_guide_page.tpl

   - includes adding '../' to the start of all img src attributes so they display in /admin

***************************************************************************************************}
{if !empty($error)}
    <div class="warning">{$error}</div>
{/if}

<div class="guide">

    <div class="edit-link-container">
    {if $separate_settings}
        {cms_action_url action=admin_settings pid=$page->id assign=settings_url}
        <a class="edit-link" href="{$settings_url}" title="{$mod->Lang('title_userguide_settings')}">{admin_icon icon='run.gif'}</a>
    {/if}
    {if $managePermission}
        {cms_action_url action=edit_page pid=$page->id assign=edit_url}
        <a class="edit-link" href="{$edit_url}" title="{$mod->Lang('edit')}">{admin_icon icon='edit.gif'}</a>
    {/if}
    {if $page->admin}
        <span class="admin-only">* = {$mod->Lang('admin_only_visible')}</span>
    {/if}
    </div>
{if $page->content!='' && $page->embed_first==0}
    <div class="guide-content-top">
        {$page->content|replace:'src="':'src="../'}
    </div>
{/if}


{if $page->embed_code!=''}
    {if $page->embed_type=='vimeo'}
        <div class="video-container ratio ratio-{$aspect}">
            <iframe src="{$mod::EMBED_PREFIX_VIMEO}{$page->embed_code}&title=0&byline=0&portrait=0" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
        </div>

    {elseif $page->embed_type=='youtube'}
        <div class="video-container ratio ratio-{$aspect}">
            <iframe class="embed-responsive-item" src="{$mod::EMBED_PREFIX_YOUTUBE}{$page->embed_code}&rel=0&modestbranding=1{*$start}{$autoplay*}"></iframe>
        </div>

    {elseif $page->embed_type=='local'}
        {$result=$page->parse_local_embed()}
        {if $result->errors}
            <div class="warning">
            {foreach $result->errors as $error}{$error}<br>{/foreach}
            </div>
        {/if}

        <div class="video-container ratio ratio-{$aspect}">
            <video width="640" height="360" controls>
            {foreach $result->sources as $type => $source}
                <source src="{uploads_url}/{$source}" type="video/{$type}">
            {/foreach}
                Your browser does not support the video tag.
            </video>
        </div>



    {elseif $page->embed_type=='code'}
        <div class="embed-container ratio ratio-16x9 -{$aspect}">
            {$page->embed_code}
        </div>
    {/if}
{/if}

{if $page->content!='' && $page->embed_first==1}
    <div class="guide-content-bottom">
        {$page->content|replace:'src="':'src="../'}
    </div>
{/if}

</div>


