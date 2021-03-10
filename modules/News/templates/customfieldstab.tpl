<script>
$(document).ready(function(){
  $('a.del_fielddef').click(function(ev){
    var self = $(this);
    ev.preventDefault();
    cms_confirm('{$mod->Lang('areyousure')}').done(function(){
       window.location = self.attr('href');
    })
  })
})
</script>

{if $itemcount > 0}
<table class="pagetable">
	<thead>
		<tr>
			<th>{$fielddeftext}</th>
			<th>{$typetext}</th>
			<th class="pageicon">&nbsp;</th>
			<th class="pageicon">&nbsp;</th>
			<th class="pageicon">&nbsp;</th>
			<th class="pageicon">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
{foreach from=$items item=entry}
	{cycle values="row1,row2" assign='rowclass'}
		<tr class="{$rowclass}">
			<td>{$entry->name}</td>
			<td>{$entry->type}</td>
			<td>{$entry->uplink}</td>
	                <td>{$entry->downlink}</td>
			<td>{$entry->editlink}</td>
			<td><a href="{$entry->delete_url}" class="del_fielddef">{admin_icon icon='delete.gif' alt=$mod->Lang('delete')}</a></td>
		</tr>
{/foreach}
	</tbody>
</table>
{/if}

<div class="pageoptions">
  <a href="{$addurl}" title="{$mod->Lang('addfielddef')}">{admin_icon icon='newobject.gif'} {$mod->Lang('addfielddef')}</a>
</div>