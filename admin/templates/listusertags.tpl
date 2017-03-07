<script type="text/javascript">
$(document).ready(function(){
   $('a.delusertag').click(function(ev){
      ev.preventDefault();
      var _hr = $(this).attr('href');
      cms_confirm('{lang('confirm_deleteusertag')|cms_escape:'javascript'}').done(function(){
         window.location.href = _hr;
      })
   })
})
</script>

<div class="pagecontainer">
    <div class="pageoptions">
       <a href="{$addurl}">{admin_icon icon='newobject.gif'} {lang('addusertag')}</a>
    </div>
</div>

{if $tags}
  <table class="pagetable">
     <thead>
       <tr>
         <th>{lang('name')}</th>
         <th>{lang('description')}</th>
	 <th class="pageicon"></th>
	 <th class="pageicon"></th>
       </tr>
     </thead>
     <tbody>
     {foreach $tags as $tag_id => $tag}
       {$edit_url="editusertag.php{$urlext}&amp;userplugin_id={$tag_id}"}
       <tr class="{cycle values='row1,row2'}">
          <td><a href="{$edit_url}" title="{lang('editusertag')}">{$tag.name}</a></td>
          <td>{$tag.description}</td>
	  <td>
	     <a href="{$edit_url}">{admin_icon icon='edit.gif' title=lang('editusertag')}</a>
	  </td>
	  <td>
	     <a class="delusertag" href="deleteuserplugin.php{$urlext}&amp;userplugin_id={$tag_id}">{admin_icon icon='delete.gif' title=lang('delete')}</a>
	  </td>
       </tr>
     {/foreach}
     </tbody>
  </table>
{/if}