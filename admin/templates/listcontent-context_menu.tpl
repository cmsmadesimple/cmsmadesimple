{get_content item=$current}
{get_template id=$content_item->template_id to=template}
{get_edit_permission userid=$content_item->owner_id id=$content_item->id mypages=$mypages modify_any_page=$check_modify_all to=has_edit}
{*
<ul>
    {if $add_pages eq true}
    	<li><a href="addcontent.php?parent_id={$content_item->id}">{tr}Add Child Here{/tr}</a></li>
    {/if}
	{if $modify_page_structure eq true}
		{if $content_item->active eq true}
			{if $content_item->default_content ne true}
				<li><a href="listcontent.php?setinactive={$content_item->id}" onclick="cms_ajax_content_setinactive({$content_item->id}); return false;">{tr}Set Inactive{/tr}</a></li>
			{/if}
		{else}
			<li><a href="listcontent.php?setactive={$content_item->id}" onclick="cms_ajax_content_setactive({$content_item->id}); return false;">{tr}Set Active{/tr}</a></li>
		{/if}
	{/if}
</ul>
*}
$.tree.reference('#content_tree').menu_def =
[
    {if $add_pages eq true}
        {ldelim}
            'Add Child Here':
            {ldelim}
                onclick:function(menuItem, menu)
                {ldelim}
                    cms_ajax_content_new({$content_item->id});
                {rdelim},
                title: 'Add a child to this page'
            {rdelim}
        {rdelim},
    {/if}
	{if $modify_page_structure eq true}
		{if $content_item->active eq true}
		    {if $content_item->default_content ne true}
                {ldelim}
                    'Set Inactive':
                    {ldelim}
                        onclick:function(menuItem, menu)
                        {ldelim}
                            cms_ajax_content_setinactive({$content_item->id});
                        {rdelim},
                        title: 'Make this page inactive'
                    {rdelim}
                {rdelim},
                {ldelim}
                    'Make Default Page':
                    {ldelim}
                        onclick:function(menuItem, menu)
                        {ldelim}
                            cms_ajax_content_setdefault({$content_item->id});
                        {rdelim},
                        title: 'Make this the default page'
                    {rdelim}
                {rdelim},
		    {/if}
		{else}
            {ldelim}
                'Set Active':
                {ldelim}
                    onclick:function(menuItem, menu)
                    {ldelim}
                        cms_ajax_content_setactive({$content_item->id});
                    {rdelim},
                    title: 'Make this page active'
                {rdelim}
            {rdelim},
		{/if}
	{/if}
];