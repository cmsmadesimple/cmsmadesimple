{literal}
<style type="text/css">
table.phpinfo {
	width:700px;
	margin: auto;
	padding: 0.2em;
	font-family: Verdana, sans-serif;
	background: #fff;
	color: #000;
	font-size: 1em;
	border: 1px solid #243135;
	border-spacing: 0px;
	border-collapse: collapse;
}
h2 {
	color: #3D545A;
	text-align: center;
	font: bold 1.2em Verdana, serif;
	padding: 0.5em;
}
</style>
{/literal}

<div class="pagecontainer">
{if empty($smarty.get.cleanreport)}
	<p class="pageshowrows"><a href="{$systeminfo}">{si_lang a=systeminfo}</a></p>
	<p class="pageshowrows"><a href="{$systeminfo_cleanreport}">{si_lang a=copy_paste_forum}</a></p>
{/if}

{$showheader}

{$phpinfo}

<p class="pageback"><a class="pageback" href="{$backurl}">&#171; {si_lang a=back}</a></p>
