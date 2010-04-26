{if $theme_object->has_messages()}
    <div class="pagemessage">
        {foreach from=$theme_object->messages item='one_message'}
            <img class="systemicon" title="Sucesso" alt="Sucesso" src="themes/default/images/icons/system/accept.gif"/> {$one_message}<br />
        {/foreach}
    </div>
{/if}