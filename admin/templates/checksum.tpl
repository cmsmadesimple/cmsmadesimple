<div class="pagecontainer">
{* checksum verification template *}

{if isset($message)}
<div class="pageerrorcontainer">
 <div c lass="pageoverflow">
   <p class="pageerror">{$message}</p>
 </div>
</div>
{/if}

<form action="{$smarty.server.PHP_SELF}?action=upload" method="post" enctype="multipart/form-data">
<fieldset>
  <legend>{lang key='perform_validation'}</legend>
  <div class="pageoverflow">
    <p>{lang key='info_validation'}</p>
  <div>
  <div class="pageoverflow">
    <p class="pagetext">{lang key='upload_cksum_file'}</p>
    <p class="pageinput"><input type="file" name="cksumdat" size="30" maxlength="255" /></p>
  </div>
  <div class="pageoverflow">
    <p class="pagetext">&nbsp;</p>
    <p class="pageinput"><input type="submit" name="submit" value="{lang key='submit'}" /></p>
  </div>
</fieldset>
</form>

<br/>
<form action="{$smarty.server.PHP_SELF}?action=download" method="post" enctype="multipart/form-data">
<fieldset>
  <legend>{lang key='download_cksum_file'}</legend>
  <div class="pageoverflow">
    <p>{lang key='info_generate_cksum_file'}</p>
  <div>
  <div class="pageoverflow">
    <p class="pagetext">&nbsp;</p>
    <p class="pageinput"><input type="submit" name="submit" value="{lang key='submit'}" /></p>
  </div>
</fieldset>
</form>

{* end template *}
</div>