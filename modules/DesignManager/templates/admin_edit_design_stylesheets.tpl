{* stylesheets tab for edit template *}
<style type="text/css">
#available-stylesheets li.selected {
   background-color: #147fdb;
}
#available-stylesheets li:focus {
   color: #147fdb;
}
#selected-stylesheets li a:focus {
   color: #147fdb;
}
#selected-stylesheets a.ui-icon+a:focus {
   border: 2px solid #147fdb;
}
</style>

<div class="information">{$mod->Lang('info_edittemplate_stylesheets_tab')}</div>
{if !isset($all_stylesheets)}
  <div class="warning" style="width: 95%;">{$mod->Lang('warning_editdesign_nostylesheets')}</div>
{else}
  {assign var='cssl' value=$design->get_stylesheets()}
  <div class="c_full cf">
    <div class="grid_6 draggable-area">
        <fieldset>
            <legend>{$mod->Lang('available_stylesheets')}</legend>
            <div id="available-stylesheets">
                <ul class="sortable-stylesheets sortable-list available-items available-stylesheets">
                {foreach from=$all_stylesheets item='css'}
                    {if !$cssl or !in_array($css->get_id(),$cssl)}
                        <li class="ui-state-default" data-cmsms-item-id="{$css->get_id()}" tabindex="0">
                            <span>{$css->get_name()}</span>
                            <input class="hidden" type="checkbox" name="{$actionid}assoc_css[]" value="{$css->get_id()}" tabindex="-1" />
                        </li>
                    {/if}
                {/foreach}
                </ul>
            </div>
        </fieldset>
    </div>
    <div class="grid_6">
        <fieldset>
            <legend>{$mod->Lang('attached_stylesheets')}</legend>
            <div id="selected-stylesheets">
                <ul class="sortable-stylesheets sortable-list selected-stylesheets">
                    {if $design->get_stylesheets()|count == 0}<li class="placeholder">{$mod->Lang('drop_items')}</li>{/if}
                    {foreach from=$design->get_stylesheets() item='one'}
                        <li class="ui-state-default cf sortable-item" data-cmsms-item-id="{$one}">
                            <a href="{cms_action_url action=admin_edit_css css=$one}" class="edit_css" title="{$mod->Lang('edit_stylesheet')}">{$list_stylesheets.$one}</a>
                            <a href="#" "title="{$mod->Lang('remove')}" class="ui-icon ui-icon-trash sortable-remove" title="{$mod->Lang('remove')}">{$mod->Lang('remove')}</a>
                            <input class="hidden" type="checkbox" name="{$actionid}assoc_css[]" value="{$one}" checked="checked" tabindex="-1"/>
                        </li>
                    {/foreach}
                </ul>
            </div>
        </fieldset>
    </div>
  </div>
  <script>
  $(function() {
    var _edit_url = '{cms_action_url action=admin_edit_css css=xxxx forjs=1}';
    $('ul.sortable-stylesheets').sortable({
        connectWith: '#selected-stylesheets ul',
        delay: 150,
        revert: true,
        placeholder: 'ui-state-highlight',
        items: 'li:not(.placeholder)',
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

            $('.sortable-stylesheets .placeholder').hide();
            $(elements).removeClass('selected ui-state-hover')
                       .append($('<a href="#"/>').addClass('ui-icon ui-icon-trash sortable-remove').text('Remove'))
                       .find('input[type="checkbox"]').attr('checked', true);
        }

    });

    $(document).on('click', '#available-stylesheets li',function(ev) {
        $(this).focus();
    });

    $(document).on('click', '#selected-stylesheets li',function(ev) {
        $('a:first',this).focus();
    });

    $(document).on('keyup','#available-stylesheets li',function(ev){
        if( ev.keyCode == $.ui.keyCode.ESCAPE ) {
          // escape
          $('#selected-stylesheets li').removeClass('selected');
          ev.preventDefault();
        }
        else if( ev.keyCode == $.ui.keyCode.SPACE || ev.keyCode == 107 ) {
          // spacebar or plus
	  ev.preventDefault();
          $(this).toggleClass('selected ui-state-hover');
          find_sortable_focus(this);
        }
        else if( ev.keyCode == 39 ) {
          // right arrow
          ev.preventDefault();
          $('#available-stylesheets li.selected').each(function(){
            $(this).removeClass('selected ui-state-hover');
            var _css_id = $(this).data('cmsms-item-id');
            var _url = _edit_url.replace('xxx',_css_id);
            var _text = $(this).text().trim();

            var _el = $(this).clone();
            var _a;
            _a = $('<a/>').attr('href',_url).text(_text).addClass('edit_css unsaved').
                      attr('title','{$mod->Lang('edit_stylesheet')}');
            $('span',_el).remove();
            $(_el).append(_a);
            $(_el).removeClass('selected ui-state-hover')
	         .attr('tabindex',-1)
                 .addClass('unsaved no-sort')
                 .append($('<a href="#"/>').addClass('ui-icon ui-icon-trash sortable-remove').text('{$mod->Lang('remove')}').attr('title','{$mod->Lang('remove')}'))
                 .find('input[type="checkbox"]').attr('checked',true);
            $('#selected-stylesheets > ul').append(_el);
            $(this).remove();
            set_changed();

            // set focus somewhere
            find_sortable_focus(this);
          });
        }
    });

    $(document).on('click', '#selected-stylesheets .sortable-remove', function(e) {
        e.preventDefault();
        set_changed();
        $(this).next('input[type="checkbox"]').attr('checked', false);
        $(this).parent('li').appendTo('#available-stylesheets ul');
        $(this).remove();
    });

    $(document).on('click','a.edit_css',function(ev){
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
    });

  });
  </script>
{/if}