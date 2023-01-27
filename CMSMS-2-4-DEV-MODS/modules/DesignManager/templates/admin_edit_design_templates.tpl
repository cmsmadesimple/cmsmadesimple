<style type="text/css">
#available-templates li.selected {
   background-color: #147fdb;
}
#template_sel li:focus {
   color: #147fdb;
}
#template_sel li a:focus {
   color: #147fdb;
}
#template_sel a.ui-icon+a:focus {
   border: 2px solid #147fdb;
}
</style>

<div class="information">{$mod->Lang('info_edittemplate_templates_tab')}</div>
{if !isset($all_templates)}
<div class="pagewarning">{$mod->Lang('warning_edittemplate_notemplates')}</div>
{else}

{assign var='tmpl' value=$design->get_templates()}
<div class="c_full cf" id="template_sel">
    <div class="grid_6 draggable-area">
        <fieldset>
            <legend>{$mod->Lang('available_templates')}</legend>
            <div id="available-templates">
                <ul class="sortable-templates sortable-list available-items available-templates">
                {foreach from=$all_templates item='tpl'}
                    {if !$tmpl || !in_array($tpl->get_id(),$tmpl)}
                        <li class="ui-state-default" data-cmsms-item-id="{$tpl->get_id()}" tabindex="0">
                            <span>{$tpl->get_name()}</span>
                            <input class="hidden" type="checkbox" name="{$actionid}assoc_tpl[]" value="{$tpl->get_id()}"/>
                        </li>
                    {/if}
                {/foreach}
                </ul>
            </div>
        </fieldset>
    </div>
    <div class="grid_6">
        <fieldset>
            <legend>{$mod->Lang('attached_templates')}</legend>
            <div id="selected-templates">
                <ul class="sortable-templates sortable-list selected-templates">
                    {if $design->get_templates()|default:[]|count == 0}<li class="placeholder no-sort">{$mod->Lang('drop_items')}</li>{/if}
                    {foreach from=$all_templates item='tpl'}
                        {if $tmpl && in_array($tpl->get_id(),$tmpl)}
                            <li class="ui-state-default cf sortable-item no-sort" data-cmsms-item-id="{$tpl->get_id()}" tabindex="-1">
			        {if $manage_templates}
                                <a href="{cms_action_url action=admin_edit_template tpl=$tpl->get_id()}" class="edit_tpl" title="{$mod->Lang('edit_template')}">{$tpl->get_name()}</a>
				{else}
				<span>{$tpl->get_name()}</span>
				{/if}
                                <a href="#" title="{$mod->Lang('remove')}" class="ui-icon ui-icon-trash sortable-remove">{$mod->Lang('remove')}</a>
                                <input class="hidden" type="checkbox" name="{$actionid}assoc_tpl[]" value="{$tpl->get_id()}" checked="checked"/>
                            </li>
                        {/if}
                    {/foreach}
                </ul>
            </div>
        </fieldset>
    </div>
</div>
<script>
function find_sortable_focus(in_e) {
   var _list = $(':tabbable');
   var _idx = _list.index(in_e);
   var _out_e = _list.eq(_idx+1).length ? _list.eq(_idx+1) : _list.eq(0);
   _out_e.focus();
}

