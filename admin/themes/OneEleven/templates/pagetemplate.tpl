<!doctype html>
<html lang="{$lang|truncate:'2':''}" dir="{$lang_dir}">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<title>{if !empty($pagetitle)}{$pagetitle} - {/if}{sitename}</title>
		<base href="{$config.admin_url}/" />
		<meta name="generator" content="CMS Made Simple - Copyright (C) 2004-12 Ted Kulp. All rights reserved." />
		<meta name="robots" content="noindex, nofollow" />
		<meta name="viewport" content="initial-scale=1.0; maximum-scale=1.0; user-scalable=no" />
		<meta name="HandheldFriendly" content="True"/>
		<link rel="shortcut icon" href="{$config.admin_url}/themes/OneEleven/images/favicon/cmsms-favicon.ico"/>
		<link rel="bookmark" href="{$config.admin_url}/themes/OneEleven/images/favicon/cmsms-favicon.ico"/>
		<link rel="stylesheet" href="style.php?{$secureparam}" />
		<!-- learn IE html5 -->
		<!--[if lt IE 9]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		{cms_jquery append="`$config.admin_url`/themes/OneEleven/includes/jquery.cookie.min.js,`$config.admin_url`/themes/OneEleven/includes/standard.js"}
		<!-- THIS IS WHERE HEADER STUFF SHOULD GO -->
	 	{$headertext|default:''}
		<!-- custom jQueryUI Theme 1.8.16 see style.css for color reference //-->
		<link href="{$config.admin_url}/themes/OneEleven/css/default-cmsms/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
        {FileManager action='javascript'}
	</head>
	<body##BODYSUBMITSTUFFGOESHERE## lang="{$lang|truncate:'2':''}">
		<!-- start container -->
		<div id="container" class="sidebar-on">
			<!-- start header -->
			<header role="banner" class="cf header">
				<!-- start header-top -->
				<div class="header-top">
					<!-- logo -->
					<div class="cms-logo">
						<img src="{$config.admin_url}/themes/OneEleven/images/layout/cmsms-logo.jpg" width="205" height="69" alt="{sitename} - {$pagetitle}" title="{sitename}" />
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
			<div id="admin-content">
				<div class="shadow">
					&nbsp;
				</div>
				<!-- start sidebar -->
				<div id="sidebar">
					<aside>
						{assign var='is_notifications' value=$theme->get_notifications()}
						<span title="Close / Open Sidebar" class="toggle-button close{if empty($is_notifications)} top{/if}">Close / Open Sidebar</span>
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
					{include file='messages.tpl'}
					<article role="main" class="content-inner">
						<header class="pageheader cf">
							{if isset($module_icon_url) or isset($pagetitle)} 
							<h1>{if isset($module_icon_url)}<img src="{$module_icon_url}" alt="{$module_name|default:''}" class="module-icon" />{/if}
							{$pagetitle|default:''}
							</h1>
							{if isset($module_help_url) or isset($wiki_url)} <span class="helptext"> {if isset($module_help_url)}<a href="{$module_help_url}">{'module_help'|lang}</a>{/if}
								{if isset($wiki_url)}<a href="{$wiki_url}" class="external" target="_blank">{'help'|lang}</a> <em>({'new_window'|lang})</em>{/if} </span> {/if}
							{/if}
							{* filemanager dropzone *}
							{FileManager action='dropzone' id='dropzone' assign='droparea'}
							{if isset($droparea)}
							<div class="drop">
								<div class="drop-inner cf">
								<span class="folder-selection open" title="Select a folder"></span>
									<div class="dialog invisible" role="dialog" title="Select a folder">
										<h3>Select a folder</h3>
										<p>A folder list or whatever</p>
									</div>										
									<span class="zone">{$droparea}</span>
								</div>									
								<a href="#" title="{'open'|lang}/{'close'|lang}" class="toggle-dropzone">{'open'|lang}/{'close'|lang}</a>
							</div>
							{/if} 
						</header>
						<section class="cf">
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