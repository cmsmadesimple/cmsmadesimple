{$startform}
<fieldset>
<legend>{$title_submission_settings}</legend>
	<div class="pageoverflow">
		<p class="pagetext">{$title_default_category}:</p>
		<p class="pageinput">{$input_default_category}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$title_allowed_upload_types}:</p>
		<p class="pageinput">{$input_allowed_upload_types}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$title_hide_summary_field}:</p>
		<p class="pageinput">{$input_hide_summary_field}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$title_allow_summary_wysiwyg}:</p>
		<p class="pageinput">{$input_allow_summary_wysiwyg}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$title_expiry_interval}:</p>
		<p class="pageinput">{$input_expiry_interval}</p>
	</div>
</fieldset>
<br/>
<fieldset>
<legend>{$title_notification_settings}</legend>
	<div class="pageoverflow">
		<p class="pagetext">{$title_formsubmit_emailaddress}:</p>
		<p class="pageinput">{$input_formsubmit_emailaddress}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$title_email_subject}:</p>
		<p class="pageinput">{$input_email_subject}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$title_email_template}:</p>
		<p class="pageinput">{$input_email_template}</p>
	</div>
</fieldset>
<br/>

<fieldset>
<legend>{$title_fesubmit_settings}</legend>
	<div class="pageoverflow">
		<p class="pagetext">{$title_fesubmit_status}:</p>
		<p class="pageinput">{$input_fesubmit_status}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$title_fesubmit_redirect}:</p>
		<p class="pageinput">{$input_fesubmit_redirect}</p>
	</div>
</fieldset>
<br/>

<fieldset>
<legend>{$title_detail_settings}</legend>
	<div class="pageoverflow">
		<p class="pagetext">{$title_detail_returnid}:</p>
		<p class="pageinput">{$input_detail_returnid}<br/>{$info_detail_returnid}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$title_expired_searchable}:</p>
		<p class="pageinput">{$input_expired_searchable}</p>
	</div>
        <div class="pageoverflow">
	  <p class="pagetext">{$title_expired_viewable}</p>
	  <p class="pagetext">
            <input type="checkbox" name="{$actionid}expired_viewable" value="1" {if $expired_viewable}checked="checked"{/if}/>
            <br/>
            {$info_expired_viewable}
          </p>
        </div>
</fieldset>
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$submit}</p>
	</div>
{$endform}
