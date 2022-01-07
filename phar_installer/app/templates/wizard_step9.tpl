{* wizard step 9 -- files *}

{extends file='wizard_step.tpl'}
{block name='logic'}
    {$subtitle = 'title_step9'|tr}
    {$current_step = '9'}
{/block}
{block name='contents'}

<div id="inner" style="overflow: auto; min-height: 10em; max-height: 35em;"></div>
<div id="bottom_nav">{* bottom nav is needed here *}</div>
{/block}
{block name='content-footer'}
<hr />
    <div class="row message yellow">{'step9_removethis'|tr}</div>
    <h3 class="orange text-centered">Join our community</h3>
    <p class="text-centered">Connect with other CMSMS developers and get help in the following ways:</p>
    <div class="row text-centered">
<a class="action-button social facebook" href="https://www.facebook.com/cmsmadesimple" target="_blank">Facebook</a>
<a class="action-button social linkedin" href="https://www.linkedin.com/groups/1139537" target="_blank">LinkedIn</a>
<a class="action-button social twitter" href="https://twitter.com/cmsms" target="_blank">Twitter</a>
<a class="action-button social google" href="http://www.cmsmadesimple.org/support/options" target="_blank">Support channels</a>
</div>
    <h3 class="orange text-centered">Love CMS Made Simple?</h3>
    <div class="row text-centered">
<a href="http://www.cmsmadesimple.org/donations" target="_blank">Click here to find out how you can support us</a>
</div>
{/block}