{* admin_settings.tpl *}

{if $separate_settings}
    {tab_header name='pages' label="{$mod->Lang('tab_pages')}"}
    {tab_header name='options' label="{$mod->Lang('tab_options')}"}
{/if}



{tab_start name='pages'}
    <div class="guide">
        <div class="edit-link-container">
        {if $separate_settings}
            <a class="edit-link" href="{cms_action_url action=defaultadmin}" title="{$mod->Lang('view_user_guide')}">{admin_icon icon='view.gif'}</a>
        {else}
            <span class="admin-only">* = {$mod->Lang('admin_only_visible')}</span>
        {/if}
        </div>
        <div class="pageoptions">
            <a href="{cms_action_url action=edit_page}">{admin_icon icon='newobject.gif'} {$mod->Lang('add_item')}</a>
            <span id="loader" data-icon="{$loadingIcon}"> </span>
        </div>
    </div>

    <table class="pagetable">
        <thead>
            <tr>
                <th>{$mod->Lang('title_title')}</th>
                <th class="pageicon">{$mod->Lang('title_active')}</th>
                <th class="pageicon">{$mod->Lang('title_admin')}</th>
                <th class="pageicon">{* edit icon *}</th>
                <th class="pageicon">{* delete icon *}</th>
            </tr>
        </thead>
        <tbody class="sortable-list">
        {if empty($pages)}
            <tr class="row1">
                <td colspan="5">--- {$mod->Lang('none')} ---</td>
            </tr>

        {else}
            {foreach $pages as $page}
                {cms_action_url action=edit_page pid=$page->id assign='edit_url'}
                <tr class="{if $page@index is even}row1{else}row2{/if}" data-id="{$page->id}" data-reorder="{cms_action_url action=reorder_page pid=$page->id after=-1 forjs=1}">
                    <td><a href="{$edit_url}" title="{$mod->Lang('edit')}">{$page->title}</a></td>
                    <td class="pagepos"><a class="active_page" href="{cms_action_url action=toggle_active_page pid=$page->id}" >{if $page->active}{admin_icon icon='true.gif'}{else}{admin_icon icon='false.gif'}{/if}</a>
                    </td>
                    <td class="pagepos"><a class="active_admin" href="{cms_action_url action=toggle_admin_only pid=$page->id}" >{if $page->admin}{admin_icon icon='true.gif'}{else}{admin_icon icon='false.gif'}{/if}</a>
                    </td>
                    <td><a href="{$edit_url}" title="{$mod->Lang('edit')}">{admin_icon icon='edit.gif'}</a></td>
                    <td><a class="del_user_guide_page" href="{cms_action_url action=delete_page pid=$page->id}" title="{$mod->Lang('delete')}" data-title="{$page->title}" data-confirm="{$mod->Lang('confirm_delete')}">{admin_icon icon='delete.gif'}</a></td>
                </tr>
            {/foreach}
        {/if}
        </tbody>
    </table>



{tab_start name='options'}
    <div class="guide">
        <div class="edit-link-container">
        {if $separate_settings}
            <a class="edit-link" href="{cms_action_url action=defaultadmin}" title="{$mod->Lang('view_user_guide')}">{admin_icon icon='view.gif'}</a>
        {else}
            <span class="admin-only">* = {$mod->Lang('admin_only_visible')}</span>
        {/if}
        </div>
    </div>

