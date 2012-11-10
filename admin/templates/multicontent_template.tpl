<div class="pagecontainer">
   <h3>{$text_settemplate}</h3>
       
   <form method="post" action="{$formurl}">
   <div>
      <input type="hidden" name="multiaction" value="dosettemplate" />
      <input type="hidden" name="idlist" value="{":"|implode:$idlist}" />
   </div>

   <div class="pagewarning">{lang('warn_bulk_settemplate')}</div>

   <div class="pageoverflow">
     <p class="pagetext">{$text_pages}:</p>
     <p class="pageinput">
        {foreach from=$pages item='onepage'}
           {$onepage.name} ({$onepage.hierarchy})<br/>
        {/foreach}
     </p>
   </div>

   <div class="pageoverflow">
     <p class="pagetext">{$text_template}</p>
     <p class="pageinput">{$input_template}</p>
   </div>

   <div class="pageoverflow">
     <p class="pagetext">{lang('confirm_set_template_1')}</p>
     <p class-"pageinput">
       <input type="checkbox" name="check1" value="1" id="check1">&nbsp;<label for="check1">{lang('confirm_set_template_2')}</label><br/>
       <input type="checkbox" name="check1" value="1" id="check2">&nbsp;<label for="check2">{lang('confirm_set_template_3')}</label>
     </p>
   </div>

   <div class="pageoverflow">
     <p class="pagetext">&nbsp;</p>
     <p class="pageinput"><input type="submit" name="submit" value="{$text_submit}" /><input type="submit" name="cancel" value="{$text_cancel}" /></p>
   </div>
   </form>
</div>