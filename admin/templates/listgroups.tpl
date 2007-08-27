DEBUG of tr: {tr}blah blah blah{/tr}

<div class="pagecontainer">
  <div class="pageoverflow">
    {$header_name}
  </div><!-- pageoverflow -->

  <div id="grouplist">
    {include file='listgroups-list.tpl'}
    {* $group_list delete-me *}
  </div><!-- grouplist -->
</div><!-- pagecontainer -->

<p class="pageback"><a class="pageback" href="{$back_url}">&#171; {lang string='back'}</a></p>
