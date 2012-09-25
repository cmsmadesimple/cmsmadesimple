{$formstart}

<div class="pageoverflow">
  <p class="pagetext">{$advancedmodetext}:</p>
  <p class="pageinput">{$advancedmodeinput} {$advancedmodehelp}</p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{$showhiddenfilestext}:</p>
  <p class="pageinput">{$showhiddenfilesinput} {$showhiddenfileshelp}</p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{$showthumbnailstext}:</p>
  <p class="pageinput">{$showthumbnailsinput} {$showthumbnailshelp}</p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('create_thumbnails')}:</p>
  <p class="pageinput">
    <select name="{$actionid}create_thumbnails">
    {html_options options=$yesno_options selected=$create_thumbnails}
    </select>
    {$mod->Lang('info_create_thumbnails')}
  </p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{$iconsizetext}:</p>
  <p class="pageinput">{$iconsizeinput}</p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{$thousanddelimitertext}:</p>
  <p class="pageinput">{$thousanddelimiterinput}</p>
</div>

<div class="pageoverflow">
  <p class="pagetext">{$permissionstyletext}:</p>
  <p class="pageinput">{$permissionstyleinput}</p>
</div>
{*
<fieldset>
  <label>{$filepickersettings}:</label>
  <div class="pageoverflow">
    <p class="pagetext">{$filepickersizetext}:</p>
    <p class="pageinput">
      {$filepickerwidthtext}
      {$filepickerwidthinput}
      {$filepickerheighttext}
      {$filepickerheightinput}

    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">{$filepickertyletext}:</p>
    <p class="pageinput">{$filepickerstyleinput}</p>
  </div>

</fieldset>
*}
<div class="pageoverflow">
  <p class="pagetext">&nbsp;</p>
  <p class="pageinput">{$submit}</p>
</div>
{$formend}
