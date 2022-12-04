<script type="text/javascript">
function parseTree(ul)
{
  var tags = [];
  ul.children('li').each(function(){
     var subtree = $(this).children('ul');
     if( subtree.size() > 0 ) {
       tags.push([$(this).attr('id'), parseTree(subtree)]);
     } else {
       tags.push($(this).attr('id'));
     }
  });
  return tags;
}

$(document).ready(function(){
  $(document).on('click','[name={$actionid}submit]',function(){
    var tree = $.toJSON(parseTree($('ul.sortable')));
    $('#submit_data').val(tree);
  });

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
  })
});
</script>

{function category_tree parent=-1 depth=1}{strip}
<ul{if $depth==1} class="sortableList sortable"{/if}>
{foreach $allcats as $cat}
  {if $cat.parent_id == $parent}
  <li id="cat_{$cat.news_category_id}">
    <div class="label">{$cat.news_category_name}</div>
    {category_tree parent=$cat.news_category_id depth=$depth+1}
  </li>
  {/if}
{/foreach}
</ul>
{/strip}{/function}

<h3>{$mod->Lang('reorder_categories')}</h3>
<div class="information">{$mod->Lang('info_reorder_categories')}</div>
{category_tree}

{form_start id="reorder_form"}
<input type="hidden" name="{$actionid}submit_type" id="submit_type" value=""/>
<input type="hidden" name="{$actionid}data" id="submit_data" value=""/>
<div class="pageoverflow">
  <p class="pagetext"></p>
  <p class="pageinput">
    <input type="submit" name="{$actionid}submit" value="{$mod->Lang('submit')}"/>
    <input type="submit" name="{$actionid}cancel" value="{$mod->Lang('cancel')}"/>
  </p>
</div>
{form_end}