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
<hr>
<div class="row message yellow">{'step9_removethis'|tr}</div>
<h3 class="orange text-centered">{'step9_join_community'|tr}</h3>
<p class="text-centered">{'step9_get_help'|tr}:</p>
<div class="row text-centered">
  <a class="action-button social facebook" href="https://www.facebook.com/cmsmadesimple" target="_blank">Facebook</a>
  <a class="action-button social linkedin" href="https://www.linkedin.com/groups/1139537" target="_blank">LinkedIn</a>
  <a class="action-button social twitter" href="https://twitter.com/cmsms" target="_blank">Twitter</a>
  <a class="action-button social orange" href="http://www.cmsmadesimple.org/support/options" target="_blank">{'step9_get_support'|tr}</a>
</div>
<h3 class="orange text-centered">{'step9_love_cmsms'|tr}?</h3>
<div class="row text-centered">
  <a href="http://www.cmsmadesimple.org/donations" target="_blank">{'step9_support_us'|tr}</a>
</div>
{/block}