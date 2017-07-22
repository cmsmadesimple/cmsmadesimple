<div class="pageoptions" style="text-align: right; float: right; margin-right: 3%;">
  <a href="{$back_url}">{admin_icon icon='back.gif'}&nbsp;{$ModuleManager->Lang('back')}</a>
</div>

<p class="pageheader">{$ModuleManager->Lang('title_missingdeps2')}:</p>
<table class="pagetable">
  <tr>
    <td>{$ModuleManager->Lang('nametext')}:</td>
    <td>{$info.name}</td>
  </tr>
  <tr>
    <td>{$ModuleManager->Lang('vertext')}:</td>
    <td>{$info.version}</td>
  </tr>
</table>

<table class="pagetable">
  <thead>
    <tr>
      <th>{$ModuleManager->Lang('nametext')}</th>
      <th>{$ModuleManager->Lang('minversion')}</th>
    </tr>
  </thead>
  <tbody>
  {foreach $info.missing_deps as $name => $version}
    <tr>
      <td>{$name}</td>
      <td>{$version}</td>
    </tr>
  {/foreach}
  </tbody>
</table>
