<div class="c_full cf">
   <label for="_{$def->name}" class="grid_3">{$def->label}</label>
   <select class="grid_8" id="_{$def->name}" name="{$def->name}">
      {html_options options=$options selected=$value}
   </select>
</div>