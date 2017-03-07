{if $ajax == 0}
  <script type="text/javascript">
  //<![CDATA[
  function cms_CMloadUrl(link, lang) {
      $(document).on('click', link, function (e) {
          var url = $(this).attr('href') + '&showtemplate=false&{$actionid}ajax=1';

          var _do_ajax = function() {
	     $.ajax({
	        url: url,
	     }).done(function(){
 	        $('#content_area').autoRefresh('refresh').done(function(){
		    console.debug('after refresh');
		});
	     })
	  }

	  e.preventDefault();
	  $('#ajax_find').val('');

          if (typeof lang == 'string' && lang.length > 0) {
	      cms_confirm(lang).done(_do_ajax);
	  } else {
	     _do_ajax();
	  }
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
			    $('#ajax_find').val('');
    		            $('#content_area').autoRefresh('option','url','{$ajax_get_content}');
			},
                        select: function (event, ui) {
                            event.preventDefault();
                            $(this).val(ui.item.label);
                            var url = '{cms_action_url action=ajax_get_content forjs=1}&showtemplate=false&{$actionid}seek=' + ui.item.value;
			    $('#content_area').autoRefresh('option','url',url).autoRefresh('refresh').done(function(){
			       $('html,body').animate({
			          scrollTop: $('#row_'+ui.item.value).offset().top
			       });
			    })
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
	  e.preventDefault();
	  var self = $(this);
	  var url = $(this).attr('href')+'{$actionid}steal=1';
          cms_confirm('{$mod->Lang('confirm_steal_lock')|escape:'javascript'}').done(function(){
	      window.location.href = url;
	  });
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
		      event.preventDefault();
	              cms_alert('{$mod->Lang('error_contentlocked')|escape:'javascript'}');
                  }
              }
	  });
      });

      // filter dialog
      $('#filter_type').change(function(){
         var map = {
	    'DESIGN_ID': '#filter_design',
	    'TEMPLATE_ID': '#filter_template',
	    'OWNER_UID': '#filter_owner',
	    'EDITOR_UID': '#filter_editor'
	 }
         var v = $(this).val();
	 $('.filter_fld').hide();
	 $(map[v]).show();
      })
      $('#filter_type').trigger('change');
      $(document).on('click', '#myoptions', function () {
          $('#useroptions').dialog({
	      minWidth: '600',
	      minHeight: 225,
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

      // other events
      $(document).on('change','#selectall,input.multicontent',function() {
          $('#content_area').autoRefresh('reset');
      });

      $(document).on('keypress','#ajax_find',function (e) {
          $('#content_area').autoRefresh('reset');
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

      $(document).on('click','a#clearlocks',function(ev){
         var self = $(this);
	 ev.preventDefault();
         cms_confirm('{$mod->Lang('confirm_clearlocks')|escape:'javascript'}').done(function(){
	    window.location = self.attr('href');
	 });
      });

      $(document).on('click','a#ordercontent',function(e){
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
	      e.preventDefault();
              cms_alert('{$mod->Lang('error_action_contentlocked')|escape:'javascript'}');
          }
      })
  });
  //]]>
  </script>

	<div id="useroptions" style="display: none;" title="{$mod->Lang('title_userpageoptions')}">
    {form_start action='defaultadmin' id='myoptions_form'}
		<div class="c_full cf">
			<input type="hidden" name="{$actionid}setoptions" value="1"/>
			<label class="grid_4">{$mod->Lang('prompt_pagelimit')}:</label>
			<select name="{$actionid}pagelimit" class="grid_7">
				{html_options options=$pagelimits selected=$pagelimit}
			</select>
		</div>
		{if $can_manage_content}
			{$type=''}{$expr=''}
			{$opts=[]}
			{$opts['']=$mod->Lang('none')}
			{$opts['DESIGN_ID']=$mod->Lang('prompt_design')}
			{$opts['TEMPLATE_ID']=$mod->Lang('prompt_template')}
			{$opts['OWNER_UID']=$mod->Lang('prompt_owner')}
			{$opts['EDITOR_UID']=$mod->Lang('prompt_editor')}
			{if $filter}{$type=$filter->type}{$expr=$filter->expr}{/if}
			<div class="c_full cf">
				<label class="grid_4">{$mod->Lang('prompt_filter_type')}:</label>
				<select name="{$actionid}filter_type" class="grid_7" id="filter_type">
					{html_options options=$opts selected=$type}
				</select>
			</div>
			<div class="c_full cf filter_fld" id="filter_design">
				<label class="grid_4">{$mod->Lang('prompt_design')}:</label>
				<select name="{$actionid}filter_design" class="grid_7">
					{html_options options=$design_list selected=$expr}
				</select>
			</div>
			<div class="c_full cf filter_fld" id="filter_template">
				<label class="grid_4">{$mod->Lang('prompt_template')}:</label>
				<select name="{$actionid}filter_template" class="grid_7">
					{html_options options=$template_list selected=$expr}
				</select>
			</div>
			<div class="c_full cf filter_fld" id="filter_owner">
				<label class="grid_4">{$mod->Lang('prompt_owner')}:</label>
				<select name="{$actionid}filter_owner" class="grid_7">
					{html_options options=$user_list selected=$expr}
				</select>
			</div>
			<div class="c_full cf filter_fld" id="filter_editor">
				<label class="grid_4">{$mod->Lang('prompt_editor')}:</label>
				<select name="{$actionid}filter_editor" class="grid_7">
					{html_options options=$user_list selected=$expr}
				</select>
			</div>
		{/if}
    {form_end}
	</div>
	<div class="clearb"></div>

{/if}{* ajax *}


<div id="content_area"></div>
