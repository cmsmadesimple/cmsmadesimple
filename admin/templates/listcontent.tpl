<div class="pagecontainer">

  <div class="pageoverflow">
    {$header_name}
  </div>

  <div id="contentlist">
    {$content_list}
  </div>

</div>

<p class="pageback"><a class="pageback" href="{$back_url}">&#171; {lang string='back'}</a></p>


<div class="contextMenu" id="context_menu" style="display: none;">
</div>

{literal}
<script type="text/javascript">
//<![CDATA[

function set_context_menu()
{
	$("span.content_name").showMenu(
		{
			query: "#context_menu",
			before_call: function(e) {
				cms_ajax_context_menu($(e.currentTarget).attr('id'));
			}
		}
	);
}

set_context_menu();

function selectall()
{
      checkboxes = document.getElementsByTagName("input");
      for (i=0; i<checkboxes.length ; i++)
      {
              if (checkboxes[i].type == "checkbox") checkboxes[i].checked=true;
      }
}

//]]>
</script>
{/literal}