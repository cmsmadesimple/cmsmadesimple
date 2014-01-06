<script type="text/javascript">
$(document).ready(function(){
  $('a.mod_upgrade').click(function(){
    return confirm('{$mod->Lang('confirm_upgrade')}');
  });
  $('a.mod_remove').click(function(){
    return confirm('{$mod->Lang('confirm_remove')}');
  });
  $('a.mod_chmod').click(function(){
    return confirm('{$mod->Lang('confirm_chmod')}');
  });
  $('a.mod_uninstall').click(function(){
    return confirm('{$mod->Lang('confirm_uninstall')}');
  });

  $('#importbtn').click(function(){
    $('#importdlg').dialog({
      modal: true,
      buttons: {
        {$mod->Lang('submit')}: function() {
          var file = $('#xml_upload').val();
          if( file.length == 0 ) {
            alert('{$mod->Lang('error_nofileuploaded')}');
            return;
          }
          var ext  = file.split('.').pop().toLowerCase();
          if($.inArray(ext, ['xml','cmsmod']) == -1) {
            alert('{$mod->Lang('error_invaliduploadtype')}');
            return;
          }
          $(this).dialog('close');
          $('#local_import').submit();
        },
        {$mod->Lang('cancel')}: function() {
          $(this).dialog('close');
        }
      }
    });
  });
});
</script>

<div id="importdlg" title="{$mod->Lang('importxml')}" style="display: none;">
  {form_start id='local_import' action='local_import'}
  <div class="pageoverflow">
    <p class="pagetext"><label for="xml_upload">{$mod->Lang('uploadfile')}:</label></p>
    <p class="pageinput">
      <input id="xml_upload" type="file" name="{$actionid}upload" accept="text/xml"/>
    </p>
  </div>
  {form_end}
</div>

{if isset($module_info)}
<div class="pageoptions">
  <a id="importbtn">{admin_icon icon='import.gif'} {$mod->Lang('importxml')}</a>
</div>

