<div class="pageoverflow">
  <p class="pageoptions">
    {if $add_pages eq true}
      <a href="addcontent.php" class="pageoptions">{$newobject_image}</a>
      <a class="pageoptions" href="addcontent.php">{lang string='addcontent'}</a>
    {/if}
  </p>
</div>

<form action="multicontent.php" method="post">
	{if count($content->get_children()) gt 0}
		<ul style="list-style-type: none;" id="list-container">
			{include file='listcontent-entries.tpl' content=$content->get_children() siblingcount=$content->get_children_count()}
		</ul>
	{/if}
</form>

<div class="pageoverflow">
  <p class="pageoptions">
    {if $add_pages eq true}
      <a href="addcontent.php" class="pageoptions">{$newobject_image}</a>
      <a class="pageoptions" href="addcontent.php">{lang string='addcontent'}</a>
    {/if}
    <a style="margin-left: 10px;" href="listcontent.php?expandall=1" onclick="cms_ajax_content_expandall(); return false;">{$expandall_image}</a>
    <a href="listcontent.php?expandall=1" onclick="cms_ajax_content_expandall(); return false;">{lang string='expandall'}</a>
    <a style="margin-left: 10px;" href="listcontent.php?collapseall=1" onclick="cms_ajax_content_collapseall(); return false;">{$collapseall_image}</a>
    <a href="listcontent.php?collapseall=1" onclick="cms_ajax_content_collapseall(); return false;">{lang string='contractall'}</a>
  </p>
</div>

