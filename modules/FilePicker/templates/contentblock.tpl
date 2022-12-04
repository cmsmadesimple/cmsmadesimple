<div class="cmsfp_cont">
  {* the instance is important, it uniquely identifies this field, and will tie it to the proper popup *}
  <input type="text" name="{$blockName}" value="{$value}" data-cmsfp-instance="{$instance}" size="80"/>
  <script type="text/javascript">
$(document).ready(function(){
   var sel = '[data-cmsfp-instance="{$instance}"]';
   $(sel).filepicker({
      param_sig: '{$sig}',
      title: '{$title}',
      required: {if $required}1{else}0{/if},
      remove_title: '{$mod->Lang('clear')}',
      remove_label: '{$mod->Lang('clear')}',
   });
})
</script>
</div>{* .cmsfp *}