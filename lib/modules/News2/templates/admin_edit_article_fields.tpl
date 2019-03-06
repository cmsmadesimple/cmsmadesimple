<style>
.related-item-del {
   cursor: pointer;
   color: red;
   font-weight: bold;
   margin-left: 5px;
}
</style>

  {foreach $fielddef_list as $name => $onedef}
     {* note: can still do custom rendering here using the name and type members *}
     {* note: value is available via $article->fieldVal($name) *}
     {$type=$fieldtypes[$onedef->type]}
     {if $type}
        {$type->renderForArticle($onedef, $article->fieldVal($onedef->name))}
     {/if}
  {/foreach}
