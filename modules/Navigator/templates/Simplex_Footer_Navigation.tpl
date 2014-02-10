{strip}

{$main_id = ' id=\'footer-menu\''}
{function do_footer_class}
    {if count($classes) > 0} class='{implode(' ',$classes)}'{/if}
{/function}

{function name='Simplex_footer_menu' depth='1'}
    <ul{$main_id}{if isset($ul_class) && $ul_class != ''} class="{$ul_class}"{/if}>
        {$main_id = ''}
        {$ul_class = ''}
        {foreach $data as $node}
            {* setup classes for the anchor and list item *}
            {$list_class = []}
            {$href_class = []}
    
            {if $node->current || $node->parent}
                {* this is the current page *}
                {$list_class[] = 'current'}
                {$href_class[] = 'current'}
            {/if}
    
            {if $node->children_exist}
                {$list_class[] = 'parent'}
            {/if}
    
            {* build the menu item node *}
            {if $node->type == 'sectionheader'}
                {$list_class[] = 'sectionheader'}
                <li{do_footer_class classes=$list_class}><span>{$node->menutext}</span>
                {if isset($node->children)}
                    {Simplex_footer_menu data=$node->children depth=$depth+1}
                {/if}
                </li>
            {else if $node->type == 'separator'}
                {$list_class[] = 'separator'}
                <li{do_footer_class classes=$list_class}'><hr class='separator'/></li>
            {else}
                {* regular item *}
                <li{do_footer_class classes=$list_class}>
                    <a{do_footer_class classes=$href_class} href='{$node->url}'{if $node->target != ''} target='{$node->target}'{/if}>{$node->menutext}</a>
                    {if isset($node->children)}
                        {Simplex_footer_menu data=$node->children depth=$depth+1}
                    {/if}
                </li>
            {/if}
        {/foreach}
    </ul>
{/function}

{if isset($nodes)}
    {Simplex_footer_menu data=$nodes depth='0' ul_class='cf'}
{/if}

{/strip}