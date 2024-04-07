{* wizard step 3 *}

{extends file='wizard_step.tpl'}
{block name='logic'}
    {$subtitle = 'title_step3'|tr}
    {$current_step = '3'}
{/block}

{block name='contents'}

{if $tests_failed}
  {if !$can_continue}
    <div class="message red">{'step3_failed'|tr}</div>
  {else}
    <div class="message yellow">{'sometests_failed'|tr}</div>
  {/if}
{/if}

{if $tests_failed || $verbose}
  <table class="table zebra-table bordered-table installer-test-information">
    <thead class="tbhead">
        <tr>
            <th>{'th_status'|tr}</th>
            <th>{'th_testname'|tr}</th>
        </tr>
    </thead>
    <tbody>
    {foreach from=$tests item='test'}
        {cycle values='odd,even' assign='rowclass'}
        <tr class="{$rowclass}{if $test->status == 'test_fail'} error{/if}{if $test->status == 'test_warn'} warning{/if}">
            <td class="{$test->status}">{if $test->status == 'test_fail'}<i title="{'test_failed'|tr}" class="icon-cancel-circle red"></i>{elseif $test->status == 'test_warn'}<i title="{'test_warning'|tr}" class="icon-warning yellow"></i>{else}<i title="{'test_passed'|tr|strip_tags}" class="icon-checkmark-circle green"></i>{/if}</td>
            <td>
                {$test->name|tr}
                {$str = $test->msg()}
                {if $str != '' && ($verbose || $test->status != 'test_pass')}
                  <br />
                  <span class="tests-infotext">{$str}</span>
                {/if}
            </td>
        </tr>
    {/foreach}
    </tbody>
  </table>
{else}
  <div class="message green">{'step3_passed'|tr}</div>
{/if}

<table class="table bordered-table installer-test-legend small-font">
    <caption>
        {'legend'|tr}
    </caption>
    <thead>
        <tr>
            <th>{'symbol'|tr}</th>
            <th>{'meaning'|tr}</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="test_fail red"><i title="{'test_failed'|tr}" class="icon-cancel-circle red"></td>
            <td>{'test_failed'|tr}</td>
        </tr>
        <tr>
            <td class="test_pass green"><i title="{'test_passed'|tr|strip_tags}" class="icon-checkmark-circle green"></i></td>
            <td>{'test_passed'|tr}</td>
        </tr>
        <tr>
            <td class="test_warn yellow"><i title="{'test_warning'|tr}" class="icon-warning yellow"></i></td>
            <td>{'test_warning'|tr}</td>
        </tr>
    </tbody>
</table>

<div class="message yellow">{'warn_tests'|tr}</div>

<div id="bottom_nav">
{if $tests_failed}
  <a onClick="window.location.reload();" class="action-button orange" title="{'retry'|tr}">{'retry'|tr} <i class="icon-loop"></i></a>
{/if}
{if $can_continue} <a href="{$next_url}" class="action-button positive" title="{'next'|tr}">{'next'|tr} &rarr;</a>{/if}
</div>

{/block}
