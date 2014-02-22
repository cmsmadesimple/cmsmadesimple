<script type="text/javascript">
$(document).ready(function(){
  $(document).on('click','#reseturl',function(){
    return confirm('{$mod->Lang('confirm_reseturl')}');
  });
  $(document).on('click','#settings_submit',function(){
    return confirm('{$mod->Lang('confirm_settings')}');
  });
});
</script>
{if isset($message)}<p>{$message}</p>{/if}

{form_start action='setprefs'}
{if isset($module_repository)}
  <div class="pageoverflow">
    <p class="pagetext"><label for="mr_url">{$mod->Lang('prompt_repository_url')}:</label></p>
    <p class="pageinput">
      <input type="text" name="{$actionid}url" id="mr_url" maxlength="255" value="{$module_repository}"/>
      <input type="submit" id="reseturl" name="{$actionid}reseturl" value="{$mod->Lang('reset')}"/>
    </p>
  </div>
 
   <div class="pageoverflow">
    <p class="pagetext"><label for="allowuninstall">{$mod->Lang('allowuninstall')}:</label>&nbsp;{cms_help key2='help_allowuninstall' title=$mod->Lang('allowuninstall')}</p>
    <p class="pageinput">
      <select id="allowuninstall" name="{$actionid}allowuninstall">{cms_yesno selected=$allowuninstall}</select>
    </p>
  </div>
{/if}

  <div class="pageoverflow">
    <p class="pagetext"><label>{$mod->Lang('prompt_dl_chunksize')}:</label>&nbsp;{cms_help key2='help_dl_chunksize' title=$mod->Lang('prompt_dl_chunksize')}</p>
    <p class="pageinput">
      <input type="text" name="{$actionid}dl_chunksize" value="{$dl_chunksize}" size="3" maxlength="3"/>
    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext"><label for="latestdepends">{$mod->Lang('latestdepends')}:</label>&nbsp;{cms_help key2='help_latestdepends' title=$mod->Lang('latestdepends')}</p>
    <p class="pageinput">
      <select id="latestdepends" name="{$actionid}latestdepends">{cms_yesno selected=$latestdepends}</select>
    </p>
  </div>

{if isset($developer_mode)}
  <div class="pageoverflow">
    <p class="pagetext"><label for="allowuninstall">{$mod->Lang('allowuninstall')}:</label>&nbsp;{cms_help key2='help_allowuninstall' title=$mod->Lang('allowuninstall')}</p>
    <p class="pageinput">
      <select id="allowuninstall" name="{$actionid}allowuninstall">{cms_yesno selected=$allowuninstall}</select>
    </p>
  </div>
{/if}

{if isset($disable_caching)}	
  <div class="pageoverflow">
    <p class="pagetext"><label for="disable_caching">{$mod->Lang('prompt_disable_caching')}:</label></p>
    <p class="pageinput">
      <select id="disable_caching" name="{$actionid}disable_caching">{cms_yesno selected=$disable_caching}</select>
    </p>
  </div>
{/if}

  <div class="pageoverflow">
    <p class="pagetext"></p>
    <p class="pageinput">
      <input type="submit" id="settings_submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>
    </p>
  </div>
{form_end}
