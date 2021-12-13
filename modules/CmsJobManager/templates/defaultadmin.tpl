<script type="text/javascript">
$(document).ready(function(){
  $('#simple1').click(function(ev){
     ev.preventDefault();
     cms_confirm('woot it works');
  });
});
</script>

<div class="information">{$mod->Lang('info_background_jobs')}</div>

{if !count($jobs)}
  <div style="text-align: center;">
    <div class="information">{$mod->Lang('info_no_jobs')}</div>
  </div>
{else}
  <table class="pagetable">
    <thead>
      <tr>
        <th>{$mod->Lang('name')}</th>
        <th>{$mod->Lang('module')}</th>
        <th>{$mod->Lang('created')}</th>
        <th>{$mod->Lang('start')}</th>
        <th>{$mod->Lang('frequency')}</th>
        <th>{$mod->Lang('until')}</th>
        <th>{$mod->Lang('errors')}</th>
        <th class="pageicon"></th>
      </tr>
    </thead>
    <tbody>
    {foreach $jobs as $job}
      <tr class="{cycle values='row1,row2'}">
        <td>{$job->name}</td>
        <td>{$job->module|default:''}</td>
        <td>{$job->created|relative_time}</td>
        <td>
           {if $job->start < $smarty.now - $async_freq}<span style="color: red;">
               {elseif $job->start < $smarty.now + $async_freq}<span style="color: green;">
           {else}<span>
           {/if}
             {$job->start|relative_time}
           </span>
        </td>
        <td>{$recur_list[$job->frequency]}</td>
        <td>{if $job->until}{$job->until|localedate_format:'%x %X'}{/if}</td>
        <td>{if $job->errors > 0}<span style="color: red;">{$job->errors}</span>{/if}</td>
        <td></td>
      </tr>
    {/foreach}
    </tbody>
  </table>
{/if}

{* delete me before distributing - TODO ?? 

<a id="simple1" href="{cms_action_url action=test1}" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary">
<span class="ui-button-icon-primary ui-icon ui-icon-circle-check"></span>
<span class="ui-button-text">Simple Derived Class Test</span>
</a>

<a href="{cms_action_url action=test2}" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary">
<span class="ui-button-icon-primary ui-icon ui-icon-circle-check"></span>
<span class="ui-button-text">Simple Derived Cron Test</span>
</a>

*}
