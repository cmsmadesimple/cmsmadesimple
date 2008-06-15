<p class="pageoverflow">
{si_lang a=systeminfo_copy_paste}
</p><hr/>

<div id="copy_paste_in_forum">

<p>----------------------------------------------</p>

<p><strong>{si_lang a=cms_version}</strong>: {$cms_version}</p>

<p><strong>{si_lang a=installed_modules}</strong>:</p>
<ul>
{foreach from=$installed_modules item='module'}
 <li>{$module.module_name}: {$module.version}</li>
{/foreach}
</ul>

<br />

<p><strong>{si_lang a=php_information}</strong>:</p>
<ul>
{foreach from=$php_information key='key' item='one'}
  {if is_array($one)}
    {if empty($one[1])}
     <li>{si_lang a=$key} ({$key}): {$one[0]}</li>
    {else}
     <li>{$key}: {si_lang a=$key}: {$one[0]}</li>
    {/if}
  {else}
   <li>{si_lang a=$key} ({$key}): {$one}</li>
  {/if}
{/foreach}
</ul>

<br />

<p><strong>{si_lang a=server_information}</strong>:</p>
<ul>
{foreach from=$server_info key='key' item='one'}
 <li>{si_lang a=$key}: {$one}</li>
{/foreach}
</ul>

<br />

<p><strong>{si_lang a=permission_information}</strong>:</p>
<ul>
{foreach from=$permission_info key='key' item='one'}
 <li>{$key}: {$one}</li>
{/foreach}
</ul>

<br />

<p><strong>{si_lang a=config_information}</strong>:</p>
<ul>
{foreach from=$config_info key='key' item='one'}
 <li>{$key}: {$one}</li>
{/foreach}
</ul>

<p>----------------------------------------------</p>

</div>

{literal}
<script type="text/javascript">
	function fnSelect(objId) {
		fnDeSelect();
		if (document.selection) {
		var range = document.body.createTextRange();
 	        range.moveToElementText(document.getElementById(objId));
		range.select();
		}
		else if (window.getSelection) {
		var range = document.createRange();
		range.selectNode(document.getElementById(objId));
		window.getSelection().addRange(range);
		}
	}
		
	function fnDeSelect() {
		if (document.selection) document.selection.empty(); 
		else if (window.getSelection)
                window.getSelection().removeAllRanges();
	}
	fnSelect('copy_paste_in_forum');
</script>
{/literal}
