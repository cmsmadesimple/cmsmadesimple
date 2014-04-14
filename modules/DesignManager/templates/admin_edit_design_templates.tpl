<div class="information">{$mod->Lang('info_edittemplate_templates_tab')}</div>
{if !isset($all_templates)}
<div class="pagewarning">{$mod->Lang('warning_edittemplate_notemplates')}</div>
{else}

{assign var='tmpl' value=$design->get_templates()}
<div class="c_full cf">
    <div class="grid_6 draggable-area">
        <fieldset>
            <legend>{$mod->Lang('available_templates')}</legend>
            <div id="available-templates">
                <ul class="sortable-templates sortable-list available-templates">
                {foreach from=$all_templates item='tpl'}
                    {if !$tmpl || !in_array($tpl->get_id(),$tmpl)}
                        <li class="ui-state-default" data-cmsms-item-id="{$tpl->get_id()}">
                            {$tpl->get_name()}
                            <input class="hidden" type="checkbox" name="{$actionid}assoc_tpl[]" value="{$tpl->get_id()}" />
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
                    {if $design->get_templates()|@count == 0}<li class="placeholder no-sort">{$mod->Lang('drop_items')}</li>{/if}
                    {foreach from=$all_templates item='tpl'}
                        {if $tmpl && in_array($tpl->get_id(),$tmpl)}
                            <li class="ui-state-default cf sortable-item no-sort" data-cmsms-item-id="{$tpl->get_id()}">
                                {$tpl->get_name()}
                                <a href="#" title="{$mod->Lang('remove')}" class="ui-icon ui-icon-trash sortable-remove">{$mod->Lang('remove')}</a>
                                <input class="hidden" type="checkbox" name="{$actionid}assoc_tpl[]" value="{$tpl->get_id()}" checked="checked" />
                            </li>
                        {/if}
                    {/foreach}
                </ul>
            </div>
        </fieldset>
    </div>
</div>
<script>
$(function() {
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
            $(elements).removeClass('selected ui-state-hover')
                       .append($('<a href="#"/>').addClass('ui-icon ui-icon-trash sortable-remove').text('Remove'))
                       .find('input[type="checkbox"]').attr('checked', true);
        }
    
    });
        
    $(document).on('click', '#selected-templates .sortable-remove', function(e) {
        $(this).next('input[type="checkbox"]').attr('checked', false);
        $(this).parent('li').appendTo('#available-templates ul');
        e.preventDefault();
    });
});
</script>
{/if}