<script>
$(function(){
   $('[name=page]').change(function(ev){
      $(this).closest('form').submit();
   })

   $('#selall').click(function(ev){
      if( $(this).is(':checked') ) {
         $('input.selbox').prop('checked',true);
      } else {
         $('input.selbox').prop('checked',false);
      }
      $('input.selbox').first().trigger('change');
   })

   $('input.selbox').change(function(ev){
      // count the checked ones
      var n = $('input.selbox:checked').length
      if( n > 0 ) {
         $('#bulkzone').show();
      } else {
         $('#bulkzone').hide();
      }
   })

   $('#bulksubmit').click(function(ev){
      ev.preventDefault();
      var items = [];
      $('input.selbox:checked').each( function( index, elem ) {
         items.push( $(elem).val() );
      })
      items = items.slice(0,500);
      $('#bulkitems').val( items.join(',') );
      $(this).closest('form').submit();
   })

   $('#filter_btn').click(function(){
      $('#filter_zone').toggle();
   })
})
</script>

<fieldset id="filter_zone" style="display: none;">
   <legend>{$mod->Lang('lbl_articlefilter')}</legend>
   {form_start}
   <div class="c_full cf">
       <label class="grid_3" for="filter_title">{$mod->Lang('lbl_title')}</label>
       <input class="grid_8" id="filter_title" name="filter_title" placeholder="{$mod->Lang('ph_filter_title')}" value="{$filter_opts.title_substr|default:''}"/>
   </div>
   <div class="c_full cf">
       <label class="grid_3" for="filter_category">{$mod->Lang('lbl_category')}</label>
       <select class="grid_8" id="filter_category" name="filter_category">
          {html_options options=$category_list selected=$filter_opts.category_id|default:0}
       </select>
   </div>
   <div class="c_full cf">
       <label class="grid_3" for="filter_categorychildren">{$mod->Lang('lbl_categorychildren')}</label>
       <select class="grid_2" id="filter_categorychildren" name="filter_categorychildren">
          {cms_yesno selected=$filter_opts.withchildren|default:false}
       </select>
   </div>
   <div class="c_full cf">
       <label class="grid_3" for="filter_status">{$mod->Lang('lbl_status')}</label>
       <select class="grid_8" id="filter_status" name="filter_status">
          {html_options options=$filter_status_list selected=$filter_opts.status|default:''}
       </select>
   </div>
   <div class="c_full cf">
       <label class="grid_3" for="filter_useperiod">{$mod->Lang('lbl_usedates')}</label>
       <select class="grid_8" id="filter_useperiod" name="filter_useperiod">
          {html_options options=$filter_periods_list selected=$filter_opts.useperiod|default:1}
       </select>
   </div>
   <hr/>
   <div class="c_full cf">
       <label class="grid_3" for="filter_limit">{$mod->Lang('lbl_pagelimit')}</label>
       {$opts=[5=>5,10=>10,25=>25,50=>50,100=>100,250=>250,500=>500]}
       <select class="grid_8" id="filter_limit" name="filter_limit">
          {html_options options=$opts selected=$filter_opts.limit}
       </select>
   </div>
   <div class="c_full cf">
       <label class="grid_3" for="filter_sortby">{$mod->Lang('lbl_sortby')}</label>
       <select class="grid_8" id="filter_sortby" name="filter_sortby">
          {html_options options=$sort_list selected=$filter_opts.sortby|default:''}
       </select>
   </div>
   <div class="c_full cf">
       <label class="grid_3" for="filter_sortorder">{$mod->Lang('lbl_sortorder')}</label>
       <select class="grid_8" id="filter_sortorder" name="filter_sortorder">
          {html_options options=$sortorder_list selected=$filter_opts.sortorder|default:'DESC'}
       </select>
   </div>
   <div class="c_full cf">
      <input type="submit" name="filter_submit" value="{$mod->Lang('submit')}"/>
      <input type="submit" name="filter_reset" value="{$mod->Lang('reset')}"/>
   </div>
   {form_end}
</fieldset>

<div class="c_full cf">
    {if $mod->canAddArticle()}
    <a href="{cms_action_url action=admin_edit_article}" title="{$mod->Lang('t_add_article')}">{admin_icon icon=newobject} {$mod->Lang('add_article')}</a>
    &nbsp;
    {/if}
    <a id="filter_btn">{admin_icon icon=filter} {$mod->Lang('view_filter')} <em id="filterinuse">{if $filter_applied}({$mod->Lang('applied')}){/if}</em></a>

    {if $articles->pagecount > 1}
       <div style="float: right;">
          {form_start}
            {$list=$articles->pageList()}
  	    <label>{$mod->Lang('lbl_page')}
            <select name="page">
	      {html_options values=$list output=$list selected=$articles->page}
	    </select>
	    </label>
	  {form_end}
       </div>
    {/if}
