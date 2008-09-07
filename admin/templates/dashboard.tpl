<div class="pagecontainer">
  <div class="pageoverflow">
    <div class="pageheader">{tr}Dashboard{/tr}</div>
    <div class="full-content clear-db">    
      <div class="coredashboardcontent">
    	  <div class="dashboardheader-core">
    		{tr}Core information{/tr}
    		</div>
    		<div class="dashboardcontent-core">
    		{$coreoutput}
    		</div>
    	</div>
      {foreach from=$dashitems item=dashitem}
      <div class="moduledashboardcontent"> 
       	<div class="dashboardheader">
    			{$dashitem.title}
    		</div>
    		<div class="dashboardcontent">
    			{$dashitem.content}
    		</div>
    	</div>      
      {/foreach}    
    </div>
  </div>
</div>