{if $theme_object->has_errors()}
    <div class="pageerror">
        {foreach from=$theme_object->errors item='one_error'}
            <img class="systemicon" title="Erro" alt="Erro" src="themes/default/images/icons/system/stop.gif"/>	{$one_error}<br />
        {/foreach}
    </div>
{/if}