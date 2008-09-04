{*TODO nuno*}
{*validation_errors for=$adminlog_object*}
{*end*}


{if $message}
<div class="message">
	<p>{$message}</p>
</div>
{/if}


<div class="pagecontainer">

	<div class="pageoverflow">
		{$header_name}
	</div><!-- pageoverflow -->
{if $have_result}  
	<p class="pageshowrows">{$page_string}</p>
    <a href="adminlog.php?download=1">{tr}download{/tr}</a>
		<table class="pagetable">
		<thead>
		<tr>
            <th>{tr}user{/tr}</th>
            <th>{tr}itemid{/tr}</th>
            <th>{tr}itemname{/tr}</th>
            <th>{tr}action{/tr}</th>
            <th>{tr}date{/tr}</th>
		
		</tr>
		</thead>
		<tbody>
     {section name=i loop=$username}
     
     {cycle values='row1,row2' assign='currow'} 
  <tr class="{$currow}">
				<td>{$username[i]}</td>
             <td>{$item_id[i]}   </td>
             <td>{$item_name[i]} </td>
             <td>{$action[i]}  </td>
			{* <td>{$dateformats[i]} </td>*}
              <td>{$date[i]}</td>
	</tr>
 {/section}
</tbody>
</table>

{if $access_result || $have_result }          
<div class="pageoptions">
	<p class="pageoptions">
	<a href="adminlog.php?clear=true">
    {adminicon icon='delete.gif' alt_lang='delete'}
</a>
	<a class="pageoptions" href="adminlog.php?clear=true">{tr}clearadminlog{/tr}</a>
	</p>
	</div>
{/if}

{else}
{* If we have no data!* }
<p>{tr}adminlogempty{/tr}</p>
{/if}

</div><!-- pagecontainer -->
<p class="pageback"><a class="pageback" href="{$back_url}">&#171; {lang string='back'}</a></p>