{form_start action=admin_settings}
<fieldset>
    <legend>{$mod->Lang('tab_options')} </legend>

        <div class="pageoverflow">
            <p class="pagetext">{$mod->Lang('title_customModuleName')}:</p>
            <p class="pageinput">{$input_customModuleName}</p>
        </div>
        <div class="pageoverflow">
            <p class="pagetext">{$mod->Lang('title_adminSection')}:</p>
            <p class="pageinput">{$input_adminSection}</p>
        </div>
        <div class="pageoverflow">
            <p class="pagetext">
                <input type="checkbox" class="cms_checkbox" name="{$actionid}separate_settings" value="1" {if $separate_settings}checked="checked"{/if}> {$mod->Lang('title_separate_settings')}
            </p>
        </div>
        <div class="pageoverflow">
            <p class="pagetext">{$mod->Lang('title_customCSS')}:</p>
            <p class="pageinput">{$mod->Lang('text_customCSS')|nl2br}</p>
        </div>
        <div class="pageoverflow">
            <br>
            <p class="pagetext">{$input_useSmarty} {$mod->Lang('title_useSmarty')}:</p>
            <p class="pageinput">{$mod->Lang('help_useSmarty')|nl2br}</p>
        </div>
</fieldset>

<div class="pageoverflow">
    <p class="pageinput">
        <button type="submit" name="{$actionid}submit" value="save_settings" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button" aria-disabled="false">
            <span class="ui-button-icon-primary ui-icon ui-icon-circle-check"></span>
            <span class="ui-button-text">{$mod->Lang('save_options')}</span>
        </button>
    </p>
</div>
<br>


<fieldset>
   <legend>{$mod->Lang('title_import_export')} </legend>
        <div class="pageoverflow">
            <p class="pagetext">{$mod->Lang('title_import')}:</p>
            <p class="pageinput">
                {$input_import}&nbsp;&nbsp;  


                <button type="submit" name="{$actionid}submit" value="xml_import" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button" aria-disabled="false">
                    <span class="ui-button-icon-primary ui-icon ui-icon-arrowthickstop-1-w"></span>
                    <span class="ui-button-text">{$mod->Lang('xml_import')}</span>
                </button>
            </p>
        </div>
    <br>

    <div class="pageoverflow">
        <p class="pagetext">{$mod->Lang('title_exportImageFolder')}:</p>
        <div class="input-group">
            <span class="input-group-text" id="basic-addon3">{uploads_url}/</span>
            <input type="text" class="form-control" name="{$actionid}input_imageFolder" value="{$imageFolder}" size="50"/>
        </div>
        {$mod->Lang('text_exportImageFolder')}
    </div>

    <div class="pageoverflow">
        <p class="pagetext">{$mod->Lang('title_export')}:</p>
        <button type="submit" name="{$actionid}submit" value="xml_export" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button" aria-disabled="false">
            <span class="ui-button-icon-primary ui-icon ui-icon-arrowthickstop-1-e"></span>
            <span class="ui-button-text">{$mod->Lang('xml_export')}</span>
        </button>
    </div>
</fieldset>
<br>


{if $hasUsersGuideMod}{* if installed *}
<fieldset>
    <legend>{$mod->Lang('title_import_UsersGuide')} </legend>
        <div class="pageoverflow">
            <button type="submit" name="{$actionid}submit" value="import_UsersGuide_module" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button" aria-disabled="false">
                <span class="ui-button-icon-primary ui-icon ui-icon-arrowthickstop-1-w"></span>
                <span class="ui-button-text">{$mod->Lang('import_UsersGuide')} {$mod->Lang('text_UsersGuide')}</span>
            </button>
        </div>
</fieldset>
<br>
{/if}


{if $hasUserGuide2Mod}{* if installed *}
<fieldset>
    <legend>{$mod->Lang('title_import_UserGuide2')} </legend>
        <div class="pageoverflow">
            <button type="submit" name="{$actionid}submit" value="import_UserGuide2_module" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" role="button" aria-disabled="false">
                <span class="ui-button-icon-primary ui-icon ui-icon-arrowthickstop-1-w"></span>
                <span class="ui-button-text">{$mod->Lang('title_import_UserGuide2')} {$mod->Lang('text_UserGuide2')}</span>
            </button>
        </div>
</fieldset>
<br>
{/if}

{form_end}


{if $separate_settings}
    {tab_end}
{/if}