
<div id="admin_group_warning" style="display:none">
{$admin_group_warning}
</div>

{if isset($message)}
<p class="pageheader">{$message}</p>
{/if}

{literal}
<script type="text/javascript">
	/* <![CDATA[ */
	var groupids = new Array({/literal}{$groupidlist}{literal});
			
	function set_group()
		{
		var gsel = document.getElementById('groupsel');
		if (gsel)
			{
			var gid = gsel[gsel.selectedIndex].value;
			var warn = document.getElementById('admin_group_warning');
			if (gid == 1)
				{
				warn.style.display='block';
				}
			else
				{
				warn.style.display='none';
				}
			
			if (gid == -1)
				{
				for (var i=0;i<groupids.length;i++)
					{
					if (groupids[i] != '')
						{
                  cell_class_toggle('g'+i,true);
						}
					}
				}
			else
				{
				for (var i=0;i<groupids.length;i++)
					{
					if (groupids[i] != '' && i == gid)
                  {
                  cell_class_toggle('g'+i,true);
						}
					else if (groupids[i] != '' && i != gid)
						{
						cell_class_toggle('g'+i,false);
						}
					}
				}
			}
		}

      function cell_class_toggle(css_class,show)
      {
         var ths =document.body.getElementsByTagName('th');
         for(var j=0; j<ths.length; j++)
            {
            if(ths[j].className==css_class)
               {
               ths[j].style.display = (show?'':'none');
               }
            }
         var tds =document.body.getElementsByTagName('td');
         for(var j=0; j<tds.length; j++)
            {
            if(tds[j].className==css_class)
               {
               tds[j].style.display = (show?'':'none');
               }
            }
      }

	/* ]]> */
</script>
{/literal}
<div class="pageoverflow">
  <div class="pagetext">{$selectgroup}:</div>
    <div class="pageinput">
    <form method="post" action="">
		<select id="groupsel" onchange="set_group()">
		{foreach from=$group_list item=thisgroup}
			<option value="{$thisgroup->id}">{$thisgroup->name}</option>
		{/foreach}
	</select>
</form>
 </div> 
</div>

{$form_start}
<div>
  <input type="hidden" name="{$cms_secure_param_name}" value="{$cms_user_key}" />
<table cellspacing="0" class="pagetable" id="permtable">
  <thead>
  <tr>
    <th>{$title_permission}</th>
	{foreach from=$group_list item=thisgroup}
		{if $thisgroup->id != -1}<th class="g{$thisgroup->id}">{$thisgroup->name}</th>{/if}
	{/foreach}
 </tr>
  </thead>
  <tbody>
  {foreach from=$perms item=perm}
    {cycle values='row1,row2' assign='currow'}
    <tr class="{$currow}" onmouseover="this.className='{$currow}.hover';" onmouseout="this.className='{$currow}';">
 		<td>{$perm->name}</td>
		{foreach from=$group_list item=thisgroup}
			{if $thisgroup->id != -1}
			{assign var="gid" value=`$thisgroup->id`}
			<td class="g{$thisgroup->id}"><input type="checkbox" name="pg_{$perm->id}_{$gid}" value="1"{if isset($perm->group[$gid]) || $gid == 1} checked="checked"{/if} {if $gid == 1} disabled="disabled"{/if} /></td>
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
