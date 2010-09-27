<ul class="sortableList">
{foreach from=$list item='child'}
{strip}
  {assign var='obj' value=$child->getContent()}
  <li id="page_{$obj->Id()}"><div class="label">{$obj->Name()} <em>({$obj->MenuText()})</em></div>
  {if $child->has_children()}
    {include file="ordercontent_tree.tpl" list=$child->getChildren() depth=$depth+1 tree=''}
  {/if}
  </li>
{/strip}
{/foreach}
</ul>