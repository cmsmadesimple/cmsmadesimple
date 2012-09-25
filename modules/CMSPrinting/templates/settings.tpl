{$startform}
   <div class="pageoverflow">
		<p class="pagetext">{$pdfenabletext}</p>
		<p class="pageinput">{$pdfenableinput}<br/>{$pdfenablehelp}</p>
	</div>

  {if isset($pdfheadertext) && ($pdfheadertext!="")}
	<div class="pageoverflow">
		<p class="pagetext">{$pdfheadertext}</p>
		<p class="pageinput">{$pdfheaderinput}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$headerfontsizetext}</p>
		<p class="pageinput">{$headerfontsizeinput}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$contentfontsizetext}</p>
		<p class="pageinput">{$contentfontsizeinput}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$fonttypetext}</p>
		<p class="pageinput">{$fonttypeinput}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$orientationtext}</p>
		<p class="pageinput">{$orientationinput}</p>
	</div>
  
  {/if}

	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$submit}</p>
	</div>
{$endform}