</div>

{if !count($articles)}
    <div class="information">{$mod->Lang('warn_noarticles')}</div>
{else}
    <table class="pagetable">
       <thead>
          <tr>
	     <th>{$mod->Lang('lbl_id')}</th>
	     <th>{$mod->Lang('lbl_title')}</th>
	     <th>{$mod->Lang('lbl_postdate')}</th>
	     <th>{$mod->Lang('lbl_starttime')}</th>
	     <th>{$mod->Lang('lbl_endtime')}</th>
	     <th>{$mod->Lang('lbl_category')}</th>
	     <th>{$mod->Lang('lbl_status')}</th>
	     <th>{$mod->Lang('lbl_info')}</th>
	     <th class="pageicon"></th>
	     <th class="pageicon"></th>
	     <th class="pageicon">
	         <input type="checkbox" name="selall" id="selall" value="----"/>
	     </th>
	  </tr>
       </thead>
       <tbody>
          {foreach $articles as $art}
	     {if $metadata[$art->id]['canedit']}
  	        {cms_action_url action=admin_edit_article news_id=$art->id assign=edit_url}
	     {elseif $metadata[$art->id]['canview']}
  	        {cms_action_url action=admin_view_article news_id=$art->id assign=edit_url}
	     {else}
	        {$edit_url=''}
	     {/if}
	     <tr class="{cycle values='row1,row2'}">
	        <td>
		   {if $edit_url}
		       <a href="{$edit_url}" title="{$mod->Lang('t_edit_article')}">{$art->id}</a>
		   {else}
		       {$art->id}
		   {/if}
		</td>
	        <td>
		   {if $edit_url}
		       <a href="{$edit_url}" title="{$mod->Lang('t_edit_article')}">{$art->title|summarize}</a>
		   {else}
		       {$art->title|summarize}
		   {/if}
		</td>
		<td>{$art->news_date|cms_date_format}</td>
		<td>{if $art->start_time}{$art->start_time|cms_date_format}{/if}</td>
		<td>{if $art->end_time}{$art->end_time|cms_date_format}{/if}</td>
		<td>{pressroom_category_name cat=$art->category_id}</td>
		<td>
		    {if $art->status == $art::STATUS_DISABLED}
		        <span style="color: red;">{$status_list[$art->status]|ucwords}</span>
		    {elseif $art->status == $art::STATUS_NEEDSAPPROVAL}
		        <span style="color: orange;">{$status_list[$art->status]|ucwords}</span>
		    {elseif $art->status == $art::STATUS_DRAFT}
		        <strong>{$status_list[$art->status]|ucwords}</strong>
		    {else}
		        {$status_list[$art->status]|ucwords}
		    {/if}
		</td>
		<td>
		    {if $art->end_time > 0 && $art->end_time < time()}
		        <span style="color: red;">{$mod->Lang('lbl_expired')}</span>
		    {elseif $art->start_time > 0 && $art->start_time > time()}
		        <span style="color: orange;">{$mod->Lang('lbl_notstarted')}</span>
		    {/if}
		</td>
		<td>
		    <a href="{$edit_url}" title="{$mod->Lang('t_edit_article')}">
		       {if $metadata[$art->id]['canedit']}
		           {admin_icon icon=edit}
		       {elseif $metadata[$art->id]['canview']}
		           {admin_icon icon=view}
		       {/if}
		    </a>
		</td>
		<td>
		    {if $metadata[$art->id]['candelete']}
		        <a href="{cms_action_url action=admin_del_article news_id=$art->id}" title="{$mod->Lang('t_del_article')}">
			   {admin_icon icon=delete}
			</a>
		    {/if}
		</td>
		<td><input type="checkbox" class="selbox" value="{$art->id}"</td>
	     </tr>
	  {/foreach}
       </tbody>
    </table>

    <div class="c_full cf">
        {if $mod->canAddArticle()}
    	    <a href="{cms_action_url action=admin_edit_article}" title="{$mod->Lang('t_add_article')}">{admin_icon icon=newobject} {$mod->Lang('add_article')}</a>
    	{/if}

    	<div id="bulkzone" style="float: right; display: none;">
             {form_start action=admin_dobulk}
             <label>{$mod->Lang('lbl_withselected')}:
	        <select name="bulk_action">
	            {html_options options=$bulk_list}
	        </select>
	        <input type="submit" value="{$mod->Lang('submit')}" id="bulksubmit"/>
	        <input type="hidden" name="bulk_items" id="bulkitems"/>
	     </label>
	     {form_end}
        </div>
    </div>
{/if}
