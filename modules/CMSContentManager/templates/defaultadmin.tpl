{if $ajax == 0}
  <script type="text/javascript">
  //<![CDATA[
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
 	    $('#content_area').autoRefresh('refresh');
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
      cms_busy();
      $('#content_area').autoRefresh({
          url: '{$ajax_get_content}',
	  done_handler: function() {
	        $('#ajax_find').autocomplete({
			source: '{cms_action_url action=admin_ajax_pagelookup forjs=1}&showtemplate=false',
			minLength: 2,
			position: {
              			  my: "right top",
				  at: "right bottom"
                        },
			change: function (event, ui)  {
			    // goes back to the full list, no options
			    console.debug('in autocomplete change');
			    $('#ajax_find').val('');
    		            $('#content_area').autoRefresh('option','url','{$ajax_get_content}');
			},
                        select: function (event, ui) {
			    console.debug('in autocomplete select');
                            event.preventDefault();
                            $(this).val(ui.item.label);
                            var url = '{cms_action_url action=ajax_get_content forjs=1}&showtemplate=false&{$actionid}seek=' + ui.item.value;
			    console.debug('url is '+url);
			    $('#content_area').autoRefresh('option','url',url);
                        }
                });
	  }
      });

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


<div id="content_area"></div>
