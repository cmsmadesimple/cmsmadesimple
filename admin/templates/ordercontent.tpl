{literal}
<style type="text/css">
.modified { background-color: red; color: white; }
</style>
{/literal}

<script type="text/javascript">
var ajax_url = 'ordercontent.php{$urlext}';
{literal}
function parseTree(ul){
	var tags = [];
	ul.children("li").each(function(){
		var subtree =	$(this).children("ul");
		if(subtree.size() > 0)
			tags.push([$(this).attr("id"), parseTree(subtree)]);
		else
			tags.push($(this).attr("id"));
	});
	return tags;
}

$(document).ready(function(){
		
	$("li.tree_item span").droppable({
		tolerance		: "pointer",
		hoverClass		: "tree_hover",
		drop			: function(event, ui){
			var dropped = ui.draggable;
			dropped.css({top: 0, left: 0});
	                dropped.removeClass('modified');
	                dropped.addClass('modified');
			var me = $(this).parent();
			if(me == dropped)
				return;
			var subbranch = $(me).children("ul");
			if(subbranch.size() == 0) {
				me.find("span").after("<ul></ul>");
				subbranch = me.find("ul");
			}
			var oldParent = dropped.parent();
			subbranch.eq(0).append(dropped);
			var oldBranches = $("li", oldParent);
			if (oldBranches.size() == 0) { $(oldParent).remove(); }
		}
	});
	
	$("li.tree_item").draggable({
		opacity: 0.5,
		revert: true
	});
	
	$(".save").click(function(){
          var tree = $.toJSON(parseTree($('#content_tree')));
          $.post(ajax_url, {data: tree}, function(res){
	     if( res != '' ) 
	     {
	       alert(res);
               return false;
             }
             return true;
          });
	});

	
});
{/literal}
</script>

<form action="ordercontent.php{$urlext}" method="post">
<div class="pagoeverflow">
 <input type="submit" name="submit" class="button save" value="{'submit'|lang}"/>
 <input type="submit" name="cancel" value="{'cancel'|lang}"/>
</div>

<div>
<ul id="content_tree" class="sortableList">
  <li id="page_-1" class="tree_item"><span>{'root'|lang}</span>
  {include file="ordercontent_tree.tpl" list=$tree->getChildren() depth=1}
  </li>
</ul>
</div>

<div class="pagoeverflow">
 <input type="submit" name="submit" class="button save" value="{'submit'|lang}"/>
 <input type="submit" name="cancel" value="{'cancel'|lang}"/>
</div>
</form>