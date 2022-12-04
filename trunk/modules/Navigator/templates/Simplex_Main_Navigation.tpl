{strip}

{$main_id = ' id=\'main-menu\''}
{function do_class}
    {if count($classes) > 0} class='{implode(' ',$classes)}'{/if}
{/function}

{function name='Simplex_menu' depth='1'}
    <ul{$main_id}{if isset($ul_class) && $ul_class != ''} class="{$ul_class}"{/if}>
        {$main_id = ''}
        {$ul_class = ''}
        {foreach $data as $node}
            {* setup classes for the anchor and list item *}
            {$list_class = []}
            {$href_class = ['cf']}
            {$parent_indicator = ''}
            {$aria_support = ''}
    
            {if $node->current || $node->parent}
                {* this is the current page *}
                {$list_class[] = 'current'}
                {$href_class[] = 'current'}
            {/if}
    
            {if $node->children_exist}
                {$list_class[] = 'parent'}
                {$aria_support = ' aria-haspopup=\'true\''}
                {$parent_indicator = ' <i class=\'icon-arrow-left\' aria-hidden=\'true\'></i>'}
            {/if}
    
            {* build the menu item node *}
            {if $node->type == 'sectionheader'}
                {$list_class[] = 'sectionheader'}
                <li{do_class classes=$list_class}{$aria_support}><span>{$node->menutext}{$parent_indicator}</span>
                {if isset($node->children)}
                    {Simplex_menu data=$node->children depth=$depth+1}
                {/if}
                </li>
            {else if $node->type == 'separator'}
                {$list_class[] = 'separator'}
                <li{do_class classes=$list_class}'><hr class='separator'/></li>
            {else}
                {* regular item *}
                <li{do_class classes=$list_class}{$aria_support}>
                    <a{do_class classes=$href_class} href='{$node->url}'{if $node->target != ''} target='{$node->target}'{/if}>{$node->menutext}{$parent_indicator}</a>
                    {if isset($node->children)}
                        {Simplex_menu data=$node->children depth=$depth+1}
                    {/if}
                </li>
            {/if}
        {/foreach}
    </ul>
{/function}

{if isset($nodes)}
    {Simplex_menu data=$nodes depth='0' ul_class='cf'}
{/if}

{/strip}