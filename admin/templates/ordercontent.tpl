<link href="../lib/jquery/css/smoothness/jquery-ui-1.8.4.custom.css" rel="stylesheet" type="text/css" />
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

$( "#dialog-confirm" ).dialog({
        	        autoOpen: false,
        			resizable: false,
        			height:140,
        			modal: true,
        			buttons: {
        				"Re Order now?": function() {
        					$( this ).dialog( "close" );// TODO HERE!!!
        				},
        				Cancel: function() {
        					$( this ).dialog( "close" );
        				}
        			}
		});
        

	$(".save").click(function(){
          var tree = $.toJSON(parseTree($('ul#content_tree')));
          //alert(tree);
          //return false;
       
    	
    $.post(ajax_url, {data: tree}, function(res){
           	     if( res != '' ) 
           	     {
           	       alert(res);
                   return false;
                   }
                   return true;
                  });
        

	});//end save
    
        $(".save-tmp").click(function(){      
         $( "#dialog-confirm" ).dialog( "open" );
			return false;
	    });//end save-tmp
	
});//end ready
{/literal}
</script>
<!---->
<div id="dialog-confirm" title="Re Order Content">
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>These items will be re order. Are you sure?</p>
</div>




<form action="ordercontent.php{$urlext}" method="post">
<div class="pagoeverflow">
 <input type="submit" name="submit" class="button save-tmp" value="Confirm Test"/>
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