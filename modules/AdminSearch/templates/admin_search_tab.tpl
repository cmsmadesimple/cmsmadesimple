<style type="text/css">{literal}
#status_area,#searchresults_cont,#workarea {
  display: none;
}
#searchresults {
  max-height: 25em;
  overflow:   auto;
}
{/literal}</style>

<script type="text/javascript">{literal}
function status_error(text) {
  var html = '<p class="status_error">'+text+'</p>';
  _update_status(html);
}
function status_msg(text) {
  var html = '<p class="pagetext">'+text+'</p>';
  _update_status(html);
}
function _update_status(html) {
  $('#status_area').html(html);
  $('#status_area').show();
}
function begin_section(lbl) {
  $('#searchresults').append('<li>'+lbl+'<ul>');
}
function add_result(html) {
  $('#searchresults_cont').show();
  $('#searchresults').append('<li>'+html+'</li>');
}
function end_section() {
  $('#searchresult').append('</ul></li>');
}
$(document).ready(function(){
  $('#adminsearchform > form').attr('target','workarea');
  $('#workarea').attr('src','{/literal}{$ajax_url}{literal}');
  $('#filter_all').live('click',function(e){
    var t = $(this).attr('checked');
    if( t == 'checked' ) {
      $('.filter_toggle').attr('checked',t);
    }
    else {
      $('.filter_toggle').removeAttr('checked');
    }
  });
  $('#searchbtn').live('click',function(e){
    $('#searchresults').html('');
  });
});
{/literal}</script>
	
<div id="adminsearchform">
{$formstart}

<table class="pagetable" cellspacing="0"><tr valign="top">
<td width="50%">
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('search_text')}:</p>
  <p class="pageinput">
    <input type="text" name="{$actionid}search_text" value="" size="80" maxlength="80" id="searchtext"/>
  </p>
</div>
<div class="pageoverflow">
  <p class="pagetext"></p>
  <p class="pageinput">
    <input type="submit" name="{$actionid}submit" value="{$mod->Lang('search')}" id="searchbtn" />
  </p>
</div>
</td>
<td>
<td width="50%">
<div class="pageoverflow">
  <p class="pagetext">{$mod->Lang('filter')}:</p>
  <p class="pageinput" style="min-height: 3em; max-height: 7em; overflow: auto;">
    <input id="filter_all" type="checkbox" value="-1"/>&nbsp;<label for="filter_all" title="{$mod->Lang('desc_filter_all')}">{$mod->Lang('all')}</label><br/>
    {foreach from=$slaves item='slave' name='slaves'}
      <input class="filter_toggle" id="{$slave.class}" type="checkbox" name="{$actionid}slaves[]" value="{$slave.class}"/>&nbsp;<label for="{$slave.class}" title="{$slave.description}">{$slave.name}</label>{if !$smarty.foreach.slaves.last}<br/>{/if}
    {/foreach}
  </p>
</div>
</td>
</tr></table>

<div class="pageoverflow" id="progress_area"></div>
<div class="pageoverflow" id="status_area"></div>
<fieldset id="searchresults_cont">
  <legend>{$mod->Lang('search_text')}:</legend>
  <div id="searchresults_cont2">
    <ul id="searchresults">
    </ul>
  </div>
</fieldset>
{$formend}
</div>

<iframe id="workarea" name="workarea"></iframe>