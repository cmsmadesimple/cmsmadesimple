<div class="pagecontainer">
{if isset($errors)}
<div class="pageerrors">{$errors}</div>
{/if}

{if isset($message)}
<div class="pagemcontainer">{$message}</div>
{/if}

{if isset($body)}
{$body}
{/if}

{if isset($modules) && !isset($body)}
<div class="pageheader">
 {'modules'|lang}
</div>
<table class="pagetable" cellspacing="0">
  <thead>
    <tr>
      <th><span title="{'info_sysmodule'|lang}">*</span></th>
      <th>{'name'|lang}</th>
      <th>{'version'|lang}</th>
      <th>{'status'|lang}</th>
      <th class="pagepos">{'active'|lang}</th>
      <th>{'action'|lang}</th>
      <th>{'help'|lang}</th>
      <th>{'about'|lang}</th>
      <th>{'export'|lang}</th>
      </tr>
    </tr>
  </thead>
  {foreach from=$modules name='modules' key='module' item='onemodule'}
  {cycle assign='rowclass' values='row1,row2'}
  <tr class="{$rowclass}" onmouseover="this.className='{$rowclass}hover';" onmouseout="this.className='{$rowclass}';">
    <td>
      {if $onemodule.sysmodule == 1}<span title="{'this_sysmodule'|lang}">*</span>{/if}
    </td>
    <td >
      {if isset($onemodule.help_url)}
        <a href="{$onemodule.help_url}" title="{'help'|lang}">{$onemodule.name}</a>
      {else}
        {$onemodule.name}
      {/if}
    </td>
    <td>{$onemodule.version}</td>
    {if $onemodule.status_spans} 
      <td colspan="3">
       {'<br/>'|implode:$onemodule.statuscol}</td>
      </td>
    {else}
      <td>
       {'<br/>'|implode:$onemodule.statuscol}</td>
      </td>
      <td>{$onemodule.activecol}</td>
      <td>{'<br/>'|implode:$onemodule.actioncol}</td>
    {/if}
    <td>
      {if isset($onemodule.help_url)}
        <a href="{$onemodule.help_url}" title="{'help'|lang}">{'help'|lang}</a>
      {/if}
    </td>
    <td><a href="{$onemodule.about_url}" title="{'about'|lang}">{'about'|lang}</a></td>
    <td>{$onemodule.export}</td>
  </tr>
  {/foreach}
  <tbody>
  </tbody>
</table>
{/if}

{if isset($import_url) && !isset($body)}
<form method="post" action="{$import_url}" enctype="multipart/form-data">
<fieldset>
  <legend>{'uploadxmlfile'|lang}</legend>
  <div class="pageoverflow">
    <p class="pagetext">{'uploadfile'|lang}:</p>
    <p class="pageinput">
      <input type="file" name="browse_xml"/>
    </p>
  </div>
  <div class="pageoverflow">
    <p class="pagetext">{'overwritemodule'|lang}:</p>
    <p class="pageinput">
      <input type="checkbox" name="allowoverwrite" value="1" />
    </p>
  </div>
  <div class="pageoverflow">
    <p class="pagetext">&nbsp;</p>
    <p class="pageinput">
      <input type="submit" name="submit" value="{'submit'|lang}" />
    </p>
  </div>
</fieldset>
</form>
{/if}

</div>{* pagecontainer *}