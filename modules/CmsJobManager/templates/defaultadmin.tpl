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
        <th>{$mod->Lang('frequency')}</th>
        <th>{$mod->Lang('created')}</th>
        <th>{$mod->Lang('start')}</th>
        <th>{$mod->Lang('errors')}</th>
	<th class="pageicon"></th>
      </tr>
    </thead>
    <tbody>
    {foreach $jobs as $job}
      <tr class="{cycle values='row1,row2'}">
        <td>{$job->name}</td>
	<td>{$job->module}</td>
	<td>{$job->frequency}</td>
	<td>{$job->created|relative_time}</td>
	<td>{$job->start|relative_time}</td>
	<td>{$job->errors}</td>
	<td></td>
      </tr>
    {/foreach}
    </tbody>
  </table>
{/if}

{* delete me before distributing *}
<a href="{cms_action_url action=test1}">Simple Derived Class Test</a><br/>
<a href="{cms_action_url action=test2}">Simple Derived Cron Test</a>
