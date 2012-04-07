<div class="pagecontainer">
{$theme->StartTabHeaders()}

{$theme->SetTabHeader('database',lang('sysmaintab_database'),isset($active_database))}
{$theme->SetTabHeader('content',lang('sysmaintab_content'),isset($active_content))}
{$theme->SetTabHeader('changelog',lang('sysmaintab_changelog'),isset($active_changelog))}

{$theme->EndTabHeaders()}

{$theme->StartTabContent()}

{$theme->StartTab('database')}
    <form action="{$formurl}" method="post">
      <fieldset>
        <legend>{'sysmain_database_status'|lang}:&nbsp;</legend>
        {$tablecount} tables found (out of which {$nonseqcount} are not seq-tables)
        <br/>
        {if $errorcount==0}
        No structural errors were detected in the database
        {else}
        {$errorcount} structural error{if $errorcount>1}s{/if} were detected in these tables: {$errortables}
        {/if}

        <div class="pageoverflow">
          <p class="pagetext">{'sysmain_optimizetables'|lang}:</p>

          <p class="pageinput">
            <input class="pagebutton" type="submit" name="optimizeall" value="{'sysmain_optimize'|lang}"/>
          </p>
        </div>
        <div class="pageoverflow">
          <p class="pagetext">{'sysmain_repairtables'|lang}:</p>

          <p class="pageinput">
            <input class="pagebutton" type="submit" name="repairall" value="{'sysmain_repair'|lang}"/>
          </p>
        </div>


      </fieldset>
        </form>


  </form>

{$theme->EndTab()}

{$theme->StartTab('content')}
  <form action="{$formurl}" method="post" onsubmit="return confirm('{'sysmain_confirmclearcache'|lang}')" >
  <fieldset>
    <legend>{'sysmain_cache_status'|lang}&nbsp;</legend>
    <div class="pageoverflow">
      <p class="pagetext">{'clearcache'|lang}:</p>

      <p class="pageinput">
        <input class="pagebutton" type="submit" name="clearcache" value="{'clear'|lang}"/>
      </p>
    </div>
  </fieldset>
    </form>
  <fieldset>
    <legend>{'sysmain_content_status'|lang}&nbsp;</legend>
    <form action="{$formurl}" method="post" onsubmit="return confirm('{'sysmain_confirmupdatehierarchy'|lang}')" >
    {$pagecount} {'sysmain_pagesfound'|lang}

    <div class="pageoverflow">
      <p class="pagetext">{'sysmain_updatehierarchy'|lang}:</p>

      <p class="pageinput">
        <input class="pagebutton" type="submit" name="updatehierarchy" value="{'sysmain_update'|lang}"/>
      </p>
    </div>
    </form>

    <form action="{$formurl}" method="post" onsubmit="return confirm('{'sysmain_confirmupdateurls'|lang}')" >
    <div class="pageoverflow">
      <p class="pagetext">{'sysmain_updateurls'|lang}:</p>

      <p class="pageinput">
        <input class="pagebutton" type="submit" name="updateurls" value="{'sysmain_update'|lang}"/>
      </p>
    </div>
    </form>

    {if $withoutaliascount!="0"}
    <form action="{$formurl}" method="post" onsubmit="return confirm('{'sysmain_confirmfixaliases'|lang}')" >
    <div class="pageoverflow">
       <p class="pagetext">{$withoutaliascount} {'sysmain_pagesmissinalias'|lang}:</p>

       <p class="pageinput">
            {foreach from=$pagesmissingalias item='page'}
              {*{$page.count}.*} {$page.content_name}<br/>
            {/foreach}
            <br/>
            <input class="pagebutton" type="submit" name="addaliases" value="{'sysmain_fixaliases'|lang}"/>
          </p>
        </div>
    </form>
    {/if}
    {if $invalidtypescount!="0"}
    <form action="{$formurl}" method="post" onsubmit="return confirm('{'sysmain_confirmfixtypes'|lang}')" >
    <div class="pageoverflow">
          <p class="pagetext">{$invalidtypescount} {'sysmain_pagesinvalidtypes'|lang}:</p>

          <p class="pageinput">
            {foreach from=$pageswithinvalidtype item='page'}
              {*{$page.count}.*} {$page.content_name} ({$page.content_alias})<br/>
            {/foreach}
            <br/>
            <input class="pagebutton" type="submit" name="fixtypes" value="{'sysmain_fixtypes'|lang}"/>
          </p>
        </div>
    </form>
    {/if}

  {if $invalidtypescount=="0" && $withoutaliascount==""}
    <br/>
    {'sysmain_nocontenterrors'|lang}
  {/if}

  </fieldset>
    </form>


{$theme->EndTab()}


{* changelog tab *}
{$theme->StartTab('changelog')}

{$changelogfilename}

  <br><br>

  <tt>{$changelog}</tt>


{$theme->EndTab()}

{$theme->EndTabContent()}
</form>

<p class="pageback"><a class="pageback" href="{$backurl}">&#171; {lang('back')}</a></p>

</div> {*pagecontainer*}

