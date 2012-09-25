{* original form template *}
{if isset($error)}
  <h3><font color="red">{$error}</font></h3>
{else}
  {if isset($message)}
    <h3>{$message}</h3>
  {/if}
{/if}
{$startform}
	<div class="pageoverflow">
		<p class="pagetext">*{$titletext}:</p>
		<p class="pageinput">{$inputtitle}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$categorytext}:</p>
		<p class="pageinput">{$inputcategory}</p>
	</div>
{if !isset($hide_summary_field) or $hide_summary_field == 0}
	<div class="pageoverflow">
		<p class="pagetext">{$summarytext}:</p>
		<p class="pageinput">{$inputsummary}</p>
	</div>
{/if}
	<div class="pageoverflow">
		<p class="pagetext">*{$contenttext}:</p>
		<p class="pageinput">{$inputcontent}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$extratext}:</p>
		<p class="pageinput">{$inputextra}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$startdatetext}:</p>
		<p class="pageinput">{html_select_date prefix=$startdateprefix time=$startdate end_year="+15"} {html_select_time prefix=$startdateprefix time=$startdate}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$enddatetext}:</p>
		<p class="pageinput">{html_select_date prefix=$enddateprefix time=$enddate end_year="+15"} {html_select_time prefix=$enddateprefix time=$enddate}</p>
	</div>
	{if isset($customfields)}
	   {foreach from=$customfields item='onefield'}
	      <div class="pageoverflow">
		<p class="pagetext">{$onefield->name}:</p>
		<p class="pageinput">{$onefield->field}</p>
	      </div>
	   {/foreach}
	{/if}
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$hidden}{$submit}{$cancel}</p>
	</div>
{$endform}
