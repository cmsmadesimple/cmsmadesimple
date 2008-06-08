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

<style>
.placeholder-class
{
	background: #FF0;
}

#context_menu {
background:#FFF;
border:1px solid #444;
display:none;
width:150px;
}

#context_menu ul, #context_menu ul * {
padding:0;
margin:0;
}

#context_menu ul li{
list-style:none;
border:1px solid #444;
padding: 5px;
display:block;
}

#context_menu ul li:hover{
background:#666;
color:#FFF;
}

#context_menu ul li:hover span{
color:#FFF;
}

#context_menu li:hover a{
color:#FFF;
}

#context_menu a{
color:#000;
font:11px Tahoma;
font-weight:bold;
text-decoration:none;
}
</style>
{/literal}