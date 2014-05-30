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

  {if $logempty==false}

    <div class="pageoptions">
    <div style="float: left; width: 49%;">
    <a id="toggle_filters">{admin_icon icon='view.gif' alt="{$langshowfilters}"} {$langshowfilters}</a>
    {if isset($downloadlink)}
      <a href="adminlog.php{$urlext}&amp;download=1">{$downloadlink}</a>
      <a href="adminlog.php{$urlext}&amp;download=1">{$langdownload}</a>
    {/if}
    {if $clearicon != ''}
      &nbsp;
      <a href="adminlog.php{$urlext}&amp;clear=true">{$clearicon}</a>
      <a href="adminlog.php{$urlext}&amp;clear=true" onclick="return confirm('{$sysmain_confirmclearlog}')">{$langclear}</a>
    {/if}
    </div>
    <div style="text-align: right;">{$pagestring}</div>
    </div>

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
    <p>{$langlogempty}</p>
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
          <a class="pageoptions" href="adminlog.php{$urlext}&amp;clear=true" onclick="return confirm('{$sysmain_confirmclearlog}')">{$langclear}</a>
        </p>
      </div>
    </td></tr>
  {/if}
  </table>


</div>