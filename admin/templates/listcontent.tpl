<div class="pagecontainer">

  <div class="pageoverflow">
    {$header_name}
  </div>
  
  <div class="pageoverflow pageoptions">
    <div class="multiselect" style="margin-top: 0; float: right; text-align: right; display: none;">
        {if count($bulk_content_ops)}
        <form action="multicontent.php" method="post" onsubmit="submit_bulk_actions($.tree.reference('#content_tree')); return true;">
        With Selected:&nbsp;&nbsp;<select name="multiaction">
            {html_options values=$bulk_content_ops options=$bulk_content_ops}
        </select>
        <input class="selectedvals" type="hidden" name="selectedvals" value="" />
        <input type="hidden" name="{$secure_name}" value="{$secure_key}" />
        <input type="submit" value="Submit"/>
        </form>
        {/if}
    </div>

    <a href="{$addcontent_url}{$urlext}" title="{lang string='addcontent'}" onclick='cms_ajax_content_new(); return false;'>{$newobject_image} {lang string='addcontent'}</a>
    <a href="{$thisurl}#" onclick="$.tree.reference('#content_tree').open_all(); return false;" title="{lang string='expandall'}">{$expandall_image} {lang string='expandall'}</a>
    <a href="{$thisurl}#" onclick="$.tree.reference('#content_tree').close_all(); return false;" title="{lang string='contractall'}">{$collapseall_image} {lang string='contractall'}</a>
  </div>
  
  <div id="contentlist" style="float: left; height: 350px;">
    {$content_list}
  </div>
  
  <div id="contentsummary" style="float: right; width: 700px;">
    Nothing selected
  </div>
  
  <br clear="both" />

<div class="pageoverflow pageoptions">
    <div class="multiselect" style="margin-top: 0; float: right; text-align: right; display: none;">
        {if count($bulk_content_ops)}
        <form action="multicontent.php" method="post" onsubmit="submit_bulk_actions($.tree.reference('#content_tree')); return true;">
        With Selected:&nbsp;&nbsp;<select name="multiaction">
            {html_options values=$bulk_content_ops options=$bulk_content_ops}
        </select>
        <input class="selectedvals" type="hidden" name="selectedvals" value="" />
        <input type="hidden" name="{$secure_name}" value="{$secure_key}" />
        <input type="submit" value="Submit"/>
        </form>
        {/if}
    </div>
  <a href="{$addcontent_url}{$urlext}" title="{lang string='addcontent'}" onclick='cms_ajax_content_new(); return false;'>{$newobject_image} {lang string='addcontent'}</a>
  <a href="{$thisurl}#" onclick="$.tree.reference('#content_tree').open_all(); return false;" title="{lang string='expandall'}">{$expandall_image} {lang string='expandall'}</a>
  <a href="{$thisurl}#" onclick="$.tree.reference('#content_tree').close_all(); return false;" title="{lang string='contractall'}">{$collapseall_image} {lang string='contractall'}</a>
</div>
</div>

<p class="pageback"><a class="pageback" href="{$back_url}">&#171; {lang string='back'}</a></p>
