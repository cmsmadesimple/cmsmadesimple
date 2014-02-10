<div class='five-col search noprint' role='search'>
    {$startform}
        <label for='{$search_actionid}searchinput' class='visuallyhidden'>{$searchprompt}:</label>
        <input type='search' class='search-input' id='{$search_actionid}searchinput' name='{$search_actionid}searchinput' size='20' maxlength='50' value='' placeholder='{$searchtext}' /><i class='icon-search' aria-hidden='true'></i>
        {if isset($hidden)}{$hidden}{/if}
    {$endform}
</div>