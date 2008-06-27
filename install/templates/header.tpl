<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>{lang_install a=install_title 1=$current_page}</title>
	<link rel="stylesheet" type="text/css" href="install.css" />
	<script src="../admin/themes/default/includes/standard.js" type="text/javascript"></script>
</head>

<body>

<div class="body">
	<img src="../images/cms/cmsbanner.jpg" width="800" height="100" alt="CMS Banner Logo" />
	<div class="headerish">
		<h1>{lang_install a=install_system}</h1>
	</div>
	<div class="main">

<h2>{lang_install a=install_thanks}<br />{$cms_version} ({$cms_version_name})</h2>

<table class="countdown" cellspacing="2" cellpadding="2">
	<tr>
{section name=stepimages loop=$number_of_pages}
{assign var='page' value=$smarty.section.stepimages.index+1}
		<td><img src="images/{$page}{if $page == $current_page}off{/if}.gif" alt="Step {$page}" /></td>
{/section}
	</tr>
</table>
