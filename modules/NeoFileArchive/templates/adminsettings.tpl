{$formstart}

<div class="pageoverflow">
	<p class="pagetext">{$module->Lang("imagelocation")}</p>
	<p class="pageinput">{$imagelocationinput}{$module->Lang("imagelocationhelp")}</p>
</div>


<div class="pageoverflow">
	<p class="pagetext">{$module->Lang("includejq")}</p>
	<p class="pageinput">{$includejqinput}{$module->Lang("includejqhelp")}</p>
</div>

<div class="pageoverflow">
	<p class="pagetext">{$module->Lang("labelslist")}</p>
	<p class="pageinput">{$labelsinput}{*$module->Lang("imagelocationhelp")*}</p>
</div>

<div class="pageoverflow">
	<p class="pagetext">&nbsp;</p>
	<p class="pageinput">{$submit}</p>
</div>
{$formend}