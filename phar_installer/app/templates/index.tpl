{block name='logic'}{/block}<!DOCTYPE html>
<!--[if IE 8]>         <html lang="en" class="lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en"> <!--<![endif]-->
    <head>
        {if isset($BASE_HREF)}<base href="{$BASE_HREF}"/>{/if}
        <meta charset="utf-8">
        <meta name='HandheldFriendly' content='True' />
        <meta name='MobileOptimized' content='320' />
        <meta name='viewport' content='width=device-width, initial-scale=1.0' />
        <meta http-equiv='cleartype' content='on' />
        <script src="app/assets/vendor/jquery-1.11.2.min.js"></script>
        <script src="app/assets/vendor/jquery-ui/jquery-ui.min.js"></script>
        <link rel="stylesheet" type="text/css" href="app/assets/vendor/jquery-ui/jquery-ui.min.css"/>
        <title>
      {if !empty($browser_title)}
        {$browser_title}
      {elseif !empty($title)}
        {$title nocache} - CMS Made Simple&trade; {'apptitle'|tr}
      {else}
        CMS Made Simple&trade; {'apptitle'|tr}}
      {/if}
     </title>
        <!--[if lt IE 9]>
            <script src="app/assets/js/html5.js"></script>
            <script src="app/assets/js/css3-mediaqueries.js"></script>
        <![endif]-->
        <link rel="stylesheet" type="text/css" href="app/assets/css/install.css"/>
    <link rel="icon" type="image/ico" href="app/assets/images/favicon.ico"/>
    </head>
    <body class="cmsms-ui">
        <div class="row header-section">
            <a href="http://www.cmsmadesimple.org" rel="external" target="_blank" class="cmsms-logo" title="CMS Made Simple&trade;">
                <img src="app/assets/images/cmsms-logo.png" alt="CMS Made Simple&trade;" title="CMS Made Simple&trade;" width="332" height="77" />
            </a>
            <span class="installer-title">{'apptitle'|tr}</span>
        </div>
        <div class="row installer-section">
            <div class="four-col installer-steps-section">
                <div class="inner">
                {block name='aside_content'}
                    {if isset($wizard_steps)}
                    <aside class="installer-steps">
                        <ol id="installer-indicator">
                            {foreach $wizard_steps as $classname => $step}
                            {strip}
                            <li class="step {if $step.active} current-step{/if}{if isset($current_step) && $current_step > $step@iteration} done-step{/if}">
                                <h4 class="step-title">{$step.classname|tr}{if isset($current_step) && $current_step > $step@iteration} <i class="icon-checkmark">&#x2713;</i>{/if}</h4>
                                <p class="step-description"><em>{'desc_'|cat:$step.classname|tr}</em></p>
                            </li>
                            {/strip}
                            {/foreach}
                        </ol>
                    </aside>
                    {/if}
                {/block}
                </div>
            </div>
            <main role="main" class="eight-col installer-content-section">
                <div class="inner">
                    <h1>{if isset($title)}{$title}{else}{'install_upgrade'|tr}{/if}</h1>
            {if isset($subtitle)}<h3>{$subtitle}</h3>{/if}

                    {if isset($dir) && ($in_phar || $cur_step > 1)}
                    <div class="message blue icon">
                        <i class="icon-folder-open message-icon"></i>
                        <div class="content"><strong>{'prompt_dir'|tr}:</strong> <br />{$dir}</div>
                    </div>
                    {/if}

                    {if isset($error)}
                    <div class="message red">
                        {$error}
                    </div>
                    {/if}
                    <article>
                        {block name='contents'}WIZARD CONTENTS GO HERE{/block}
            {block name='content-footer'}{/block}
                    </article>

                </div>
            </main>
        </div>
        <footer class="row footer-section">
            <div class="footer-info">
                <a href="https://forum.cmsmadesimple.org" target="_blank">{'title_forum'|tr}</a> &bull; <a href="https://docs.cmsmadesimple.org" target="_blank">{'title_docs'|tr}</a> &bull; <a href="http://apidoc.cmsmadesimple.org" target="_blank">{'title_api_docs'|tr}</a>
            </div>
            <small>
                &copy; Copyright {$smarty.now|localedate_format:'Y'} <a href="http://www.cmsmadesimple.org">CMS Made Simple&trade;</a>. All rights reserved{if isset($installer_version)} - {'installer_ver'|tr}:&nbsp;{$installer_version}{/if}{if isset($build_time)} - {'build_date'|tr}:&nbsp;{$build_time|localedate_format:'j %h Y H:i:s'}{/if}
            </small>
        </footer>
    {block name='javascript'}
    <script>
    var cmsms_lang = {
        freshen : '{'confirm_freshen'|tr|addslashes}',
        upgrade : '{'confirm_upgrade'|tr|addslashes}',
        message : '{'social_message'|tr|addslashes}'
    };
    </script>
    {/block}
    </body>
</html>
