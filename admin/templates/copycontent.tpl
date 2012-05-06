<div class="pagecontainer c_full cf">

  {$showheader}

  <form action="copycontent.php">
  <div class="pageoverflow">
    <p class="pagetext">&nbsp;</p>
    <p class="pageinput"><input type="submit" name="submit" value="{$lang_submit}" />&nbsp;<input type="submit" name="cancel" value="{$lang_cancel}" /></p>
  </div>

  <div class='grid_6 alpha'>
    <fieldset>
    <legend><strong>{$lang_copyfrom}:</strong></legend>

    <div class="pageoverflow">
      <p class="pagetext">{$lang_pageid}:</p>
      <p class="pageinput">{$fromid}</p>
    </div>

    <div class="pageoverflow">
      <p class="pagetext">{$lang_pagealias}:</p>
      <p class="pageinput">{$fromobj->Alias()}</p>
    </div>

    <div class="pageoverflow">
      <p class="pagetext">{$lang_pagetitle}:</p>
      <p class="pageinput">{$fromobj->Name()}</p>
    </div>

    <div class="pageoverflow">
      <p class="pagetext">{$lang_pagemenutext}:</p>
      <p class="pageinput">{$fromobj->MenuText()}</p>
    </div>

    <div class="pageoverflow">
      <p class="pagetext">{$lang_pagetype}:</p>
      <p class="pageinput">{$fromobj->FriendlyName()}</p>
    </div>

    <div class="pageoverflow">
      <p class="pagetext">{$lang_pageparent}:</p>
      <p class="pageinput">{$parentinfo}</p>
    </div>
    </fieldset>
    </div>

    <div class='grid_6'>
    <fieldset>
    <legend><strong>{$lang_copyto}:</strong></legend>
    <div>
      <input type="hidden" name="{$cms_secure_param_name}" value="{$cms_user_key}" />
    </div>
    <div class="hidden"><input type="hidden" name="content_id" value="{$fromid}" /></div>

    {if isset($info_pagealias)}
    <div class="pageoverflow">
      <p class="pagetext">{$lang_pagealias}:</p>
      <p class="pageinput"><input type="text" name="to_alias" size="50" maxlength="255" value="" />
        <br/>{$info_pagealias}
	{if isset($info_alias)}<br/>{$info_alias}{/if}</p>
    </div>
    {/if}

    <div class="pageoverflow">
      <p class="pagetext">{$lang_pagetitle}:</p>
      <p class="pageinput"><input type="text" name="to_title" size="50" maxlength="255" value="{$fromobj->Name()}" /></p>
    </div>

    <div class="pageoverflow">
      <p class="pagetext">{$lang_pagemenutext}:</p>
      <p class="pageinput"><input type="text" name="to_menutext" size="50" maxlength="255" value="{$fromobj->MenuText()}" /></p>
    </div>

    <div class="pageoverflow">
      <p class="pagetext">{$lang_pageparent}:</p>
      <p class="pageinput">{$input_parentdropdown}</p>
    </div>

    {if isset($lang_pageaccesskey)}
    <div class="pageoverflow">
      <p class="pagetext">{$lang_pageaccesskey}:</p>
      <p class="pageinput"><input type="text" name="to_accesskey" size="5" maxlength="5" value="" /></p>
    </div>
    {/if}

    </fieldset>
    </div>
  </form>

</div>
