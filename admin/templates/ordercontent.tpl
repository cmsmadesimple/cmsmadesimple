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
					toleranceElement: '> div'
				});

     
$(".save").click(function(){
          var tree = $.toJSON(parseTree($('ul#content_tree')));
          //alert(tree);
          //return false;
       
    	
    $.post(ajax_url, {data: tree}, function(res){
           	     if( res != '' ) 
           	     {
           	       alert("Res is: " + res);
                   return false;
                   }
                   return true;
                  });
        
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
<ul id="content_tree" class="sortable">
  <li id="page_-1"><div>&nbsp;</div>
  {include file="ordercontent_tree.tpl" list=$tree->getChildren() depth=1}
  </li>
</ul>
</div>

<div class="pagoeverflow">
 <input type="submit" name="submit" class="button save" value="{'submit'|lang}"/>
 <input type="submit" name="cancel" value="{'cancel'|lang}"/>
 <input type="submit" name="revert" value="{'revert'|lang}"/>
</div>
</form>
</div>