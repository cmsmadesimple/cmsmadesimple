<fieldset>
{$formstart}
<input type="hidden" name="MAX_FILE_SIZE" value="{$maxfilesize}" />
{$path}
{foreach from=$inputs item=input}
<div class="pageoverflow">

<p class="pagetext">
<strong>{$input->label}</strong>
</p>
<p class="pageinput">
{$input->fileinput}{$input->unpackinput}{$unpacktext}
</p>
</div>
{/foreach}
<div class="pageoverflow">
<p class="pagetext">
<strong>&nbsp;</strong>
</p>
<p class="pageinput">
{$submit}
</p>
</div>
{$formend}
</fieldset>
