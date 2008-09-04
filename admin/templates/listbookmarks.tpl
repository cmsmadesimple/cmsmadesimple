<div class="pagecontainer">
	<div class="pageoverflow">
	
     		{$header_name}
	</div><!-- pageoverflow -->
 

{if count($marklist) gt 0} 
<p class="pageshowrows">{$pagination}</p>
<table class="pagetable">
		<thead>
		<tr>
		<th class="pagew60">{tr}name{/tr}</th>
		<th class="pagepos">{tr}url{/tr}</th>
		<th class="pageicon">&nbsp;</th>
		<th class="pageicon">&nbsp;</th>
		</tr>
		</thead>
		<tbody>
      {foreach from=$marklist item=onemark name=onemark }
     {cycle values='row1,row2' assign='currow'} 
        
      {if ($counter < $page*$limit && $counter >= ($page*$limit)-$limit)}


       <tr class="{$currow}">
				<td><a href="editbookmark.php?bookmark_id={$onemark->id}">{$onemark->title}</a></td>
				<td>{$onemark->url}</td>
				<td><a href="editbookmark.php?bookmark_id={$onemark->id}">
                {adminicon icon='edit.gif' alt_lang='edit'}
                 </a></td>
				<td>
                <a href="deletebookmark.php?bookmark_id={$onemark->id}" onclick="return confirm('{tr}deleteconfirm{/tr} - {$onemark->title} - ?');">
                {adminicon icon='delete.gif' alt_lang='delete'}
                </a></td>
				</tr>
				 
		 {/if}
          {capture assign='junk'}{$counter++}{/capture}
			
            {/foreach}

		</tbody>
		</table>
<p class="pageshowrows">{$pagination}</p>

{/if}   		

<div class="pageoptions">
		<p class="pageoptions">
			<a title="{tr}true{/tr}" href="addbookmark.php">
				{adminicon icon='newobject.gif' alt_lang='addbookmark'}
					</a>
                    <a title="{tr}addbookmark{/tr}" class="pageoptions" href="addbookmark.php">{tr}addbookmark{/tr}</a>
		</p>
	</div>


</div><!-- pagecontainer -->

<p class="pageback"><a class="pageback" href="{$back_url}">&#171; {lang string='back'}</a></p>
