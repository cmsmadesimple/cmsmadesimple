{function news2_show_categories depth=0}
<ul class="news2-categorylist depth-{$depth}">
   {foreach $categories as $onecat}
   <li class="news2-category">
         <a href="{cms_action_url action=default category_id=$onecat.id withchildren=1}">{$onecat.name}</a>
         {if !empty($onecat.children) && $depth < $maxdepth}
             {news2_show_categories categories=$onecat.children depth=$depth+1}
         {/if}
   </li>
   {/foreach}
</ul>
{/function}

{news2_show_categories}