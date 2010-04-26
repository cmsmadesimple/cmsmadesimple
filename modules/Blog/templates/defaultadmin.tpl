{admin_tabs}

	{tab_headers active=$selected_tab}

		{tab_header name="writepost"}{tr}Write Post{/tr}{/tab_header}
		{tab_header name="manageposts"}{tr}Manage Posts{/tr}{/tab_header}
	
	{/tab_headers}

	{tab_content name="writepost"}
	
		{$writepost}
	
	{/tab_content}	
	
	{tab_content name="manageposts"}
	
		{$manageposts}
	
	{/tab_content}

{/admin_tabs}
