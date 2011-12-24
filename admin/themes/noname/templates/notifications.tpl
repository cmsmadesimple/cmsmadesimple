{* notifications *}
{if count($items)}{strip}
<script type="text/javascript">{literal}
$('#clicknotifications').live('click',function(e){
  e.preventDefault();
  $('.notification').toggle(); 
});
{/literal}</script>
<div id="notifications">
  <span>You have: {$items|@count} unhandled notifications:&nbsp;<span id="clicknotifications">&raquo;</span>
  {foreach from=$items item='one'}
  <div class="notification" style="display: none;">{$one->html}</div>
  {/foreach}
</div>
{/strip}{/if}