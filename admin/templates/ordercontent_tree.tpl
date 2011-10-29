<ul class="sortableList {if $depth==1}sortable{/if}">
{foreach from=$list item='child'}
{strip}
  {assign var='obj' value=$child->getContent(false,true,false)}
  {if is_object($obj)}
  <li id="page_{$obj->Id()}">
    <div class="label" {if !$obj->Active()}style="color: red;"{/if}>
      <span>&nbsp;</span>{$obj->Hierarchy()}:&nbsp;{$obj->Name()}{if !$obj->Active()}&nbsp;({'inactive'|lang}){/if} <em>({$obj->MenuText()})</em>
    </div>
  {if $child->has_children()}
    {include file="ordercontent_tree.tpl" list=$child->getChildren(false,true) depth=$depth+1 tree=''}
  {/if}
  </li>
  {/if}
{/strip}
{/foreach}
</ul>