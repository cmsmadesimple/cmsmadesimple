{$startform}
<label for="{$search_actionid}searchinput">{$searchprompt}:&nbsp;</label><input type="text" class="search-input" id="{$search_actionid}searchinput" name="{$search_actionid}searchinput" size="20" maxlength="50" value="{$searchtext}" {$hogan}/><input class="search-button" name="submit" value="{$submittext}" type="submit" />
{if isset($hidden)}{$hidden}{/if}
{$endform}
