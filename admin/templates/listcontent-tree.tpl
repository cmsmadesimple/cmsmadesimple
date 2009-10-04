<div id="content_tree" class="content_tree">
    {if $content->has_children()}
        <ul>
            {include file='listcontent-entries.tpl' content=$content->get_children()}
        </ul>
    {/if}
</div>
{literal}
<script type="text/javascript">
$(function () {
	$("#content_tree").tree(
	    {
	        rules:
	        {
                draggable : "all",
                multiple : false
	        },
	        ui:
	        {
    	        theme_path: "../lib/jquery/tree/themes/",
    	        theme_name: "default",
    	        animation: 200
	        },
	        callback:
	        {
                onselect: function(node, tree_obj)
                {
                    cms_ajax_content_select(node.id);

                },
                onmove: function(node, ref_node, type, tree_obj, rollback) 
                {
                    tree_obj.lock(true);
                    cms_ajax_call("content_move_new", [node.id, ref_node.id, type],
                    {
                        success: function (data, textStatus)
                        {
                            successful = false;
                            cms_ajax_callback(data);
                            if (!successful)
                            {
                                $.tree_rollback(rollback);
                            }
                            this;
                        },
                        complete: function(XMLHttpRequest, textStatus)
                        {
                            tree_obj.lock(false);
                            this;
                        }
                    }); 
                }
	        },
	        cookies:
	        {
	            prefix: "pagetree",
	            opts:
	            {
	                path : '/'
	            }
	        } 
	    }
	);
});
</script>
{/literal}
{*
<div class="pageoverflow">
  <p class="pageoptions">
    {if $add_pages eq true}
      <a href="addcontent.php{$urlext}" class="pageoptions">{$newobject_image}</a>
      <a class="pageoptions" href="addcontent.php{$urlext}">{lang string='addcontent'}</a>
    {/if}
    {if $modify_page_structure eq true or $check_modify_all eq true}
      &nbsp;&nbsp;&nbsp;<a href="listcontent.php{$urlext}&error=jsdisabled" class="pageoptions" onclick="cms_ajax_reorder_display_list();return false;">{$reorder_image}</a>
      &nbsp;<a href="listcontent.php{$urlext}&error=jsdisabled" class="pageoptions" onclick="cms_ajax_reorder_display_list();return false;">{lang string='reorderpages'}</a>
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
        <th>{lang string='owner'}</th>
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
      <a href="editcontent.php{$urlext}" class="pageoptions">{$newobject_image}</a>
      <a class="pageoptions" href="addcontent.php{$urlext}">{lang string='addcontent'}</a>
    {/if}
    <a style="margin-left: 10px;" href="listcontent.php{$urlext}&expandall=1" onclick="cms_ajax_content_expandall(); return false;">{$expandall_image}</a>
    <a href="listcontent.php{$urlext}&expandall=1" onclick="cms_ajax_content_expandall(); return false;">{lang string='expandall'}</a>
    <a style="margin-left: 10px;" href="listcontent.php{$urlext}&collapseall=1" onclick="cms_ajax_content_collapseall(); return false;">{$collapseall_image}</a>
    <a href="listcontent.php{$urlext}&collapseall=1" onclick="cms_ajax_content_collapseall(); return false;">{lang string='contractall'}</a>
    {if $modify_page_structure eq true or $check_modify_all eq true}
      &nbsp;&nbsp;&nbsp;<a href="listcontent.php{$urlext}&error=jsdisabled" class="pageoptions" onclick="cms_ajax_reorder_display_list();return false;">{$reorder_image}</a>
      &nbsp;<a href="listcontent.php{$urlext}&error=jsdisabled" class="pageoptions" onclick="cms_ajax_reorder_display_list();return false;">{lang string='reorderpages'}</a>
    {/if}
  </p>
</div>
*}
