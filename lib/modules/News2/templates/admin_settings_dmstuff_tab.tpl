
<div style="text-align: center;">
{if !$have_dmsetup}

  <div class="information">
    {$mod->Lang('info_dmstuff1')}
  </div>

  <div class="c_full cf">
  <br/>
  {form_start action=admin_settings_dmstuff_tab}
     <button>{$mod->Lang('create_tpltypes')}</button>
  {form_end}
  </div>

{else}

  <div class="information">
    {$mod->Lang('info_dmstuff2')}
  </div>

{/if}
</div>
