<div class="information">{$mod->Lang('info_background_jobs')}</div>

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
  {if count($jobs)}
    {foreach $jobs as $job}
      <tr class="{cycle values='row1,row2'}">
        <td>{$job->name}</td>
        <td>{$job->module|default:''}</td>
        <td>{$job->created|relative_time}</td>
        <td>
          {if $job->start < $smarty.now - $async_freq}
            <span style="color: red;">
		      {elseif $job->start < $smarty.now + $async_freq}
            <span style="color: green;">
          {else}
            <span>
          {/if}
          {$job->start|relative_time}
          </span>
        </td>
        <td>{$recur_list[$job->frequency]}</td>
        <td>{if $job->until}{$job->until|date_format:'%x %X'}{/if}</td>
        <td>{if $job->errors > 0}<span style="color: red;">{$job->errors}</span>{/if}</td>
        <td></td>
      </tr>
    {/foreach}
  {/if}
  </tbody>
</table>

{if !count($jobs)}<div class="information">{$mod->Lang('info_no_jobs')}</div>{/if}