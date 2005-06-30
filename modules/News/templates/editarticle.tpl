{$startform}
	<div class="pageoverflow">
		<p class="pagetext">*{$titletext}:</p>
		<p class="pageinput">{$inputtitle}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">*{$categorytext}:</p>
		<p class="pageinput">{$inputcategory}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$summarytext}:</p>
		<p class="pageinput">{$inputsummary}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">*{$contenttext}:</p>
		<p class="pageinput">{$inputcontent}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$postdatetext}:</p>
		<p class="pageinput">{html_select_date prefix=$postdateprefix time=$postdate start_year=2000 end_year=2010} {html_select_time prefix=$postdateprefix time=$postdate}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">*{$statustext}:</p>
		<p class="pageinput">{$status}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$useexpirationtext}:</p>
		<p class="pageinput">{$inputexp}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$startdatetext}:</p>
		<p class="pageinput">{html_select_date prefix=$startdateprefix time=$startdate start_year=2000 end_year=2010} {html_select_time prefix=$startdateprefix time=$startdate}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$enddatetext}:</p>
		<p class="pageinput">{html_select_date prefix=$enddateprefix time=$enddate start_year=2000 end_year=2010} {html_select_time prefix=$enddateprefix time=$enddate}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$hidden}{$submit}{$cancel}</p>
	</div>
{$endform}
