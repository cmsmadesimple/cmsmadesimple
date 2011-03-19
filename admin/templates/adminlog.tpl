<div class="pagecontainer">
  <div class="pageoverflow">
  {$header}
  {if $logempty==false}
    <p class="pageshowrows">{$pagestring}</p>

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

    <table cellspacing="0" class="pagetable">
      <thead>
      <tr>
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