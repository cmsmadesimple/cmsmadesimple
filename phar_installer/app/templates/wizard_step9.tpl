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
    <h3 class="orange text-centered">{'title_share'|tr}</h3>
    <div class="row text-centered">
        <a id="google" class="action-button social google"><i class="icon-googleplus"></i> Google+</a> <a id="facebook" class="action-button social facebook"><i class="icon-facebook"></i> Facebook</a> <a id="twitter" class="action-button social twitter"><i class="icon-twitter"></i> Twitter</a> <a id="linkedin" class="action-button social linkedin"><i class="icon-linkedin"></i> LinkedIn</a>
    </div>
{/block}