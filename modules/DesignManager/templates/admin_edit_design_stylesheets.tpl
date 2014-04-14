{* stylesheets tab for edit template *}
<div class="information">{$mod->Lang('info_edittemplate_stylesheets_tab')}</div>
{if !isset($all_stylesheets)}
<div class="pagewarning">{$mod->Lang('warning_edittemplate_nostylesheets')}</div>
{/if}

{assign var='cssl' value=$design->get_stylesheets()}
<div class="c_full cf">
    <div class="grid_6 draggable-area">
        <fieldset>
            <legend>{$mod->Lang('available_stylesheets')}</legend>
            <div id="available-stylesheets">
                <ul class="sortable-stylesheets sortable-list available-items">
                {foreach from=$all_stylesheets item='css'}
                    {if !$cssl or !in_array($css->get_id(),$cssl)}
                        <li class="ui-state-default" data-cmsms-item-id="{$css->get_id()}">
                            {$css->get_name()}
                            <input class="hidden" type="checkbox" name="{$actionid}assoc_css[]" value="{$css->get_id()}" />
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
                <ul class="sortable-stylesheets sortable-list selected-items">
                    {if $design->get_stylesheets()|count == 0}<li class="placeholder">{$mod->Lang('drop_items')}</li>{/if}
                    {foreach from=$design->get_stylesheets() item='one'}
                        <li class="ui-state-default cf sortable-item" data-cmsms-item-id="{$one}">
                            {$list_stylesheets.$one}
                            <a href="#" title="{$mod->Lang('remove')}" class="ui-icon ui-icon-trash sortable-remove">{$mod->Lang('remove')}</a>
                            <input class="hidden" type="checkbox" name="{$actionid}assoc_css[]" value="{$one}" checked="checked" />
                        </li>
                    {/foreach}
                </ul>
            </div>
        </fieldset>
    </div>
</div>
<script>
$(function() {
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
        
    $(document).on('click', '#selected-stylesheets .sortable-remove', function(e) {
        $(this).next('input[type="checkbox"]').attr('checked', false);
        $(this).parent('li').appendTo('#available-stylesheets ul');
        e.preventDefault();
    });
});
</script>