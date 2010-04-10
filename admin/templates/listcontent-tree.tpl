<div id="content_tree" class="content_tree">
    {if $content->has_children()}
        <ul>
            {include file='listcontent-entries.tpl' content=$content->get_children()}
        </ul>
    {/if}
</div>
{literal}
<script type="text/javascript">
function update_bulk_actions(tree_obj)
{
    selected = tree_obj.selected_arr;
    if (selected.length > 0)
    {
        $(".multiselect:hidden").each(
            function ()
            {
                $(this).fadeIn("slow");
            }
        );
    }
    else
    {
        $(".multiselect:visible").each(
            function ()
            {
                $(this).fadeOut("slow");
            }
        );
    }
}
function submit_bulk_actions(tree_obj)
{
    $(".selectedvals").each(
        function()
        {
            $(this).val("");
        }
    );
    $.each(
        $.tree.reference('#content_tree').selected_arr,
        function()
        {
            var the_id = this.attr('id');
            $(".selectedvals").each(
                function()
                {
                    $(this).val($.trim($(this).val() + " " + the_id));
                }
            );
        }
    );
}
$(function () {
	$("#content_tree").tree(
	    {
	        rules:
	        {
                draggable : "all",
                multiple : "alt",
                drag_copy : false
	        },
	        ui:
	        {
    	        animation: 200
	        },
	        callback:
	        {
                onchange: function(node, tree_obj)
                {
                    selected = tree_obj.selected_arr;
                    if (selected.length > 1)
                        cms_ajax_content_select("multiple");
                    else if (selected.length == 0)
                        cms_ajax_content_select("none");
                    else
                        cms_ajax_content_select(selected[0].attr('id'));
                    update_bulk_actions(tree_obj);
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
            plugins : {
                cookie : { prefix : "pagetree" },
                metadata : {},
                hotkeys : {},
                /* contextmenu : { items : { remove : { action: function(NODE, TREE_OBJ) { if(confirm("?")) TREE_OBJ.remove(NODE); } } } }, */
                checkbox : { three_state : true },
                themeroller : { }
            }
	    }
	);
});
$(document).ready(function () {
    update_bulk_actions($.tree.reference('#content_tree'));
});
</script>
{/literal}
