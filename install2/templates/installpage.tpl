<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>{tr}CMS Made Simple Install (step %s){/tr}</title>
	<link rel="stylesheet" type="text/css" href="install.css" />
	<script type="text/javascript" src="../lib/jquery/jquery.js"></script>
	<script type="text/javascript" src="../lib/jquery/interface/interface.js"></script>
	{$xajax_header}
</head>

<body>

<div class="body">
	<div class="banner">
		<img src="../admin/themes/NCleanGrey2/images/logoCMS.png" alt="CMS" title="CMS" />
	</div>
	<div class="headerish">
		<h1>{tr}Install System{/tr}</h1>
	</div>

	<div class="main">

		<h2>
			{tr}Thanks for installing CMS Made Simple{/tr}<br />
			{$cms_version} ({$cms_version_name})
		</h2>

		<table class="countdown" cellspacing="1" cellpadding="2">
			<tr>
{section name=step loop=$number_of_pages}
{assign var='page' value=$smarty.section.step.index_next}
				<td><img src="images/{$page}{if $page == $current_page}off{/if}.gif" alt="Step {$page}" /></td>
{/section}
			</tr>
		</table>

		{include file=$include_file}

	</div>
</div>

</body>
</html>
