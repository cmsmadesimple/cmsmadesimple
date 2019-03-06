<script>
$(function(){
   var cont = $('#cont_{$def->name}')
   cont.on('click','.cmsfp_clear',function(ev){
      $('img',cont).removeAttr('src');
   })
   cont.on('change','[name={$def->name}]',function(ev){
      var val = $(this).val();
      val = $('img',cont).data('base') + '/' + val;
      $('img',cont).attr('src',val);
   })
})
</script>
<div class="c_full cf" id="cont_{$def->name}">
   <label for="_{$def->name}" class="grid_3">{$def->label}</label>
   <div class="grid_8">
      {cms_filepicker name=$def->name type=image value=$value}
      {if $value}
         <img src="{uploads_url}/{$value}" height="200" data-base="{uploads_url}"/>
      {/if}
   </div>
</div>