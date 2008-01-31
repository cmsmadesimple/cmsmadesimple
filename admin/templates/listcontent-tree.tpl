<div class="pageoverflow">
  <p class="pageoptions">
    {if $add_pages eq true}
      <a href="addcontent.php" class="pageoptions">{$newobject_image}</a>
      <a class="pageoptions" href="addcontent.php">{lang string='addcontent'}</a>
    {/if}
  </p>
</div>

<form action="multicontent.php" method="post">
  <table cellspacing="0" class="pagetable">
    <thead>
      <tr>
        <th width="12">&nbsp;</th>
        <th>&nbsp;</th>
        <th class="pagew25">{lang string='title'}</th>
        <th>{lang string='template'}</th>
        <th>{lang string='type'}</th>
        {if $modify_page_structure eq true}
          <th class="pagepos">{lang string='active'}</th>
        {/if}
        <th class="pagepos">{lang string='default'}</th>
        {if $modify_page_structure eq true and $check_modify_all eq true}
          <th class="move">{lang string='move'}</th>
          <th class="pagepos invisibleme">{lang string='order'}</th>
        {/if}
        <th class="pageicon">&nbsp;</th>
        <th class="pageicon">&nbsp;</th>
        <th class="pageicon">&nbsp;</th>
        {if $modify_page_structure eq true}
          <th class="checkbox">&nbsp;</th>
        {/if}
      </tr>
    </thead>
    <tbody id="sortparent">
      {if count($content->get_children()) gt 0}
        {include file='listcontent-entries.tpl' content=$content->get_children() siblingcount=$content->get_children_count()}
      {/if}
    </tbody>
  </table>
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