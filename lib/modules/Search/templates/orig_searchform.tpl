{* this is the original search form template *}
{* $formparms get encoded into the form's action URL, not into the form itself to prevent forgery *}
{form_start action=dosearch method=$form_method returnid=$destpage inline=$inline extraparms=$formparms}
    <label for="{$search_actionid}searchinput">{$searchprompt}:&nbsp;</label>
    <input type="search" class="search-input" id="{$search_actionid}searchinput" name="{$search_actionid}searchinput" size="20" maxlength="50" placeholder="{$searchtext}" required/>
    {*
    <br/>
    <input type="checkbox" name="{$search_actionid}use_or" value="1"/>
    *}
    <input class="search-button" name="submit" value="{$submittext}" type="submit" />
    {if isset($hidden)}{$hidden}{/if}
{form_end}
