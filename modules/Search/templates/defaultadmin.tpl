{*mod_form action='defaultadmin'}

<p>
	{mod_label name="reindex"}{tr}Reindex Search Module{/tr}{/mod_label}: 
	{mod_submit name="reindex" translate="true" value="Reindex"}
</p>

{/mod_form*}

{has_permission perm="Modify Templates"}
	<div class="pageoverflow" style="text-align: right; width: 80%;"><a href="listmodtemplates.php" title="{tr}modify_templates{/tr}">{tr}modify_templates{/tr}</a></div><br/>
{/has_permission}

{tabs}
	{has_permission perm="Modify Site Preferences"}
		{tab_content name="statistics"}
			{tab_header name="statistics"}{tr}statistics{/tr}{/tab_header}
			{$statistics_tab}
		{/tab_content}
		{tab_content name="options"}
			{tab_header name="options"}{tr}options{/tr}{/tab_header}
			{mod_form action="defaultadmin"}
				{mod_submit name="reindex" value="reindexallcontent"}
				{mod_submit name="optimize" value="optimizecontent"}

				<hr />
				<p>{tr}stopwords{/tr}:<br />{mod_textarea value=$stopwords name="stopwords" cols="50" rows="6"}</p>
				<p>{tr}prompt_searchtext{/tr}:&nbsp;{mod_textbox name="searchtext" value=$searchtext}</p>
				<p>{tr}prompt_alpharesults{/tr}:&nbsp;{mod_textbox name="alpharesults" value=$alpharesults}</p>
				{mod_submit name="submit" value="submit"}
			{/mod_form}
		{/tab_content}			
	{/has_permission}
{/tabs}

