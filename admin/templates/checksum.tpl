<div class="pagecontainer">
{* checksum verification template *}

{if isset($error)}
<div class="pageerrorcontainer">
 <div class="pageoverflow">
   <p class="pageerror">{$error}</p>
 </div>
 </div>
{/if}

{if isset($message)}
<div class="pagecontainer">
 <div class="pageoverflow">
   <p style="color: green;">{$message}</p>
 </div>
 </div>
{/if}

<form action="{$smarty.server.PHP_SELF}" method="post" enctype="multipart/form-data">
<div>
  <input type="hidden" name="{$cms_secure_param_name}" value="{$cms_user_key}" />
  <input type="hidden" name="action" value="upload" />
</div>
<fieldset>
  <legend>{lang key='perform_validation'}</legend>
  <div class="pageoverflow">
    <p>{lang key='info_validation'}</p>
  </div>
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
<form action="{$smarty.server.PHP_SELF}" method="post" enctype="multipart/form-data">
<div>
  <input type="hidden" name="{$cms_secure_param_name}" value="{$cms_user_key}" />
  <input type="hidden" name="action" value="download" />
</div>
<fieldset>
  <legend>{lang key='download_cksum_file'}</legend>
  <div class="pageoverflow">
    <p>{lang key='info_generate_cksum_file'}</p>
  </div>
  <div class="pageoverflow">
    <p class="pagetext">&nbsp;</p>
    <p class="pageinput"><input type="submit" name="submit" value="{lang key='submit'}" /></p>
  </div>
</fieldset>
</form>

{* end template *}
</div>
