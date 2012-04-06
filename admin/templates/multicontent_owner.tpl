<div class="pagecontainer">
   <h3>{$text_changeowner}</h3>
       
   <form method="post" action="{$formurl}">
   <div>
      <input type="hidden" name="multiaction" value="dochangeowner" />
      <input type="hidden" name="idlist" value="{":"|implode:$idlist}" />
   </div>

   <div class="pageoverflow">
     <p class="pagetext">{$text_pages}:</p>
     <p class="pageinput">
        {foreach from=$pages item='onepage'}
           {$onepage.name} ({$onepage.hierarchy})<br/>
        {/foreach}
     </p>
   </div>

   <div class="pageoverflow">
     <p class="pagetext">{$text_owner}</p>
     <p class="pageinput">{$input_changeowner}</p>
   </div>

   <div class="pageoverflow">
     <p class="pagetext">&nbsp;</p>
     <p class="pageinput"><input type="submit" name="submit" value="{$text_submit}" /><input type="submit" name="cancel" value="{$text_cancel}" /></p>
   </div>
   </form>
</div>