{* wizard step 5 *}

{extends file='wizard_step.tpl'}

{block name='logic'}
    {$subtitle = 'title_step5'|tr}
    {$current_step = '5'}
{/block}

{block name='contents'}

<div class="installer-form">
{wizard_form_start}
    <p>{'info_adminaccount'|tr}</p>

    <fieldset>
        <div class="row form-row">
            <div class="four-col">
                <label>{'username'|tr}</label>
            </div>
            <div class="eight-col">
                <input class="form-field required full-width" type="text" name="username" required="required" />
                <div class="corner red">
                    <i class="icon-asterisk"></i>
                </div>
            </div>
        </div>
        <div class="row form-row">
            <div class="four-col">
                <label>{'emailaddr'|tr}</label>
            </div>
            <div class="eight-col">
            {if $verbose}
                <input class="form-field full-width" type="email" name="emailaddr" />
            {else}
                <input class="form-field required full-width" type="email" name="emailaddr" required="required" />
                <div class="corner red">
                    <i class="icon-asterisk"></i>
                </div>
	    {/if}
            </div>
        </div>
        <div class="row form-row">
            <div class="four-col">
                <label>{'password'|tr}</label>
            </div>
            <div class="eight-col">
                <input class="form-field required full-width" type="password" name="password" required="required" autocomplete="off" />
                <div class="corner red">
                    <i class="icon-asterisk"></i>
                </div>
            </div>
        </div>
        <div class="row form-row">
            <div class="four-col">
                <label>{'repeatpw'|tr}</label>
            </div>
            <div class="eight-col">
                <input class="form-field required full-width" type="password" name="repeatpw" required="required" autocomplete="off" />
                <div class="corner red">
                    <i class="icon-asterisk"></i>
                </div>
            </div>
        </div>
        {if $verbose}
        <div class="row form-row">
            <div class="four-col">
                <label>{'saltpasswords'|tr}</label>
            </div>
            <div class="eight-col">
                <select name="saltpw" class="form-field">
                    {html_options options=$yesno selected=$account.saltpw}
                </select>
            </div>
        </div>
        <div class="row form-row">
            <div class="four-col">
                <label>{'emailaccountinfo'|tr}</label>
            </div>
            <div class="eight-col">
                <select id="emailacctinfo" name="emailaccountinfo" class="form-field">
                    {html_options options=$yesno selected=$account.emailaccountinfo}
                </select>
            </div>
        </div>
        {/if}

	<div id="bottom_nav">
        <input class="action-button positive" type="submit" name="next" value="{'next'|tr} &rarr;" />
	</div>

{wizard_form_end}
</div>

{/block}
