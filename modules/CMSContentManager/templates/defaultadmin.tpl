{if $ajax == 0}
  <script type="text/javascript">
  //<![CDATA[

      function refresh_content_list() {
          console.log('refresh content list');
          $('#content_area').css({ 'pointer-events': 'none', 'cursor': 'busy' }); // set busy
          $.ajax({
	      url: '{$ajax_get_content}',
          }).done(function(data){
              $('#content_area').html(data);
	      $('#content_area').css({ 'pointer-events': '', 'cursor': '' }); // clear busy
	  });
      }

  function cms_CMloadUrl(link, lang) {
      $(document).on('click', link, function (e) {
          var url = $(this).attr('href') + '&showtemplate=false&{$actionid}ajax=1';
          if (typeof lang == 'string' && lang.length > 0) {
              if (!confirm(lang)) return false;
	  }
	  $('#ajax_find').val('');
	  $.ajax({
	    url: url,
	  }).done(function(){
	    refresh_content_list();
	  })
          e.preventDefault();
      });
  }


  function cms_CMtoggleState(el) {
      $(el).attr('disabled', true);
      $('button' + el).button({ 'disabled' : true });

      $(document).on('click', 'input:checkbox', function () {
          if ($('input:checkbox').is(':checked')) {
              $(el).attr('disabled', false);
              $('button' + el).button({ 'disabled' : false });
          } else {
              $(el).attr('disabled', true);
              $('button' + el).button({ 'disabled' : true });
          }
     });
  }

  $(document).ready(function () {
      $('#selectall').cmsms_checkall({
          target: '#contenttable'
      });
      cms_CMtoggleState('#multiaction'),
      cms_CMtoggleState('#multisubmit'),

      // these links can't use ajax as they effect pagination.
      //cms_CMloadUrl('a.expandall'),
      //cms_CMloadUrl('a.collapseall'),
      //cms_CMloadUrl('a.page_collapse'),
      //cms_CMloadUrl('a.page_expand'),

      cms_CMloadUrl('a.page_sortup'),
      cms_CMloadUrl('a.page_sortdown'),
      cms_CMloadUrl('a.page_setinactive', '{$mod->Lang('confirm_setinactive')|escape:'javascript'}'),
      cms_CMloadUrl('a.page_setactive'),
      cms_CMloadUrl('a.page_setdefault', '{$mod->Lang('confirm_setdefault')|escape:'javascript'}'),
      cms_CMloadUrl('a.page_delete', '{$mod->Lang('confirm_delete_page')|escape:'javascript'}');

      // load the contents area.
      setInterval( function() {
         refresh_content_list();
      }, 30000);
      refresh_content_list();

      $('a.steal_lock').on('click',function(e) {
          // we're gonna confirm stealing this lock.
          var v = confirm('{$mod->Lang('confirm_steal_lock')|escape:'javascript'}');
          $(this).data('steal_lock',v);
          if( v ) {
              var url = $(this).attr('href');
              url = url + '{$actionid}steal=1';
              $(this).attr('href',url);
          }
      });

      $('a.page_edit').on('click',function(event) {
          var v = $(this).data('steal_lock');
          $(this).removeData('steal_lock');
          if( typeof(v) != 'undefined' && v != null && !v ) return false;
          if( typeof(v) == 'undefined' || v != null ) return true;

          // do a double check to see if this page is locked or not.
          var content_id = $(this).attr('data-cms-content');
          var url = '{$admin_url}/ajax_lock.php?showtemplate=false';
          var opts = { opt: 'check', type: 'content', oid: content_id };
          var ok = false;
          opts[cms_data.secure_param_name] = cms_data.user_key;
          $.ajax({
              url: url,
              data: opts,
              success: function(data,textStatus,jqXHR) {
             }
          }).done(data,function(){
              if( data.status == 'success' ) {
                  if( data.locked ) {
                      // gotta display a message.
	              alert('{$mod->Lang('error_contentlocked')|escape:'javascript'}');
		      event.preventDefault();
                  }
              }
	  });
      });

      $(document).on('click', '#myoptions', function () {
          $('#useroptions').dialog({
              resizable: false,
              buttons: {
                  '{$mod->Lang('submit')|escape:'javascript'}': function () {
                      $(this).dialog('close');
                      $('#myoptions_form').submit();
                  },
                  '{$mod->Lang('cancel')|escape:'javascript'}': function () {
                      $(this).dialog('close');
                  },
              }
          });
      });

      $('#ajax_find').keypress(function (e) {
          if (e.which == 13) e.preventDefault();
      });

      $('#ajax_find').autocomplete({
          source: '{cms_action_url action=admin_ajax_pagelookup forjs=1}&showtemplate=false',
          minLength: 2,
          position: {
              my: "right top",
              at: "right bottom"
          },
          select: function (event, ui) {
              $(this).val(ui.item.label);
              var url = '{cms_action_url action=defaultadmin forjs=1}&showtemplate=false&{$actionid}ajax=1&{$actionid}seek=' + ui.item.value;
              $('#contenttable').load(url + ' #contenttable > *');
              event.preventDefault();
          }
      });

      // go to page on option change
      $(document).on('change', '#{$actionid}curpage', function () {
          $(this).closest('form').submit();
      })

      $(document).ajaxComplete(function () {
      	  $('#selectall').cmsms_checkall();
          $('tr.selected').css('background', 'yellow');
      });

      $('a#ordercontent').click(function(e){
          var have_locks = {$have_locks};
          if( !have_locks ) {
              // double check to see if anything is locked
              var content_id = $(this).attr('data-cms-content');
   	      var url = '{$admin_url}/ajax_lock.php?showtemplate=false';
              var opts = { opt: 'check', type: 'content' };
              var ok = false;
              opts[cms_data.secure_param_name] = cms_data.user_key;
              $.ajax({
                  url: url,
                  async: false,
                  data: opts,
                  success: function(data,textStatus,jqXHR) {
	              if( data.status != 'success' ) return;
	              if( data.locked ) have_locks = true;
	          }
              });
          }
          if( have_locks ) {
              alert('{$mod->Lang('error_action_contentlocked')|escape:'javascript'}');
	      e.preventDefault();
          }
      })
  });
  //]]>
  </script>

  <div id="useroptions" style="display: none;" title="{$mod->Lang('title_userpageoptions')}">
    {form_start action='defaultadmin' id='myoptions_form'}
      <div class="pageoverflow">
        <input type="hidden" name="{$actionid}setoptions" value="1"/>
	<p class="pagetext">{$mod->Lang('prompt_pagelimit')}:</p>
	<p class="pageinput">
	  <select name="{$actionid}pagelimit">
	    {html_options options=$pagelimits selected=$pagelimit}
	  </select>
	</p>
      </div>
    {form_end}
  </div>
  <div class="clearb"></div>
{/if}{* ajax *}

