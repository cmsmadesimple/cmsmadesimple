<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <title>{sitename} - {$pagetitle}</title>
  <base href="{$config.admin_url}/" />
  <meta name="Generator" content="CMS Made Simple - Copyright (C) 2004-12 Ted Kulp. All rights reserved." />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="robots" content="noindex, nofollow" />
  <link rel="shortcut icon" href="{$config.admin_url}/themes/noname/images/layout/favicon.ico"/>
  <link rel="Bookmark" href="{$config.admin_url}/themes/noname/images/layout/favicon.ico"/>
  <link rel="stylesheet" type="text/css" href="style.php?_sx_=c9772250" />
  {* microtiny stuff goes here *}
  <script type="text/javascript" src="{root_url}/lib/jquery/js/jquery-1.6.2.min.js"></script>
  <script type="text/javascript" src="{root_url}/lib/jquery/js/jquery-ui-1.8.14.min.js"></script>
  <script type="text/javascript" src="{root_url}/lib/jquery/js/jquery.ui.nestedSortable-1.3.4.js"></script>
  <script type="text/javascript" src="{root_url}/lib/jquery/js/jquery.json-2.2.js"></script>

  <!--[if IE]>
  <style type="text/css">{literal}
  ul#nav li ul  {
    filter: alpha(opacity=95);
  }
  /* Nav Tools  */
  div.MainMenu { 
    width:97%;
  }
  {/literal}</style>
  <![endif]-->
</head>
<body>
<div id="body">
<div id="header">
  <div id="logocontainer">
    <img src="{$config.admin_url}/themes/NCleanGrey/images/layout/logoTM.png" alt="{sitename}" title="{sitename}" />
    <div class="logotext">CMS Made Simple&trade; Admin Console - {sitename}<br />{'welcome'|lang}: {$username}</div>
  </div>
</div>
<div class="topnav">
  {include file='navigation.tpl' nav=$theme->get_navigation_tree()}
</div>
<div id="secondnav">
  {include file='breadcrumbs.tpl' items=$theme->get_breadcrumbs()}
  <div id="nav-icons_all"><ul id="nav-icons">
    <li class="viewsite-icon"><a  rel="external" title="View Site"  href="">View Site</a></li>
    <li class="logout-icon"><a  title="Logout"  href="logout.php">Logout</a></li>
  </ul></div>
</div>

  {include file='notifications.tpl' items=$theme->get_notifications()}

<div id="pagecontainer">{strip}
  <div id="pageheader">
    {if isset($module_icon_url) or isset($pagetitle)}
    {if isset($module_icon_url)}<img src="{$module_icon_url}" alt="{$module_name|default:''}"/>&nbsp;{/if}{$pagetitle|default:''}
    {if isset($module_help_url) or isset($wiki_url)}
      <span class="helptext">
        {if isset($module_help_url)}<a href="{$module_help_url}">{'module_help'|lang}</a>{/if}
	&nbsp;
        {if isset($wiki_url)}<a href="{$wiki_url}" target="_blank">{'help'|lang}</a> <em>({'new_window'|lang})</em>{/if}
      </span>
    {/if}
    {/if}
  </div>
  {$content}
{/strip}</div>

<div id="footer">
  <a rel="external" href="http://www.cmsmadesimple.org"><b>CMS Made Simple</b></a><b>&trade;</b> &nbsp;&nbsp;&nbsp; {cms_version} &nbsp;"{cms_versionname}"
</div>
</div>{* end if id=body *}
</body>
</html>