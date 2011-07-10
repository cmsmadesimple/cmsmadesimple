<div class="pagecontainer">
  <div class="pageoverflow">
  {$header}
    <input type="checkbox" id="toggle_filters" {if $filterdisplay=="block"}checked="1"  {/if}
           onClick="if (this.checked) document.getElementById('adminlog_filters').style.display = 'block'; else document.getElementById('adminlog_filters').style.display = 'none';"/>
    <label for="toggle_filters">{$langshowfilters}</label>

    <div id="adminlog_filters" style="display: {$filterdisplay}">
      <fieldset>
        <legend>{$langfilters}</legend>
        <form id="adminlog_filter" method="post" action="adminlog.php">
          <input type="hidden" name="{$SECURE_PARAM_NAME}" value="{$CMS_USER_KEY}"/>

          <div class="pageoverflow">
  <p class="pagetext">{$langfilteruser}</p>
  <p class="pageinput"><input type="text" name="filteruser" value="{$filteruservalue}"/></p>
</div>
{*
<div class="pageoverflow">
  <p class="pagetext">{$langfiltername}</p>
  <p class="pageinput"><input type="text" name="filtername" value="{$filternamevalue}"/></p>
</div>
*}
          <div class="pageoverflow">
  <p class="pagetext">{$langfilteraction}</p>
  <p class="pageinput"><input type="text" name="filteraction" value="{$filteractionvalue}"/></p>
</div>
          <div class="pageoverflow">
  <p class="pagetext"></p>
  <p class="pageinput"><input type="submit" name="filterapply" value="{$langfilterapply}"/><input type="submit" name="filterreset"
                                                                                     value="{$langfilterreset}"/></p>
</div>

        </form>
      </fieldset>
    </div>

  {if $logempty==false}



    <p class="pageshowrows">{$pagestring}</p>

    <table>
    <tr>
      {if isset($downloadlink)}
      <td>
        <div class="pageoptions">
          <p class="pageoptions">
            <a href="adminlog.php{$urlext}&amp;download=1">
              {$downloadlink}
            </a>
            <a class="pageoptions" href="adminlog.php{$urlext}&amp;download=1">
              {$langdownload}
            </a>
          </p>
        </div>
      </td>
      {/if}
      {if $clearicon!=""}
        <td>
          <div class="pageoptions">
            <p class="pageoptions">
              <a href="adminlog.php{$urlext}&amp;clear=true">{$clearicon}</a>
              <a class="pageoptions" href="adminlog.php{$urlext}&amp;clear=true">{$langclear}</a>
            </p>
          </div>
        </td>
      {/if}
    </tr>
    </table>

    <table cellspacing="0" class="pagetable">
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
        <tr class="{$currow}" onmouseover="this.className='{$currow}.hover';" onmouseout="this.className='{$currow}';">
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
          <a href="adminlog.php{$urlext}&amp;download=1">
          {$downloadlink}
          </a>
          <a class="pageoptions" href="adminlog.php{$urlext}&amp;download=1">
          {$langdownload}
          </a>
        </p>
      </div>
    </td>
  {if $clearicon!=""}
    <td>
      <div class="pageoptions">
        <p class="pageoptions">
          <a href="adminlog.php{$urlext}&amp;clear=true">{$clearicon}</a>
          <a class="pageoptions" href="adminlog.php{$urlext}&amp;clear=true">{$langclear}</a>
        </p>
      </div>
    </td></tr>
  {/if}
  </table>


</div>

<p class="pageback"><a class="pageback" href="{$backurl}">&#171;{$langback}</a></p>