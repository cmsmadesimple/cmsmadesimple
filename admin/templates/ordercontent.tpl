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
					toleranceElement: '> div',
					onChange: function(){
					  alert(' got here ');
					}
				});

     
$(".save").click(function(){
    var tree = $.toJSON(parseTree($('ul.sortable')));
    var ajax_res = false;
    //alert(tree);
    //return false;       
    $.ajax({
      type: 'POST',
      url:  ajax_url,
      data: {data: tree},
      cache: false,
      async: false,
      success: function(res) {
         if( res != '' ) {
           alert("Res is: " + res);
           ajax_res = false;
         }
         else {
           ajax_res = true;
         }
      }
    });
    return ajax_res;
});//end save

});//end ready
{/literal}
</script>
<form action="ordercontent.php{$urlext}" method="post">
<div class="pagoeverflow">
 <input type="submit" name="submit" class="button save" value="{'submit'|lang}"/>
 <input type="submit" name="cancel" value="{'cancel'|lang}"/>
 <input type="submit" name="revert" value="{'revert'|lang}"/>
</div>

<div>
{*
<ul id="content_tree">
  <li id="page_-1" class="sortable"><div>&nbsp;</div>
  {include file="ordercontent_tree.tpl" list=$tree->getChildren() depth=1}
  </li> 
</ul>
*}
{include file="ordercontent_tree.tpl" list=$tree->getChildren() depth=1}
</div>

<div class="pagoeverflow">
 <input type="submit" name="submit" class="button save" value="{'submit'|lang}"/>
 <input type="submit" name="cancel" value="{'cancel'|lang}"/>
 <input type="submit" name="revert" value="{'revert'|lang}"/>
</div>
</form>
</div>