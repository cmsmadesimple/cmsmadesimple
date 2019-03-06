<h3>{$mod->Lang('add_category')}</h3>

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
      {if $obj->image_url}
          <img class="grid_4" src="{uploads_url}/{$obj->image_url}" width="200"/><br/>
      {/if}
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
   <input type="submit" name="submit" value="{$mod->Lang('submit')}"/>
   <input type="submit" name="cancel" value="{$mod->Lang('cancel')}" formnovalidate/>
</div>
{form_end}