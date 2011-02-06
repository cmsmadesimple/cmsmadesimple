<form action="{$smarty.server.REQUEST_URI}" method="post">
{foreach from=$display_info key='name' item='type'}
<div class="pageoverflow">
  <p class="pagetext">{$display_prompts.$name}: <span style="color: blue;">{$display_data.$name}</span></p>
</div>
{/foreach}
<div class="pageoverflow">
  <p class="pagetext"></p>
  <p class="pageinput">
    <input type="submit" name="cancel" value="{'cancel'|lang}"/>
    <input type="submit" name="steal" value="{'steal'|lang}"/>
  </p>
</div>
</form>