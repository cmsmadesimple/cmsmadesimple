{*
<div id="admin_group_warning" style="display:none">
{$admin_group_warning}
</div>
*}

{if isset($message)}
<p class="pagemessage">{$message}</p>
{/if}

<div class="pageoverflow">
<form method="post" action="{$filter_action}">
<div class="hidden">
  <input type="hidden" name="{$cms_secure_param_name}" value="{$cms_user_key}" />
</div>
	<b>{$selectgroup}:</b>&nbsp;
        <select name="groupsel" id="groupsel">
	{foreach from=$allgroups item=thisgroup}
           {if $thisgroup->id == $disp_group}
                <option value="{$thisgroup->id}" selected="selected">{$thisgroup->name}</option>
           {else}
		<option value="{$thisgroup->id}">{$thisgroup->name}</option>
           {/if}
	{/foreach}
	</select>&nbsp;
        <input type="submit" name="filter" value="{$apply}"/>
</form>
</div><br />

{$form_start}
<div class="hidden">
  <input type="hidden" name="{$cms_secure_param_name}" value="{$cms_user_key}" />
</div>
<table cellspacing="0" class="pagetable" id="permtable">
  <thead>
  <tr>
    <th>{if isset($title_group)}{$title_group}{/if}</th>
	{foreach from=$group_list item=thisgroup}
		{if $thisgroup->id != -1}<th class="g{$thisgroup->id}">{$thisgroup->name}</th>{/if}
	{/foreach}
 </tr>
  </thead>
  <tbody>
  {foreach from=$users item=user}
    {cycle values='row1,row2' assign='currow'}
    <tr class="{$currow}" onmouseover="this.className='{$currow}hover';" onmouseout="this.className='{$currow}';">
 		<td>{$user->name}</td>
		{foreach from=$group_list item=thisgroup}
                    {if $user->id == $user_id}
    		      {if $thisgroup->id != -1}
                        <td class="g{$thisgroup->id}">--</td>
                      {/if}
                    {else}
			{if $thisgroup->id != -1}
                          {if ($thisgroup->id == 1 && $user->id == 1)}
  			    <td class="g{$thisgroup->id}">&nbsp;</td>
                          {else}
			    {assign var="gid" value=`$thisgroup->id`}
			    <td class="g{$thisgroup->id}">
                              <input type="checkbox" name="ug_{$user->id}_{$gid}" value="1"{if isset($user->group[$gid])} checked="checked"{/if}  />
                            </td>
			  {/if}
                        {/if}
                     {/if}
		{/foreach}
    </tr>
  {/foreach}
  </tbody>
</table>

<div class="pageoverflow">
  <p class="pageoptions">
    {$hidden}
    {$submit} {$cancel} 
  </p>
</div>
</form>