<table class="pagetable">
  <thead>
    <tr>
      <th></th>
      <th>{$mod->Lang('nametext')}</th>
      <th><span title="{$mod->Lang('title_moduleversion')}">{$mod->Lang('vertext')}</span></th>
      <th><span title="{$mod->Lang('title_modulestatus')}">{$mod->Lang('status')}</span></th>
      <th><span title="{$mod->Lang('title_moduleaction')}">{$mod->Lang('action')}</span></th>
      <th class="pageicon"><span title="{$mod->Lang('title_moduleactive')}">{$mod->Lang('active')}</span></th>
      <th class="pageicon"><span title="{$mod->Lang('title_modulehelp')}">{$mod->Lang('helptxt')}</span></th>
      <th class="pageicon"><span title="{$mod->Lang('title_moduleabout')}">{$mod->Lang('abouttxt')}</span></th>
      {if $allow_export}<th class="pageicon"><span title="{$mod->Lang('title_moduleexport')}">{$mod->Lang('export')}</span></th>{/if}
    </tr>
  </thead>
  <tbody>
    {foreach $module_info as $item}
    {cycle values="row1,row2" assign='rowclass'}
    <tr class="{$rowclass}">
      <td>{if $item.system_module}{$system_img}{/if}
           {if $item.e_status == 'newer_available'}{$star_img}{/if}
      </td>
      <td>
          {if !$item.installed}
            <span title="{$item.description}" class="important">{$item.name}</span>
          {else}
            <span title="{$item.description}" {if $item.system_module} style="color: blue;"{/if}>{$item.name}</span>
          {/if}
      </td>
      <td>{if $item.e_status == 'newer_available'}
            <strong title="{$mod->Lang('status_newer_available')}">{$item.installed_version}</strong>
          {else}
            {$item.installed_version}
          {/if}
      </td>
      <td>{* status column *}
          {$ops=[]}
          {if !$item.installed}
            {if $item.can_install}
              {capture assign='op'}<strong title="{$mod->Lang('title_notinstalled')}">{$mod->Lang('notinstalled')}</strong>{/capture}{$ops[]=$op}
	    {else if $item.missing_deps}
              {capture assign='op'}<a class="modop mod_missingdeps important" style="color: red;" title="{$mod->Lang('title_missingdeps')}" href="{cms_action_url action='local_missingdeps' mod=$item.name}">{$mod->Lang('missingdeps')}</a>{/capture}{$ops[]=$op}
            {/if}
          {else}
            {capture assign='op'}{$tmp='status_'|cat:$item.status}<span title="{$mod->Lang($tmp)}">{$mod->Lang($item.status)}</span>{/capture}{$ops[]=$op}
          {/if}
          {if isset($item.e_status)}
            {capture assign='op'}{$tmp='status_'|cat:$item.e_status}<span {if $item.e_status == 'db_newer'}class="important"{/if} title="{$mod->Lang($tmp)}">{$mod->Lang($item.e_status)}</span>{/capture}{$ops[]=$op}
          {/if}
	  {if !$item.ver_compatible}
            {capture assign='op'}<span class="important" title="{$mod->Lang('title_notcompatible')}">{$mod->Lang('notcompatible')}</span>{/capture}{$ops[]=$op}
          {/if}
          {if !$item.writable}
            {capture assign='op'}<span title="{$mod->Lang('title_cantremove')}">{$mod->Lang('cantremove')}</span>{/capture}{$ops[]=$op}
          {/if}
          {if isset($item.dependants)}
            {capture assign='op'}<span title="{$mod->Lang('title_has_dependants')}">{$mod->Lang('has_dependants')}</span>: (<strong>{implode(',',$item.dependants)}</strong>){/capture}{$ops[]=$op}
          {/if}
          {'<br/>'|implode:$ops}
      </td>
      <td>
        {$ops=[]}
        {if !$item.installed}
          {if $item.can_install}
            {capture assign='op'}<a class="modop mod_install" href="{cms_action_url action='local_install' mod=$item.name}" title="{$mod->Lang('title_install')}">{$mod->Lang('install')}</a>{/capture}{$ops[]=$op}
          {/if}
          {if $item.writable}
            {capture assign='op'}<a class="modop mod_remove" href="{cms_action_url action='local_remove' mod=$item.name}" title="{$mod->Lang('title_remove')}">{$mod->Lang('remove')}</a>{/capture}{$ops[]=$op}
          {else}
            {capture assign='op'}<a class="modop mod_chmod" href="{cms_action_url action='local_chmod' mod=$item.name}" title="{$mod->Lang('title_chmod')}">{$mod->Lang('changeperms')}</a>{/capture}{$ops[]=$op}
          {/if}
        {else}
	  {if !isset($item.dependants) & $item.can_uninstall}
            {if $item.name != 'ModuleManager' || $allow_modman_uninstall}
              {capture assign='op'}<a class="modop mod_uninstall" href="{cms_action_url action='local_uninstall' mod=$item.name}" title="{$mod->Lang('title_uninstall')}">{$mod->Lang('uninstall')}</a>{/capture}{$ops[]=$op}
	    {/if}
	  {/if}
          {if $item.e_status == 'need_upgrade' && $item.can_upgrade}
            {capture assign='op'}<a class="modop mod_upgrade" href="{cms_action_url action='local_upgrade' mod=$item.name}" title="{$mod->Lang('title_upgrade')}">{$mod->Lang('upgrade')}</a>{/capture}{$ops[]=$op}
          {/if}
        {/if}
        {'<br/>'|implode:$ops}
      </td>
      <td>
	{* active column *}
        {if !isset($item.dependants) && $item.can_deactivate}
          {if $item.active}
            <a class="modop mod_inactive" href="{cms_action_url action='local_active' mod=$item.name state=0}" title="{$mod->Lang('toggle_inactive')}">{admin_icon icon='true.gif'}</a>
          {else}
            <a class="modop mod_active" href="{cms_action_url action='local_active' mod=$item.name state=1}" title="{$mod->Lang('toggle_active')}">{admin_icon icon='false.gif'}</a>
          {/if}
        {/if}
      </td>
      <td>
        <a class="modop mod_help" href="{cms_action_url action='local_help' mod=$item.name}" title="{$mod->Lang('title_modulehelp')}">{$mod->Lang('helptxt')}</a>
      </td>
      <td>
        <a class="modop mod_about" href="{cms_action_url action='local_about' mod=$item.name}" title="{$mod->Lang('title_moduleabout')}">{$mod->Lang('abouttxt')}</a>
      </td>
      {if $allow_export}<td>
        {if $item.active && $item.writable}
          <a class="modop mod_export" href="{cms_action_url action='local_export' mod=$item.name}" title="{$mod->Lang('title_moduleexport')}">{admin_icon icon='xml_rss.gif'}</a>
        {/if}
      </td>{/if}
    </tr>
    {/foreach}
  </tbody>  
</table>
{else}
  <div class="warning">{$mod->Lang('error_nomodules')}</div>
{/if}