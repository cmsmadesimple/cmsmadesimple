<h2>Thanks for installing CMS Made Simple</h2>
<table class="countdown" cellspacing="2" cellpadding="2">
	<tr>
{section name=stepimages loop=$number_of_pages}
{assign var='page' value=$smarty.section.stepimages.index+1}
		<td><img src="images/{$page}{if $page == $current_page}off{/if}.gif" alt="Step {$page}" /></td>
{/section}
	</tr>
</table>
