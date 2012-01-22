<div class="pagecontainer">
{$header}
{'info_pagedefaults'|lang}

<form id="pagedefaultsform" method="post" action="pagedefaults.php">
  <input type="hidden" name="editpagedefaults" value="true"/>
  <input type="hidden" name="{$CMS_SECURE_PARAM_NAME}" value="{$CMS_KEY}"/>

  <div class="pageoverflow">
    <p class="pagetext"></p>
    <p class="pageinput">
      <input type="submit" name="submit" value="{'submit'|lang}" class="pagebutton" />
      <input type="submit" name="cancel" value="{'cancel'|lang}" class="pagebutton" />
    </p>
  </div>

<div class="pageoverflow">
  <p class="pagetext">{'default_contenttype'|lang}:</p>
  <p class="pageinput">
    <select name="default_contenttype">
      {html_options options=$all_contenttypes selected=$default_contenttype}
    </select>
    <br/>
    {'info_default_contenttype'|lang}
  </p>
</div>

  <div class="pageoverflow">
    <p class="pagetext">{'active'|lang}:</p>
    <p class="pageinput">
      <input class="pagenb" type="checkbox" name="page_active" {if $page_active==1}checked="checked"{/if}/>
    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">{'secure_page'|lang}:</p>
    <p class="pageinput">
      <input class="pagenb" type="checkbox" name="page_secure" {if $page_secure==1}checked="checked"{/if}/>
    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">{'showinmenu'|lang}:</p>
    <p class="pageinput">
      <input class="pagenb" type="checkbox" name="page_showinmenu" {if $page_showinmenu==1}checked="checked"{/if}/>
    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">{'cachable'|lang}:</p>
    <p class="pageinput">
      <input class="pagenb" type="checkbox" name="page_cachable" {if $page_cachable==1}checked="checked"{/if}/>
    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">{'metadata'|lang}:</p>
    <p class="pageinput">
      <textarea class="pagesmalltextarea" name="page_metadata" cols="80" rows="20">{$page_metadata}</textarea>
    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">{'content'|lang}:</p>
    <p class="pageinput">
      <textarea class="pagesmalltextarea" name="page_defaultcontent" cols="80" rows="20">{$page_defaultcontent}</textarea>
    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">{'searchable'|lang}:</p>
    <p class="pageinput">
      <input class="pagenb" type="checkbox" name="page_searchable" {if $page_searchable==1}checked="checked"{/if}/>
    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">{'additionaleditors'|lang}:</p>
    <p class="pageinput">
      {$input_additional_editors}
    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">{'extra1'|lang}:</p>
    <p class="pageinput">
      <input class="pagenb" type="text" name="page_extra1" size="50" maxlength="255" value="{$page_extra1}"/>
    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">{'extra2'|lang}:</p>
    <p class="pageinput">
      <input class="pagenb" type="text" name="page_extra2" size="50" maxlength="255" value="{$page_extra2}"/>
    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext">{'extra3'|lang}:</p>
    <p class="pageinput">
      <input class="pagenb" type="text" name="page_extra3" size="50" maxlength="255" value="{$page_extra3}"/>
    </p>
  </div>

  <div class="pageoverflow">
    <p class="pagetext"></p>
    <p class="pageinput">
      <input type="submit" name="submit" value="{'submit'|lang}" class="pagebutton" />
      <input type="submit" name="cancel" value="{'cancel'|lang}" class="pagebutton" />
    </p>
  </div>

</form>
</div>{* pagecontainer *}

<p class="pageback"><a class="pageback" href="{$backurl}">&#171; {'back'|lang}</a></p>