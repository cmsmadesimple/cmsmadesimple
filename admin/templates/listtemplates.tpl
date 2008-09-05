{validation_errors for=$foo_object}
{if $messages}
<div class="pagemcontainer">
	<div class="pagemessage"> <ul>{$messages}</ul></div>
</div>
{/if}

{if $error_msg}
<div class="error">
		<div class="pageerrorcontainer"><ul style="list-style:none" class="pageerror">{$error_msg}</ul></div>
	</div>
{/if}


<div class="pagecontainer">
	<div class="pageoverflow">
		{$header_name}
	</div><!-- pageoverflow -->
    {$pagination}
    
<form action="multitemplate.php" method="post">
{if count($templatelist) gt 0} 
	
		<table cellspacing="0" class="pagetable">
		<thead>
		<tr>
		<th class="pagew50">{tr}template{/tr}</th>
		<th class="pagepos">{tr}default{/tr}</th>
		<th class="pagepos">{tr}active{/tr}</th>
		{if $edit}
			<th class="pagepos">&nbsp;</th>
         
		<th class="pageicon">&nbsp;</th>
		{/if}
       {if $add}
       <th class="pageicon">&nbsp;</th>
       {/if}
		{if $remove}
			<th class="pageicon">&nbsp;</th>
            {/if}
		{if $all}
			<th class="pageicon">&nbsp;</th>
		<th class="pageicon">&nbsp;</th>
         {/if}
		</tr>
		</thead>
		<tbody>

	 {foreach from=$templatelist item=onetemplate name=onetemplate }
     {cycle values='row1,row2' assign='currow'} 

				            
            {if ($counter < $page*$limit && $counter >= ($page*$limit)-$limit)}
            
            
            
  			    <tr class="{$currow}" >
				<td><a href="edittemplate.php?template_id={$onetemplate->id}">{$onetemplate->name}</a></td>
				<td class="pagepos">
                 {if $onetemplate->default eq 1}
                 {adminicon icon='true.gif' alt_lang='yes'}
                 {else}
                 
                 <a href="listtemplates.php?setdefault={$onetemplate->id}">
                 {adminicon icon='false.gif' alt_lang='settrue'}
                 </a>
                 
                 {/if}
                    </td>
				{if $onetemplate->default}
					<td class="pagepos"> {adminicon icon='true.gif' alt_lang='true'}</td>
				{else}
					<td class="pagepos">
                  {if $onetemplate->active eq 1}
                  
                  <a href="listtemplates.php?setinactive={$onetemplate->id}">
                  {adminicon icon='true.gif' alt_lang='setfalse'}
                  </a>
                   {else}
                   
                   <a href="listtemplates.php?setactive={$onetemplate->id}">
                   {adminicon icon='false.gif' alt_lang='settrue'}</a>
	              {/if}
                 
                </td>
                 {/if}
				{* set template to all content *}
				{if $all}
					<td class="pagepos"><a href="listtemplates.php?action=setallcontent&amp;template_id={$onetemplate->id}" onclick="return confirm('{tr}setallcontentconfirm{/tr}');">{tr}setallcontent{/tr}</a></td>
                 
				{*  view css association *}
				<td class="icons_wide">
                <a href="assignstylesheets.php?type=template&amp;template_id={$onetemplate->id}">
                 {adminicon icon='css.gif' alt_lang='attachstylesheets'}</a>
                 </td>
                 {/if}
				{* add new template*}
				{if $add}
				
				 {* <td class="icons_wide">
                 <a href="copytemplate.php?template_id={$onetemplate->id}&amp;template_name={urlencode($onetemplate->name)}"></a>
                  *}
                  <td class="icons_wide">
                  <a href="copytemplate.php?template_id={$onetemplate->id}&amp;template_name={$onetemplate->name|escape:"url"}">
                     {adminicon icon='copy.gif' alt_lang='copy'}</a>
                     </td>
				{/if}

				{*  edit template*}
				{if $edit}
				
					<td class="icons_wide"><a href="edittemplate.php?template_id={$onetemplate->id}">
                     {adminicon icon='edit.gif' alt_lang='edit'} </a></td>
				{/if}

				{* remove template*}
				{if $remove}
				
					<td class="icons_wide">
					{if $onetemplate->default}
					
						&nbsp;
					
					{else}
					
						<a href="deletetemplate.php?template_id={$onetemplate->id}" onclick="return confirm('{tr}deleteconfirm{/tr}', {$onetemplate->name})');">
						 {adminicon icon='delete.gif' alt_lang='delete'}
                         </a>
					{/if}
					</td>
				{/if}
				{if $onetemplate->default}
					<td>&nbsp;</td>
				{else}
					<td><input type="checkbox" name="multitemplate-{$onetemplate->id}" /></td>
				{/if}
                
		 </tr>
	 {/if}
  {capture assign='junk'}{$counter++}{/capture}
   {/foreach}

		</tbody>
		</table>
<p class="pageshowrows">{$pagination}</p>
{/if}   
 
 
 
 {if $add}

	<div class="pageoptions">
		<p class="pageoptions">
			<span style="float: left;">
				<a href="addtemplate.php">{adminicon icon='newobject.gif' alt_lang='addtemplate'}</a>
						 <a class="pageoptions" href="addtemplate.php">{tr}addtemplate{/tr}</a>
			</span>
            
			<span style="margin-right: 30px; float: right; text-align: right">
				{tr}selecteditems{/tr}: 
                <select name="multiaction">
				<option value="delete">{tr}delete{/tr}</option>
				<option value="active">{tr}active{/tr}</option>
				<option value="inactive">{tr}inactive{/tr}</option>
				</select>
				<input type="submit" value="{tr}submit{/tr}" />
			</span>
			<br /><br />
		</p>	
       </div> 
  {/if}       
          
</form>
</div><!-- pagecontainer -->

<p class="pageback"><a class="pageback" href="{$back_url}">&#171; {lang string='back'}</a></p>
