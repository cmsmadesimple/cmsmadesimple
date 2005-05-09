<div class="AdminForm">

{$startform}

<table width="100%" border="0">

	<tr>
		<th>*{$titletext}:</th>
		<td>{$inputtitle}</td>
	</tr>
	<tr>
		<th>*Category:</th>
		<td>{$inputcategory}</td>
	</tr>
	<tr>
		<th>Summary:</th>
		<td>{$inputsummary}</td>
	</tr>
	<tr>
		<th>*Content:</th>
		<td>{$inputcontent}</td>
	</tr>
	<tr>
		<th>Post Date:</th>
		<td>{html_select_date prefix=$postdateprefix time=$postdate start_year=2000 end_year=2010} {html_select_time prefix=$postdateprefix time=$postdate}</td>
	</tr>
	<tr>
		<th>*Status:</th>
		<td>{$status}</td>
	</tr>
	<tr>
		<th>Use Expiration:</th>
		<td>{$inputexp}</td>
	</tr>
	<tr>
		<th>Start Date:</th>
		<td>{html_select_date prefix=$startdateprefix time=$startdate start_year=2000 end_year=2010} {html_select_time prefix=$startdateprefix time=$startdate}</td>
	</tr>
	<tr>
		<th>End Date:</th>
		<td>{html_select_date prefix=$enddateprefix time=$enddate start_year=2000 end_year=2010} {html_select_time prefix=$enddateprefix time=$enddate}</td>
	</tr>
	<tr>
		<td>&nbsp;{$hidden}</td>
		<td>{$submit}{$cancel}</td>
	</tr>

</table>

{$endform}

</div>