$(function() {
    var _manage_templates = '{$manage_templates}';
    var _edit_url = '{cms_action_url action=admin_edit_template tpl=xxxx forjs=1}';
    $('ul.sortable-templates').sortable({
        connectWith: '#selected-templates ul',
        delay: 150,
        revert: true,
        placeholder: 'ui-state-highlight',
        items: 'li:not(.no-sort)',
        helper: function (event, ui) {
            if (!ui.hasClass('selected')) {
                ui.addClass('selected')
                  .siblings()
                  .removeClass('selected');
            }

            var elements = ui.parent()
                             .children('.selected')
                             .clone(),
                helper = $('<li/>');

            ui.data('multidrag', elements).siblings('.selected').remove();
            return helper.append(elements);
        },
        stop: function (event, ui) {
            var elements = ui.item.data('multidrag');

            ui.item.after(elements).remove();
        },
        receive: function(event, ui) {
            var elements = ui.item.data('multidrag');

            $('.sortable-templates .placeholder').hide();

            $(elements).each(function(){
		var _tpl_id = $(this).data('cmsms-item-id');
		var _url = _edit_url.replace('xxxx',_tpl_id);
		var _text = $(this).text().trim();
		var _e;
		if( _manage_templates ) {
		    _e = $('<a/>').attr('href',_url).text(_text).addClass('edit_tpl unsaved').attr('title','{$mod->Lang('edit_template')}');
		} else {
		    _e = $('<span/>').text(_text);
		}
		$('span',this).remove();
		$(this).append(_e);
		$(this).removeClass('selected ui-state-hover')
	               .attr('tabindex',-1)
                       .addClass('unsaved no-sort')
                       .append($('<a href="#"/>').addClass('ui-icon ui-icon-trash sortable-remove').text('Remove'))
                       .find('input[type="checkbox"]').attr('checked', true);
	    });
	    set_changed();
        }
    });

    $(document).on('click', '#available-templates li',function(ev) {
        $(this).focus();
    });

    $(document).on('click', '#selected-templates li',function(ev) {
        $('a:first',this).focus();
    });

    $(document).on('keyup', '#available-templates li',function(ev) {
        if( ev.keyCode == $.ui.keyCode.ESCAPE ) {
	    // escape
	    $('#available-templates li').removeClass('selected');
	    ev.preventDefault();
	}
        if( ev.keyCode == $.ui.keyCode.SPACE || ev.keyCode == 107 ) {
	   // spacebar or plus
	   console.debug('selected');
	   ev.preventDefault();
	   $(this).toggleClass('selected ui-state-hover');
	   find_sortable_focus(this);
	}
	else if( ev.keyCode == 39 ) {
	   // right arrow.
	   $('#available-templates li.selected').each(function(){
	      $(this).removeClass('selected');
	      var _tpl_id = $(this).data('cmsms-item-id');
	      var _url = _edit_url.replace('xxxx',_tpl_id);
	      var _text = $(this).text().trim();

	      var _el = $(this).clone();
	      var _a;
	      if( _manage_templates ) {
	      	  _a = $('<a/>').attr('href',_url).text(_text).addClass('edit_tpl unsaved').attr('title','{$mod->Lang('edit_template')}');
   	      } else {
	          _a = $('<span/>').text(_text);
	      }
	      $('span',_el).remove();
	      $(_el).append(_a);
	      $(_el).removeClass('selected ui-state-hover')
	               .attr('tabindex',-1)
                       .addClass('unsaved no-sort')
                       .append($('<a href="#"/>').addClass('ui-icon ui-icon-trash sortable-remove').text('{$mod->Lang('remove')}').attr('title','{$mod->Lang('remove')}'))
                       .find('input[type="checkbox"]').attr('checked', true);
	      $('#selected-templates > ul').append(_el);
	      $(this).remove();
	      set_changed();

	      // set focus somewhere
	      find_sortable_focus(this);
	   });
	   console.debug('got arrow');
	}
    });

    $(document).on('click', '#selected-templates .sortable-remove', function(e) {
        // click on remove icon
        e.preventDefault();
	set_changed();
        $(this).next('input[type="checkbox"]').attr('checked', false);
        $(this).parent('li').removeClass('no-sort').appendTo('#available-templates ul');
        $(this).remove();
    });

    $(document).on('click','a.edit_tpl',function(ev){
       if( __changed ) {
           ev.preventDefault();
          var url = $(this).attr('href');
      	  cms_confirm('{$mod->Lang('confirm_save_design')}').done(function(){
             // save and redirect
	     save_design().done(function(){
	        window.location.href = url;
	     });
	  });
       }
       // normal default link behavior.
    });
});
</script>
{/if}