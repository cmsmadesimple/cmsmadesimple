<script type="text/javascript">
$(document).ready(function(){
    cms_busy();
    $('#stylesheet_area').autoRefresh({
      url: '{$ajax_stylesheets_url}',
      data: {
        filter: '{$jsoncssfilter}'
      }
    });

    $('#css_bulk_action,#css_bulk_submit').attr('disabled','disabled');
    $('#css_bulk_submit').button({ 'disabled' : true });
    $('#css_selall,.css_select').on('click',function(){
      // if there is one or more .css_select checked, we enabled the bulk actions
      var l = $('.css_select:checked').length;
      if( l == 0 ) {
        $('#css_bulk_action').attr('disabled','disabled');
        $('#css_bulk_submit').attr('disabled','disabled');
        $('#css_bulk_submit').button({ 'disabled' : true });
      } else {
        $('#css_bulk_action').removeAttr('disabled');
        $('#css_bulk_submit').removeAttr('disabled');
        $('#css_bulk_submit').button({ 'disabled' : false });
      }
    });

    $('a.steal_css_lock').on('click',function(e) {
      // we're gonna confirm stealing this lock.
      return confirm('{$mod->Lang('confirm_steal_lock')|escape:'javascript'}');
    });

    $('#stylesheet_area').on('click','#editcssfilter',function(){
      $('#filtercssdlg').dialog({
        width: 'auto',
        buttons: {
          '{$mod->Lang('submit')}': function () {
            $(this).dialog('close');
            $('#filtercssdlg_form').submit();
          },
          '{$mod->Lang('reset')}': function () {
            $(this).dialog('close');
	    $('#submit_filter_css').val('-1');
            $('#filtercssdlg_form').submit();
          },
          '{$mod->Lang('cancel')}': function () {
            $(this).dialog('close');
          },
        }
      });
    });
});
</script>

<div id="filtercssdlg" style="display: none;" title="{$mod->Lang('css_filter')}">
  {form_start id='filtercssdlg_form'}{*strip*}
    <input type="hidden" id="submit_filter_css" name="{$actionid}submit_filter_css" value="1"/>
    <div class="c_full">
      <label for="filter_css_design" class="grid_3 text-right">{$mod->Lang('prompt_design')}:</label>
      <select id="filter_css_design" name="{$actionid}filter_css_design" title="{$mod->Lang('title_filter_design')}" class="grid_9">
          <option value="">{$mod->Lang('any')}</option>
	  {html_options options=$design_names selected=$css_filter.design}
      </select>
    </div>
    <div class="c_full">
      <label for="filter_css_sortby" class="grid_3 text-right">{$mod->Lang('prompt_sortby')}:</label>
      <select id="filter_css_sortby" name="{$actionid}filter_css_sortby" title="{$mod->Lang('title_sortby')}" class="grid_9">
          <option value="name"{if $css_filter.sortby == 'name'} selected="selected"{/if}>{$mod->Lang('name')}</option>
          <option value="created"{if $css_filter.sortby == 'created'} selected="selected"{/if}>{$mod->Lang('created')}</option>
          <option value="modified"{if $css_filter.sortby == 'modified'} selected="selected"{/if}>{$mod->Lang('modified')}</option>
      </select>
    </div>
    <div class="c_full">
      <label for="filter_css_sortorder" class="grid_3">{$mod->Lang('prompt_sortorder')}:</label>
      <select id="filter_css_sortorder" name="{$actionid}filter_css_sortorder" title="{$mod->Lang('title_sortorder')}" class="grid_9">
        <option value="asc"{if $css_filter.sortorder == 'asc'} selected="selected"{/if}>{$mod->Lang('asc')}</option>
        <option value="desc"{if $css_filter.sortorder == 'desc'} selected="selected"{/if}>{$mod->Lang('desc')}</option>
      </select>
    </div>
    <div class="c_full">
      <label for="filter_limit_css" class="grid_3">{$mod->Lang('prompt_limit')}:</label>
      <select id="filter_limit_css" name="{$actionid}filter_limit_css" class="grid_9">
          <option value="10"{if (isset($css_filter.limit) && ($css_filter.limit == 10)) } selected="selected"{/if}>10</option>
	  <option value="25"{if (isset($css_filter.limit) && ($css_filter.limit == 25)) } selected="selected"{/if}>25</option>
	  <option value="50"{if (isset($css_filter.limit) && ($css_filter.limit == 50)) } selected="selected"{/if}>50</option>
	  <option value="100"{if (isset($css_filter.limit) && ($css_filter.limit == 100)) } selected="selected"{/if}>100</option>
      </select>
    </div>
  {form_end}
</div>

<div id="stylesheet_area"></div>