<h3>{$mod->Lang('edit_profile')}: <em>{$data.name}</em></h3>

{form_start}
<input type="hidden" name="{$actionid}profile" value="{$profile}"/>
<input type="hidden" name="{$actionid}origname" value="{$profile.name}"/>

  {if $profile.system}<div class="information">{$tmp='profiledesc_'|cat:$data.name}{$mod->Lang($tmp)}</div>{/if}

  <div class="pageoverflow">
    <p class="pagetext"></p>
    <p class="pageinput">
      <input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>
      <input type="submit" name="{$actionid}cancel" value="{$mod->Lang('cancel')}"/>
    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">
      <label for="profile_name">*{$mod->Lang('profile_name')}:</label>
    </p>
    <p class="pageinput">
      <input type="text" size="40" id="profile_name" name="{$actionid}profile_name" value="{$data.name}" {if $data.system}readonly disabled{/if}/>
    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">
      <label for="profile_label">*{$mod->Lang('profile_label')}:</label>
    </p>
    <p class="pageinput">
      <input type="text" size="80" id="profile_label" name="{$actionid}profile_label" value="{$data.label}" {if $data.system}readonly disabled{/if}/>
    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">
      <label for="profile_label">{$mod->Lang('profile_menubar')}:</label>
    </p>
    <p class="pageinput">
      <select id="profile_menubar" name="{$actionid}profile_menubar">{cms_yesno selected=$data.menubar}</select>
    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">
      <label for="profile_label">{$mod->Lang('profile_allowimages')}:</label>
    </p>
    <p class="pageinput">
      <select id="profile_allowimages" name="{$actionid}profile_allowimages">{cms_yesno selected=$data.allowimages}</select>
    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">
      <label for="profile_label">{$mod->Lang('profile_showstatusbar')}:</label>
    </p>
    <p class="pageinput">
      <select id="profile_showstatusbar" name="{$actionid}profile_showstatusbar">{cms_yesno selected=$data.showstatusbar}</select>
    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">
      <label for="profile_label">{$mod->Lang('profile_allowresize')}:</label>
    </p>
    <p class="pageinput">
      <select id="profile_allowresize" name="{$actionid}profile_allowresize">{cms_yesno selected=$data.allowresize}</select>
    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">
      <label for="profile_label">{$mod->Lang('profile_dfltstylesheet')}:</label>
    </p>
    <p class="pageinput">
      <select id="profile_dfltstylesheet" name="{$actionid}profile_dfltstylesheet">
      {html_options options=$stylesheets selected=$data.dfltstylesheet}
      </select>
    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">
      <label for="profile_label">{$mod->Lang('profile_allowcssoverride')}:</label>
    </p>
    <p class="pageinput">
      <select id="profile_allowcssoverride" name="{$actionid}profile_allowcssoverride">{cms_yesno selected=$data.allowcssoverride}</select>
    </p>
  </div>

{form_end}