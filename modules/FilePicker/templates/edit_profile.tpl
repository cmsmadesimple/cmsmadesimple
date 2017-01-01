{form_start pid=$profile->id}
<div class="pageoverflow">
  <p class="pageinput">
    <input type="submit" id="submit" name="{$actionid}submit" value="{lang('submit')}"/>
    <input type="submit" id="cancel" name="{$actionid}cancel" value="{lang('cancel')}"/>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('ProfileName')}:&nbsp;{cms_help key2='HelpPopup_ProfileName' title=$mod->Lang('HelpPopupTitle_ProfileName')}</p>
  <p class="pageinput"><input type="text" name="{$actionid}name" value="{$profile->name}"/></p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('dir')}:&nbsp;{cms_help key2='HelpPopup_ProfileDir' title=$mod->Lang('HelpPopupTitle_ProfileDir')}</p>
  <p class="pageinput"><input type="text" name="{$actionid}dir" value="{$profile->dir}" size="80"/></p>
</div
><div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('fileextensions')}:&nbsp;{cms_help key2='HelpPopup_ProfileFileExtensions' title=$mod->Lang('HelpPopupTitle_ProfileFileExtensions')}</p>
  <p class="pageinput"><input type="text" name="{$actionid}file_extensions" value="{$profile->file_extensions}" size="80"/></p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('show_thumbs')}:&nbsp;{cms_help key2='HelpPopup_ProfileShowthumbs' title=$mod->Lang('HelpPopupTitle_ProfileShowthumbs')}</p>
  <p class="pageinput"><select name="{$actionid}show_thumbs">{cms_yesno selected=$profile->show_thumbs}</select></p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('can_upload')}:&nbsp;{cms_help key2='HelpPopup_ProfileCan_Upload' title=$mod->Lang('HelpPopupTitle_ProfileCan_Upload')}</p>
  <p class="pageinput"><select name="{$actionid}can_upload">{cms_yesno selected=$profile->can_upload}</select></p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('can_delete')}:&nbsp;{cms_help key2='HelpPopup_ProfileCan_Delete' title=$mod->Lang('HelpPopupTitle_ProfileCan_Delete')}</p>
  <p class="pageinput"><select name="{$actionid}can_delete">{cms_yesno selected=$profile->can_delete}</select></p>
</div>
{form_end}