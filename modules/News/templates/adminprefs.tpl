{$startform}
<div class="pageoverflow">
  <p class="pageinput">
    <input type="submit" name="{$actionid}optionssubmitbutton" value="{$mod->Lang('submit')}"/>
  </p>
</div>

<fieldset>
<legend>{$title_submission_settings}:</legend>
        <div class="pageoverflow">
	    <p class="pagetext"><label for="alert_drafts">{$mod->Lang('prompt_alert_drafts')}:</label> {cms_help key='help_opt_alert_drafts' title=$mod->Lang('prompt_alert_drafts')}</p>
	    <p class="pageinput">
	        <select id="alert_drafts" name="{$actionid}alert_drafts">
		{cms_yesno selected=$alert_drafts}
		</select>
	    </p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext"><label for="dfltcat">{$title_default_category}:</label> {cms_help key='help_opt_dflt_category' title=$title_submission_settings}</p>
		<p class="pageinput">
                  <select id="dfltcat" name="{$actionid}default_category">
                  {html_options options=$categorylist selected=$default_category}
                  </select>
                </p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext"><label for="fld2">{$title_allowed_upload_types}:</label> {cms_help key='help_opt_allowed_upload_types' title=$title_allowed_upload_types}</p>
		<p class="pageinput">
                  <input type="text" id="fld2" name="{$actionid}allowed_upload_types" value="{$allowed_upload_types}" size="50"/>
                </p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext"><label for="fld3">{$title_hide_summary_field}:</label> {cms_help key='help_opt_hide_summary' title=$title_hide_summary_field}</p>
		<p class="pageinput">
                  <input type="checkbox" id="fld3" name="{$actionid}hide_summary_field" value="1" {if $hide_summary_field == 1}checked="checked"{/if}/>
                </p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext"><label for="fld4">{$title_allow_summary_wysiwyg}: {cms_help key='help_opt_allow_summary_wysiwyg' title=$title_allow_summary_wysiwyg}</label></p>
		<p class="pageinput">
                  <input type="checkbox" id="fld4" name="{$actionid}allow_summary_wysiwyg" value="1" {if $allow_summary_wysiwyg}checked="checked"{/if}/>
                </p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext"><label for="fld5">{$title_expiry_interval}:</label> {cms_help key='help_opt_expiry_interval' title=$title_expiry_interval}</p>
		<p class="pageinput">
                  <input type="text" id="fld5" name="{$actionid}expiry_interval" value="{$expiry_interval}" size="4" maxlength="4"/>
                </p>
	</div>
</fieldset>
<br/>

<fieldset>
<legend>{$title_fesubmit_settings}:</legend>
	<div class="pageoverflow">
		<p class="pagetext"><label for="fld9_a">{$mod->Lang('prompt_allow_fesubmit')}:</label></p>
		<p class="pageinput">
                  <select id="fld9_a" name="{$actionid}allow_fesubmit">
                  {cms_yesno selected=$allow_fesubmit}
                  </select>
		  <br/>{$mod->Lang('info_allow_fesubmit')}
                </p>
	</div>

	<div class="pageoverflow">
		<p class="pagetext"><label for="fld9">{$title_fesubmit_status}:</label></p>
		<p class="pageinput">
                  <select id="fld9" name="{$actionid}fesubmit_status">
                  {html_options options=$statuses selected=$fesubmit_status}
                  </select>
                </p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext"><label for="fld10">{$title_fesubmit_redirect}:</label> {cms_help key='help_fesubmit_redirect' title=$title_fesubmit_redirect}</p>
		<p class="pageinput">
                   <input type="text" id="fld10" name="{$actionid}fesubmit_redirect" value="{$fesubmit_redirect}" size="20" maxlength="20"/>
                </p>
	</div>

  <fieldset>
  <legend>{$title_notification_settings}:</legend>
        <div class="information">{$mod->Lang('info_fesubmit_notification')}</div>
	<div class="pageoverflow">
		<p class="pagetext"><label for="fld6">{$title_formsubmit_emailaddress}:</label></p>
		<p class="pageinput">
                   <input type="text" id="fld6" name="{$actionid}formsubmit_emailaddress" value="{$formsubmit_emailaddress|cms_escape}" size="50" maxlength="255"/>
                </p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext"><label for="fld7">{$title_email_subject}:</label></p>
		<p class="pageinput">
		  <input type="text" id="fld7" name="{$actionid}email_subject" value="{$email_subject|cms_escape}" size="50" maxlength="255"/>
                </p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext"><label for="fld8">{$title_email_template}:</label></p>
		<p class="pageinput">
                  <textarea id="fld8" name="{$actionid}email_template" rows="5" cols="80">{$email_template}</textarea>
                </p>
	</div>
  </fieldset>

</fieldset>
<br/>

<fieldset>
<legend>{$title_detail_settings}:</legend>
	<div class="pageoverflow">
		<p class="pagetext">{$title_detail_returnid}: {cms_help key='info_detail_returnid' title=$title_detail_returnid}</p>
		<p class="pageinput">{$input_detail_returnid}
	</div>
	<div class="pageoverflow">
		<p class="pagetext"><label for="fld12">{$title_expired_searchable}:</label> {cms_help key='info_expired_searchable' title=$title_expired_searchable}</p>
		<p class="pageinput">
                  <input type="checkbox" id="fld12" name="{$actionid}expired_searchable" value="1" {if $expired_searchable}checked="checked"{/if}/>
                </p>
	</div>
        <div class="pageoverflow">
	  <p class="pagetext"><label for="fld13">{$title_expired_viewable}</label> {cms_help key='info_expired_viewable' title=$title_expired_viewable}</p>
	  <p class="pagetext">
            <input type="checkbox" id="fld13" name="{$actionid}expired_viewable" value="1" {if $expired_viewable}checked="checked"{/if}/>
          </p>
        </div>
</fieldset>

{$endform}