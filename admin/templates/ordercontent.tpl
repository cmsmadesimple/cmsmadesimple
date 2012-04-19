<div class="pagecontainer">
{$showheader}
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

$('ul.sortable').nestedSortable({	
					disableNesting: 'no-nest',
					forcePlaceholderSize: true,
					handle: 'div',
					items: 'li',
					opacity: .6,
					placeholder: 'placeholder',
					tabSize: 25,
					tolerance: 'pointer',
					listType: 'ul',
					toleranceElement: '> div'
				});

     
jQuery('[name=submit]').live('click',function(){
    var tree = $.toJSON(parseTree($('ul.sortable')));
    var ajax_res = false;
    $.ajax({
      type: 'POST',
      url:  ajax_url,
      data: {data: tree},
      cache: false,
      async: false,
      success: function(res) {
         ajax_res = true;
      }
    });
    return ajax_res;
});//end save

});//end ready
{/literal}
</script>
<form action="ordercontent.php{$urlext}" method="post">
<div class="pageoverflow">
 <input type="submit" name="submit" class="button save" value="{'submit'|lang}"/>
 <input type="submit" name="cancel" value="{'cancel'|lang}"/>
 <input type="submit" name="revert" value="{'revert'|lang}"/>
</div>

<div class="reorder-pages">
{include file="ordercontent_tree.tpl" list=$tree->getChildren(false,true) depth=1}
</div>

<div class="pageoverflow">
 <input type="submit" name="submit" class="button save" value="{'submit'|lang}"/>
 <input type="submit" name="cancel" value="{'cancel'|lang}"/>
 <input type="submit" name="revert" value="{'revert'|lang}"/>
</div>
</form>
</div>
