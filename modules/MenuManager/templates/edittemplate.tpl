{if isset($errormsg) && $errormsg != ""}
	<div class="pageerror">{$errormsg}</div>
{/if}
{$startform}
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$hidden|default:""}{$submit}{$cancel}{$apply|default:""}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">*{$newtemplate}:</p>
		<p class="pageinput">{$inputname}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">*{$content}:</p>
		<p class="pageinput">{$inputcontent}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$hidden|default:""}{$submit}{$cancel}{$apply|default:""}</p>
	</div>
{$endform}
