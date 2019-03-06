<script>
function parseTree(ul)
{
  var tags = {}
  ul.children('li').each(function(){
     var subtree = $(this).children('ul')
     var id = $(this).attr('id')
     if( subtree.length > 0 ) {
       tags[id] = parseTree(subtree);
     } else {
       tags[id] = '--';
     }
  });
  return tags;
}

$(function(){
   var form = $('#reorder_form')
   var el = $('ul.sortable').first();

   form.submit(function(ev){
      var tree = JSON.stringify(parseTree(el));
      $('[name=submit_data]',form).val(tree);
   })

   $(el).nestedSortable({
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
   })
})
</script>

{function category_tree depth=1}{strip}
   <ul class="depth-{$depth} {if $depth==1}sortableList sortable{/if}">
       {foreach $cat_list as $cat}
          <li id="cat_{$cat.id}">
	     <div class="label">{$cat.name} {if $cat.alias}<span class="alias" style="color: red;">({$cat.alias})</span>{/if}</div>
	     {if !empty($cat.children) && count($cat.children)}
	         {category_tree cat_list=$cat.children depth=$depth+1}
	     {/if}
	  </li>
       {/foreach}
   </ul>
{/strip}{/function}

<h3>{$mod->Lang('reorder_categories')}</h3>
<div class="information" style="width: 95%;">{$mod->Lang('info_reorder_categories')}</div>

{category_tree cat_list=$category_tree}

{form_start id="reorder_form"}
<div class="c_full cf">
   <input type="hidden" name="submit_data"/>
   <input type="submit" name="submit" value="{$mod->Lang('submit')}"/>
   <input type="submit" name="cancel" value="{$mod->Lang('cancel')}"/>
</div>
{form_end}