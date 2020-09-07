{if $profile->id}
<h3>{$mod->Lang('hdr_edit_profile')} <em>({$profile->id})</em></h3>
{else}
<h3>{$mod->Lang('hdr_add_profile')}</h3>
{/if}

{form_start pid=$profile->id}
<div class="pageoverflow">
  <p class="pageinput">
    <input type="submit" id="submit" name="{$actionid}submit" value="{lang('submit')}"/>
    <input type="submit" id="cancel" name="{$actionid}cancel" value="{lang('cancel')}" formnovalidate/>
  </p>
</div>
<hr/>
<div class="c_full cf">
  <label for="profile_name" class="grid_2 required">* {$mod->Lang('name')}:</label>
  <p class="grid_9"><input type="text" size="40" id="profile_name" name="{$actionid}name" value="{$profile->name|cms_escape}" required/>
    &nbsp;{cms_help key2='HelpPopup_ProfileName' title=$mod->Lang('HelpPopupTitle_ProfileName')}
  </p>
</div>
<div class="c_full cf">
  <label for="profile_top" class="grid_2">{$mod->Lang('topdir')}:</label>
  <p class="grid_9"><input type="text" id="profile_top" name="{$actionid}top" value="{$profile->reltop}" size="80"/>
  &nbsp;{cms_help key2='HelpPopup_ProfileDir' title=$mod->Lang('HelpPopupTitle_ProfileDir')}
  </p>
</div>
<div class="c_full cf">
  <label for="profile_thumbs" class="grid_2">{$mod->Lang('show_thumbs')}:</label>
  <p class="grid_9"><select id="profile_thumbs" name="{$actionid}show_thumbs">{cms_yesno selected=$profile->show_thumbs}</select>
  &nbsp;{cms_help key2='HelpPopup_ProfileShowthumbs' title=$mod->Lang('HelpPopupTitle_ProfileShowthumbs')}
  </p>
</div>
<div class="c_full cf">
  <label for="profile_canupload" class="grid_2">{$mod->Lang('can_upload')}:</label>
  <p class="grid_9"><select id="profile_canupload" name="{$actionid}can_upload">{cms_yesno selected=$profile->can_upload}</select>
  &nbsp;{cms_help key2='HelpPopup_ProfileCan_Upload' title=$mod->Lang('HelpPopupTitle_ProfileCan_Upload')}
  </p>
</div>
<div class="c_full cf">
  <label for="profile_candelete" class="grid_2">{$mod->Lang('can_delete')}:</label>
  <p class="grid_9"><select id="profile_candelete" name="{$actionid}can_delete">{cms_yesno selected=$profile->can_delete}</select>
  &nbsp;{cms_help key2='HelpPopup_ProfileCan_Delete' title=$mod->Lang('HelpPopupTitle_ProfileCan_Delete')}
  </p>
</div>
<div class="c_full cf">
  <label for="profile_canmkdir" class="grid_2">{$mod->Lang('can_mkdir')}:</label>
  <p class="grid_9"><select id="profile_canmkdir" name="{$actionid}can_mkdir">{cms_yesno selected=$profile->can_mkdir}</select>
  &nbsp;{cms_help key2='HelpPopup_ProfileCan_Mkdir' title=$mod->Lang('HelpPopupTitle_ProfileCan_Mkdir')}
  </p>
</div>
{form_end}