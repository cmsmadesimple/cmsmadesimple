<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>Printing {title}</title>
    <meta name="robots" content="noindex" />
    <base href="{$rooturl}" />
    <meta name="Generator" content="CMS Made Simple - Copyright (C) 2004-12 Ted Kulp. All rights reserved." />
    <meta http-equiv="Content-Type" content="text/html; charset={$encoding}" />

    {cms_stylesheet media='print' templateid=$templateid}

    {if $overridestylesheet!=''}
    <style type="text/css">
    {$overridestylesheet}
    </style>
    {/if}
    
  </head>
  <body style="background-color: white; color: black; background-image: none; text-align: left;">	
    {$content}
        
    {$printscript}
  </body>
</html>
