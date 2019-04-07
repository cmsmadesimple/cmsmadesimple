{function pressroom_show_categories depth=0}
<ul class="pressroom-categorylist depth-{$depth}">
   {foreach $categories as $onecat}
   <li class="pressroom-category">
         <a href="{cms_action_url action=default category_id=$onecat.id withchildren=1}">{$onecat.name}</a>
         {if !empty($onecat.children) && $depth < $maxdepth}
             {pressroom_show_categories categories=$onecat.children depth=$depth+1}
         {/if}
   </li>
   {/foreach}
</ul>
{/function}

{pressroom_show_categories}