{if isset($errormsg) && $errormsg != ""}
	<div class="pageerror">{$errormsg}</div>
{/if}
{$startform}
	<div class="pageoverflow">
		<p class="pagetext">*{$newtemplate}:</p>
		<p class="pageinput">{$inputname}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$hidden}{$submit}{$cancel}</p>
	</div>
{$endform}
