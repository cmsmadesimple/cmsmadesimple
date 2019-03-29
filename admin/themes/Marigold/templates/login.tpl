{extends file='minimal.tpl'}
{block name='css' append}
    {* <link rel="stylesheet" href="loginstyle.php" /> *}
{/block}
{block name='js' append}
    {cms_queue_script file="{$theme_path}/includes/login.js"}
{/block}
{block name='footer'}
    <small class="copyright">Copyright &copy; <a rel="external" href="http://www.cmsmadesimple.org">CMS Made Simple&trade;</a></small>
{/block}
