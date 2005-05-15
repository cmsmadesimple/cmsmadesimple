{$startform}
	<div class="pageoverflow">
		<p class="pagetext">*{$titletext}:</p>
		<p class="pageinput">{$inputtitle}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">*Category:</p>
		<p class="pageinput">{$inputcategory}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">Summary:</p>
		<p class="pageinput">{$inputsummary}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">*Content:</p>
		<p class="pageinput">{$inputcontent}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">Post Date:</p>
		<p class="pageinput">{html_select_date prefix=$postdateprefix time=$postdate start_year=2000 end_year=2010} {html_select_time prefix=$postdateprefix time=$postdate}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">*Status:</p>
		<p class="pageinput">{$status}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">Use Expiration:</p>
		<p class="pageinput">{$inputexp}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">Start Date:</p>
		<p class="pageinput">{html_select_date prefix=$startdateprefix time=$startdate start_year=2000 end_year=2010} {html_select_time prefix=$startdateprefix time=$startdate}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">End Date:</p>
		<p class="pageinput">{html_select_date prefix=$enddateprefix time=$enddate start_year=2000 end_year=2010} {html_select_time prefix=$enddateprefix time=$enddate}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$hidden}{$submit}{$cancel}</p>
	</div>
{$endform}