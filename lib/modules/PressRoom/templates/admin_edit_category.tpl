<script>
var base = '{uploads_url}'
function do_category_thumb(el,src) {
   if( typeof el == 'undefined' || !el ) return;
   if( typeof src == 'undefined' || !src ) {
      el.hide().prop('src','')
   } else {
      el.prop('src',base + '/'+src).show()
   }
}
$(function(){
   var img = $('#category_img');
   do_category_thumb(img, '{$obj->image_url}')
   var input = $('input[name="image_url"]').change(function(){
       do_category_thumb(img ,$(this).val());
   })
})
</script>

{if $obj->id > 0}
    <h3>{$mod->Lang('edit_category')}</h3>
{else}
    <h3>{$mod->Lang('add_category')}</h3>
{/if}

{if empty($category_tree_list)}
    <div class="warning">{$mod->Lang('warn_nocategories')}</div>
{/if}

{form_start catid=$obj->id}
<div class="c_full cf">
   <label for="fld_name" class="grid_3">{$mod->Lang('lbl_name')}</label>
   <div class="grid_8">
      <input class="grid_12" name="name" value="{$obj->name}" required/>
   </div>
</div>
<div class="c_full cf">
   <label for="fld_alias" class="grid_3">{$mod->Lang('lbl_alias')}</label>
   <div class="grid_8">
      <input class="grid_12" name="alias" value="{$obj->alias}"/>
   </div>
</div>
<div class="c_full cf">
   <label for="fld_alias" class="grid_3">{$mod->Lang('lbl_image')}</label>
   <div class="grid_8">
      <img class="grid_4" id='category_img' width="200"/ id="category_img" style="display: none;"><br/>
      <div class="grid_12">
          {cms_filepicker type='image' name='image_url' value=$obj->image_url}
      </div>
   </div>
</div>

{if !empty($category_tree_list)}
<div class="c_full cf">
   <label for="fld_alias" class="grid_3">{$mod->Lang('lbl_parent')}</label>
   <div class="grid_8">
      <select name="parent" class="grid_12">
         {html_options options=$category_tree_list selected=$obj->parent_id}
      </select>
   </div>
</div>
{/if}

<div class="c_full cf">
   <label for="fld_alias" class="grid_3">{$mod->Lang('lbl_detailpage')}</label>
   <div class="grid_8">
      {page_selector name='detailpage' value=$obj->detailpage}
      {if $obj->detailpage < 1}
      <div class="grid_12">
          <label class="grid_3">{$mod->lang('lbl_effective_detailpage')}</label>
	  <div class="grid_8">{$effective_detailpage_str|default:$mod->Lang('none')}</div>
      </div>
      {/if}
   </div>
</div>

<div class="c_full cf">
   <input type="submit" name="submit" value="{$mod->Lang('submit')}"/>
   <input type="submit" name="cancel" value="{$mod->Lang('cancel')}" formnovalidate/>
</div>
{form_end}