<div class="row c_full">
  <div class="pageoptions grid_6">
    <ul class="options-menu">
      {if isset($content_list) && $npages > 1}
      <li>
        {form_start action='defaultadmin'}
	  <span>{$mod->Lang('page')}:&nbsp;
	    <select name="{$actionid}curpage" id="{$actionid}curpage">
	      {html_options options=$pagelist selected=$curpage}
	    </select>
	    <button name="{$actionid}submitpage" class="invisible ui-button ui-widget ui-corner-all ui-state-default ui-button-text-icon-primary">
	      <span class="ui-button-icon-primary ui-icon ui-icon-circle-check"></span>
	      <span class="ui-button-text">{$mod->Lang('submit')}</span>
	    </button>
	  </span>
	{form_end}
      </li>
      {/if}

      {if $can_add_content}
      <li>
        <a  href="{cms_action_url action=admin_editcontent}" accesskey="n" title="{$mod->Lang('addcontent')}" class="pageoptions">{admin_icon icon='newobject.gif' alt=$mod->Lang('addcontent')}&nbsp;{$mod->Lang('addcontent')}</a>
      </li>
      {/if}

      {if isset($content_list)}
        <li class="parent">{admin_icon icon='run.gif' alt=$mod->Lang('prompt_options')}&nbsp;{$mod->Lang('prompt_options')}
        <ul id="popupmenucontents">
          <li><a class="expandall" href="{cms_action_url action='defaultadmin' expandall=1}" accesskey="e" title="{$mod->Lang('prompt_expandall')}">{admin_icon icon='expandall.gif' alt=$mod->Lang('expandall')}&nbsp;{$mod->Lang('expandall')}</a></li>
	    <li><a class="collapseall" href="{cms_action_url action='defaultadmin' collapseall=1}" accesskey="c" title="{$mod->Lang('prompt_collapseall')}">{admin_icon icon='contractall.gif' alt=$mod->Lang('contractall')}&nbsp;{$mod->Lang('contractall')}</a></li>

	  {if $can_reorder_content}
	    <li><a id="ordercontent" href="{cms_action_url action=admin_ordercontent}" accesskey="r" title="{$mod->Lang('prompt_ordercontent')}">{admin_icon icon='reorder.gif' alt=$mod->Lang('reorderpages')}&nbsp;{$mod->Lang('reorderpages')}</a></li>
	  {/if}
	  <li><a id="myoptions" accesskey="o" title="{$mod->Lang('prompt_settings')}">{admin_icon icon='edit.gif' alt=$mod->Lang('prompt_settings')}&nbsp;{$mod->lang('prompt_settings')}</a></li>
	</ul>
        </li>
      {/if}
    </ul>
  </div>

  {if isset($content_list)}
  <div class="pageoptions options-form grid_6">
    <span><label for="ajax_find">{$mod->Lang('find')}:</label>&nbsp;<input type="text" id="ajax_find" name="ajax_find" title="{$mod->Lang('title_listcontent_find')}" value="" size="25"/></span>
  </div>
  {/if}
</div>

<div id="content_area"></div>
