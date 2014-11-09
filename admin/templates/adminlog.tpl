<script type="text/javascript">
$(document).ready(function(){
  $('#toggle_filters').click(function(){
    $('#adminlog_filters').dialog({
      modal: true,
      width: 'auto'
    });
  });
})
</script>

<div class="pagecontainer">
  <div class="pageoverflow">
    {if $filteruser != '' or $filteraction != ''}
    <fieldset>
      <legend>{'filterapplied'|lang}:</legend>
      {if $filteruser != ''}<p class="pageinput">{'user'|lang} = {$filteruser}</p>{/if}
      {if $filteraction != ''}<p class="pageinput">{'actioncontains'|lang}: {$filteraction}</p>{/if}
    </fieldset>
    {/if}

    <div id="adminlog_filters" style="display: none;" title="{$langfilters}">
        <form id="adminlog_filter" method="post" action="adminlog.php?{$SECURE_PARAM_NAME}={$CMS_USER_KEY}">
          <div class="pageoverflow">
            <p class="pagetext">{$langfilteruser}</p>
            <p class="pageinput"><input type="text" name="filteruser" value="{$filteruservalue}"/></p>
          </div>
          <div class="pageoverflow">
            <p class="pagetext">{$langfilteraction}</p>
            <p class="pageinput"><input type="text" name="filteraction" value="{$filteractionvalue}"/></p>
          </div>
          <div class="pageoverflow">
            <p class="pagetext"></p>
            <p class="pageinput">
	      <input type="submit" name="filterapply" value="{lang('apply')}"/>
	      <input type="submit" name="filterreset" value="{lang('filterreset')}"/>
	    </p>
          </div>
        </form>
    </div>

    <div class="c_full">
      <div class="grid_6">
        <a id="toggle_filters">{admin_icon icon='view.gif' alt="{$langshowfilters}"} {$langshowfilters}</a>
        {if isset($downloadlink)}
          <a href="adminlog.php{$urlext}&amp;download=1">{$downloadlink}</a>&nbsp;
          <a href="adminlog.php{$urlext}&amp;download=1">{$langdownload}</a>
        {/if}
        {if $clearicon != ''}
          &nbsp;
          <a href="adminlog.php{$urlext}&amp;clear=true">{$clearicon}</a>
          <a href="adminlog.php{$urlext}&amp;clear=true" onclick="return confirm('{$sysmain_confirmclearlog|escape:'javascript'}')">{$langclear}</a>
        {/if}
      </div>
      {if isset($pagestring)}<div class="grid_6" style="text-align: right;">{$pagestring}</div>{/if}
      <div class="clear"></div>
  </div>

  {if $logempty==false}
    <table class="pagetable">
      <thead>
      <tr>
        <th>{'ip_addr'|lang}</th>
        <th>{$languser}</th>
        <th>{$langitemid}</th>
        <th>{$langitemname}</th>
        <th>{$langaction}</th>
        <th>{$langdate}</th>
      </tr>
      </thead>
      <tbody>
        {foreach from=$loglines item='line'}
          {cycle values='row1,row2' assign='currow'}
        <tr class="{$currow}">
          <td>{$line.ip_addr|default:''}</td>
          <td>{$line.username}</td>
          <td>{$line.itemid}</td>
          <td>{$line.itemname}</td>
          <td>{$line.action}</td>
          <td>{$line.date}</td>
        </tr>
        {/foreach}

      </tbody>
    </table>
  {else}
    <h3 style="text-align: center; color: red;">{$langlogempty}</h3>
  {/if}

  </div>


  <table>
  <tr>
    <td>
      <div class="pageoptions">
        <p class="pageoptions">
	{if isset($downloadlink)}
          <a href="adminlog.php{$urlext}&amp;download=1">{$downloadlink}</a>
          <a href="adminlog.php{$urlext}&amp;download=1">{$langdownload}</a>
        {/if}
        </p>
      </div>
    </td>
  {if $clearicon!=""}
    <td>
      <div class="pageoptions">
        <p class="pageoptions">
          <a href="adminlog.php{$urlext}&amp;clear=true">{$clearicon}</a>
          <a class="pageoptions" href="adminlog.php{$urlext}&amp;clear=true" onclick="return confirm('{$sysmain_confirmclearlog|escape:'javascript'}')">{$langclear}</a>
        </p>
      </div>
    </td></tr>
  {/if}
  </table>


</div>