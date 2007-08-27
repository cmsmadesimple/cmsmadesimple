<div class="pagecontainer">

  <div class="pageoverflow">
    {$header_name}
  </div>

  <div id="contentlist">
    {$content_list}
  </div>

</div>

<p class="pageback"><a class="pageback" href="{$back_url}">&#171; {lang string='back'}</a></p>
{literal}
<script type="text/javascript">
//<![CDATA[
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
