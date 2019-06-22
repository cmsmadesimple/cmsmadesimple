{form_start action=rename selall=$selall}
<div class="c_full cf">
  <label for="newname" class="grid_3">{$mod->Lang('newname')}</label>
  <input id="newname" class="grid_8" type="text" name="{$actionid}newname" value="{$newname}" size="40"/>
</div>

<div class="c_full cf">
   <input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>
   <input type="submit" name="{$actionid}cancel" value="{$mod->Lang('cancel')}"/>
</div>
{form_end}
