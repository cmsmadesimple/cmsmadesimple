{*
#CMS - CMS Made Simple
#(c)2004-6 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$
*}
<script type="text/javascript">
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
