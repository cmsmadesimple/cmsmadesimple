<!doctype html>
<html lang="en" dir="ltr">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<title>{if !empty($pagetitle)}{$pagetitle} - {/if}{sitename}</title>
		<base href="{$config.admin_url}/" />
		<meta name="generator" content="CMS Made Simple - Copyright (C) 2004-12 Ted Kulp. All rights reserved." />
		<meta name="robots" content="noindex, nofollow" />
		<link rel="shortcut icon" href="{$config.admin_url}/themes/noname/images/layout/favicon.ico"/>
		<link rel="bookmark" href="{$config.admin_url}/themes/noname/images/layout/favicon.ico"/>
		<link rel="stylesheet" type="text/css" href="style.php?{$secureparam}" />
		<!-- custom jQueryUI Theme 1.8.16 see style.css for color reference //-->
		<link type="text/css" href="{$config.admin_url}/themes/noname/css/default-cmsms/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
		<!-- learn IE html5 -->
		<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<!-- THIS IS WHERE HEADER STUFF SHOULD GO -->
		<script type="text/javascript" src="{$config.admin_url}/themes/noname/includes/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="{$config.admin_url}/themes/noname/includes/jquery-ui-1.8.16.custom.min.js"></script> 
		<script type="text/javascript" src="{root_url}/lib/jquery/js/jquery.ui.nestedSortable-1.3.4.js"></script>
		<script type="text/javascript" src="{root_url}/lib/jquery/js/jquery.json-2.2.js"></script> 
		<script type="text/javascript" src="{$config.admin_url}/themes/noname/includes/jquery.cookie.js"></script>
		<script type="text/javascript" src="{$config.admin_url}/themes/noname/includes/standard.js"></script>
		<script type="text/javascript" src="{$config.admin_url}/themes/noname/includes/functions.js"></script>
	</head>
	<body##BODYSUBMITSTUFFGOESHERE##>
		<!-- start container -->
		<div id="container" class="sidebar-on">
			<!-- start header -->
			<header role="banner" class="cf header">
				<!-- start header-top -->
				<div class="header-top">
					<!-- logo -->
					<div class="cms-logo">
						<img src="{$config.admin_url}/themes/noname/images/layout/cmsms-logo.jpg" width="205" height="69" alt="{sitename} - {$pagetitle}" title="{sitename}" />
					</div>
					<!-- title -->
					<span class="admin-title"> {'adminpaneltitle'|lang} - {sitename} - {$pagetitle} </span>
				</div>
				<!-- end header-top //-->
				<!-- start header-bottom -->
				<div class="header-bottom cf">
					<!-- welcome -->
					<div class="welcome">
						<span>{'welcome_user'|lang}: {$user->username}</span>
					</div>
					<!-- breadcrubms -->
					{include file='breadcrumbs.tpl' items=$theme->get_breadcrumbs()} 
					<!-- bookmarks -->
					{include file='shortcuts.tpl'}
				</div>
				<!-- end header-bottom //-->
			</header>
			<!-- end header //-->
			<!-- start content -->
			<div id="content">
				<div class="shadow">
					&nbsp;
				</div>
				<!-- start sidebar -->
				<div id="sidebar">
					<aside>
						<span title="Close / Open Sidebar" class="toggle-button close">Close / Open Sidebar</span>
						<!-- notifications -->
						{include file='notifications.tpl' items=$theme->get_notifications()} 
						<!-- start navigation -->
						{include file='navigation.tpl' nav=$theme->get_navigation_tree()} 
						<!-- end navigation //-->
					</aside>
				</div>
				<!-- end sidebar //-->
				<!-- start main -->
				<div id="main" class="cf sidebar-on">
					{strip}
					<article role="main" class="content-inner">
						<header class="pageheader cf">
							{if isset($module_icon_url) or isset($pagetitle)} 
							<h1>{if isset($module_icon_url)}<img src="{$module_icon_url}" alt="{$module_name|default:''}" class="module-icon" />{/if}
							{$pagetitle|default:''}
							</h1>
							{if isset($module_help_url) or isset($wiki_url)} <span class="helptext"> {if isset($module_help_url)}<a href="{$module_help_url}">{'module_help'|lang}</a>{/if}
								{if isset($wiki_url)}<a href="{$wiki_url}" target="_blank">{'help'|lang}</a> <em>({'new_window'|lang})</em>{/if} </span> {/if}
							{/if} 
						</header>
						<section>
							{$content}
						</section>
					</article>
					{/strip}
				</div>
				<!-- end main //-->
				<div class="spacer">
					&nbsp;
				</div>
			</div>
			<!-- end content //-->
			<!-- start footer -->
			{include file='footer.tpl'} 
			<!-- end footer //-->
		</div>
		<!-- end container //-->
		</body>
</html>