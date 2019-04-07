<style>
#fldtype_options {
   display: none;
}
</style>

<script>
$(function(){
   $('#fldtype').change((ev)=> {
      var type = $('#fldtype').val();
      if( type == 'select' || type == 'multiselect' ) {
         $('#fldtype_options').show();
      } else {
         $('#fldtype_options').hide();
      }
   }).trigger('change');

})
</script>

<h3>{$mod->Lang('add_fielddef')}: {$type->getName()}</h3>

{form_start fdid=$obj->id fldtype=$obj->type}
<div class="c_full cf">
    <label class="grid_3" for="fldname">{$mod->Lang('lbl_name')}</label>
    <div class="grid_8">
        <input class="grid_12" id="fldname" name="name" value="{$obj->name}" required/>
        <div class="grid_12">{$mod->Lang('info_fldname')}</div>
    </div>
</div>
<div class="c_full cf">
    <label class="grid_3" for="fllabel">{$mod->Lang('lbl_label')}</label>
    <div class="grid_8">
        <input class="grid_12" id="fldlabel" name="label" value="{$obj->raw_label}"/>
    </div>
</div>

{$type->renderForEditor($obj)}

<div class="c_full cf">
    <input type="submit" name="submit" value="{$mod->Lang('submit')}"/>
    <input type="submit" name="cancel" value="{$mod->Lang('cancel')}" formnovalidate/>
</div>
{form_end}