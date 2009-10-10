<div class="pagecontainer">

  <div class="pageoverflow">
    {$header_name}
  </div>
  
  <div class="pageoverflow pageoptions">
    <div class="multiselect" style="margin-top: 0; float: right; text-align: right; display: none;">
        {if count($bulk_content_ops)}
        With Selected:&nbsp;&nbsp;<select name="multiaction">
            {html_options values=$bulk_content_ops options=$bulk_content_ops}
        </select>
        <input type="submit" value="Submit"/>
        {/if}
    </div>

    <a href="{$addcontent_url}{$urlext}" title="{lang string='addcontent'}">{$newobject_image} {lang string='addcontent'}</a>
    <a href="{$thisurl}#" onclick="$.tree.reference('#content_tree').open_all(); return false;" title="{lang string='expandall'}">{$expandall_image} {lang string='expandall'}</a>
    <a href="{$thisurl}#" onclick="$.tree.reference('#content_tree').close_all(); return false;" title="{lang string='contractall'}">{$collapseall_image} {lang string='contractall'}</a>
  </div>
  
  <div id="contentlist" style="float: left;">
    <br clear="both" />
    {$content_list}
  </div>
  
  <div id="contentsummary" style="float: right; width: 500px;">
    Nothing selected
  </div>
  
  <br clear="both" />

<div class="pageoverflow pageoptions">
    <div class="multiselect" style="margin-top: 0; float: right; text-align: right; display: none;">
        {if count($bulk_content_ops)}
        With Selected:&nbsp;&nbsp;<select name="multiaction">
            {html_options values=$bulk_content_ops options=$bulk_content_ops}
        </select>
        <input type="submit" value="Submit"/>
        {/if}
    </div>
  <a href="{$addcontent_url}{$urlext}" title="{lang string='addcontent'}">{$newobject_image} {lang string='addcontent'}</a>
  <a href="{$thisurl}#" onclick="$.tree.reference('#content_tree').open_all(); return false;" title="{lang string='expandall'}">{$expandall_image} {lang string='expandall'}</a>
  <a href="{$thisurl}#" onclick="$.tree.reference('#content_tree').close_all(); return false;" title="{lang string='contractall'}">{$collapseall_image} {lang string='contractall'}</a>
</div>
</div>

<p class="pageback"><a class="pageback" href="{$back_url}">&#171; {lang string='back'}</a></p>
