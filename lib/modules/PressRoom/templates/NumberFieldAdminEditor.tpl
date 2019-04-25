<div class="c_full cf">
   <label class="grid_3" for="_min">{$mod->Lang('lbl_min')}</label>
   <div class="grid_8">
     <input class="grid_2" type="number" id="_min" name="num_minval" value="{$def->getExtra('minval')}" step="0.001"/>
   </div>
</div>
<div class="c_full cf">
   <label class="grid_3" for="_max">{$mod->Lang('lbl_max')}</label>
   <div class="grid_8">
     <input class="grid_2" type="number" id="_max" name="num_maxval" value="{$def->getExtra('maxval')}" step="0.001"/>
   </div>
</div>
<div class="c_full cf">
   <label class="grid_3" for="_step">{$mod->Lang('lbl_step')}</label>
   <div class="grid_8">
     <input class="grid_2" type="number" id="_step" name="num_stepval" value="{$def->getExtra('stepval')}" step="0.001"/>
   </div>
</div>