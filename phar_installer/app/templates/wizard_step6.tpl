{* wizard step 6 *}
{extends file='wizard_step.tpl'}

{block name='logic'}
    {$subtitle = 'title_step6'|tr}
    {$current_step = '6'}
{/block}

{block name='contents'}

<div class="installer-form">
{wizard_form_start}
    {if $action != 'freshen'}
        <h3>{'prompt_sitename'|tr}</h3>
        <p>{'info_sitename'|tr}</p>

        <div class="row form-row">
            <div class="twelve-col">
                <input class="form-field required full-width" type="text" name="sitename" value="{$siteinfo.sitename}" placeholder="{'ph_sitename'|tr}" required="required" />
                <div class="corner red">
                    <i class="icon-asterisk"></i>
                </div>
            </div>
        </div>
    {/if}

{if !empty($language_list)}
    <h3>{'prompt_addlanguages'|tr}</h3>
    <p>{'info_addlanguages'|tr}</p>

    <div class="row form-row">
        <select class="form-field" name="languages[]" multiple="multiple" size="8">
            {html_options options=$language_list selected=$siteinfo.languages}
        </select>
    </div>
{/if}

    <div id="bottom_nav">
    <input class="action-button positive" type="submit" name="next" value="{'next'|tr} &rarr;" />
    </div>

{wizard_form_end}
</div>

{/block}
