<style>
#endtime_cont {
   display: none;
}
</style>

<script>
$(function(){
   $('[name=use_endtime]').change(function(ev) {
      var val = $(this).val();
      if( val == 1 ) {
         $('#endtime_cont').show();
      } else {
         $('#endtime_cont').hide();
      }
   }).trigger('change')
})
</script>

<h3>{$mod->Lang('add_article')}</h3>

{form_start news_id=$article->id}

<div class="c_full cf">
  <input type="submit" name="submit" value="{$mod->Lang('submit')}"/>
  {if $article->id > 0}
  <input type="submit" name="apply" value="{$mod->Lang('apply')}"/>
  {/if}
  <input type="submit" name="cancel" value="{$mod->Lang('cancel')}"/>
</div>

{tab_header name='content' label=$mod->Lang('tab_content')}
{if !empty($fielddef_list)}
  {tab_header name='fields' label=$mod->Lang('tab_fields')}
{/if}
{tab_header name='more' label=$mod->Lang('tab_more')}

{tab_start name='content'}
<div class="c_full cf">
  <label for="fld_title" class="grid_3">{$mod->Lang('lbl_title')}*</label>
  <input id="fld_title" class="grid_8 required" name="title" value="{$article->title}" required/>
</div>

{if $settings->editor_summary_enabled}
<div class="c_full cf">
  <label for="fld_summary" class="grid_3">{$mod->Lang('lbl_summary')}</label>
  <div class="grid_8">
      {cms_textarea name=summary enablewysiwyg=$settings->editor_summary_wysiwyg id="fld_summary" rows="5" value=$article->summary}
  </div>
</div>
{/if}

<div class="c_full cf">
  <label for="fld_content" class="grid_3">{$mod->Lang('lbl_content')}*</label>
  <div class="grid_8">
      {cms_textarea name=content enablewysiwyg=1 id="fld_content" rows="10" value=$article->content}
  </div>
</div>

{if !empty($category_tree_list)}
<div class="c_full cf">
  <label for="fld_category_id" class="grid_3">{$mod->Lang('lbl_category')}</label>
  <select id="fld_category_id" class="grid_8" name="category_id">
     {html_options options=$category_tree_list selected=$article->category_id}
  </select>
</div>
{/if}

{if !empty($fielddef_list)}
  {tab_start name=fields}
  {include file='module_file_tpl:News2;admin_edit_article_fields.tpl'}
{/if}

{tab_start name='more'}
<div class="c_full cf">
   <label class="grid_3">{$mod->Lang('lbl_newsdate')}</label>
   <div class="grid_8">
       {html_select_date prefix=newsdate_ start_year='1970' end_year='+20' time=$article->news_date}
       @ {html_select_time prefix=newsdate_ time=$article->start_time display_seconds=false}
   </div>
</div>
<div class="c_full cf">
   <label class="grid_3">{$mod->Lang('lbl_autohide')}</label>
   {$val=0}{if $article->start_time > 0 || $article->end_time > 0}{$val=1}{/if}
   <select class="grid_2" name="use_endtime">
      {cms_yesno selected=$val}
   </select>
</div>
<div id="endtime_cont">
   <div class="c_full cf">
      <label class="grid_3">{$mod->Lang('lbl_starttime')}</label>
      <div class="grid_8">
         {html_select_date prefix=starttime_ start_year='1970' end_year='+10' time=$article->start_time}
         @ {html_select_time prefix=starttime_ time=$article->start_time display_seconds=false}
      </div>
   </div>
   <div class="c_full cf">
      <label class="grid_3">{$mod->Lang('lbl_endtime')}</label>
      <div class="grid_8">
         {$endtime=$article->end_time}
         {html_select_date prefix=endtime_ start_year='1970' end_year='+10' time=$endtime end_year='+20'}
         @ {html_select_time prefix=endtime_ time=$article->end_time display_seconds=false}
      </div>
   </div>
</div>

<hr/>
<div class="c_full cf">
   <label class="grid_3">{$mod->Lang('lbl_status')}</label>
   <select class="grid_8" name="status">
      {html_options options=$status_list selected=$article->status}
   </select>
</div>
<div class="c_full cf">
   <label class="grid_3">{$mod->Lang('lbl_urlslug')}</label>
   <input class="grid_8" name="url_slug" value="{$article->url_slug}" {if $settings->editor_urlslug_required}required{/if}/>
</div>
<div class="c_full cf">
   <label class="grid_3">{$mod->Lang('lbl_searchable')}</label>
   <select class="grid_2" name="searchable">
      {cms_yesno selected=$article->searchable}
   </select>
</div>

{tab_end}
{form_end}