{form_start action='savesettings'}

<div class="pageoverflow">
  <p class="pagetext"><label for="advancedmode">{$mod->Lang('enableadvanced')}:</label>&nbsp;{cms_help key2='help_advancedmode' title=$mod->Lang('enableadvanced')}</p>
  <p class="pageinput">
    <select id="advancedmode" name="{$actionid}advancedmode">
      {cms_yesno selected=$advancedmode}
    </select>
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext"><label for="showhidden">{$mod->Lang('showhiddenfiles')}:</label>&nbsp;{cms_help key2='help_showhiddenfiles' title=$mod->Lang('showhiddenfiles')}</p>
  <p class="pageinput">
    <select id="showhidden" name="{$actionid}showhiddenfiles">
      {cms_yesno selected=$showhiddenfiles}
    </select>
</div>

<div class="pageoverflow">
  <p class="pagetext"><label for="showthumbnails">{$mod->Lang('showthumbnails')}:</label>&nbsp;{cms_help key2='help_showthumbnails' title=$mod->Lang('showthumbnails')}</p>
  <p class="pageinput">
    <select id="showthumbnails" name="{$actionid}showthumbnails">
      {cms_yesno selected=$showthumbnails}
    </select>
</div>

<div class="pageoverflow">
  <p class="pagetext"><label for="createthumbs">{$mod->Lang('create_thumbnails')}:</label>&nbsp;{cms_help key2='help_create_thumbnails' title=$mod->Lang('create_thumbnails')}</p>
  <p class="pageinput">
    <select id="createthumbs" name="{$actionid}create_thumbnails">
      {cms_yesno selected=$create_thumbnails}
    </select>
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext"><label for="iconsize">{$mod->Lang('iconsize')}:</label>&nbsp;{cms_help key2='help_iconsize' title=$mod->Lang('iconsize')}</p>
  <p class="pageinput">
    <select id="iconsize" name="{$actionid}iconsize">
      {html_options options=$iconsizes selected=$iconsize}
    </select>
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext"><label for="permstyle">{$mod->Lang('permissionstyle')}:</label>&nbsp;{cms_help key2='help_permissionstyle' title=$mod->Lang('permissionstyle')}</p>
  <p class="pageinput">
    <select id="permstyle" name="{$actionid}permissionstyle">
      {html_options options=$permstyles selected=$permissionstyle}
    </select>
  </p>
</div>
<div class="pageoverflow">
  <p class="pageinput">
    <input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>
  </p>
</div>
{form_end}