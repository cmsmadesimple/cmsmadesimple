<style>
#oe_mainarea .mm-featured-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 16px;
  list-style: none;
  margin: 16px 0;
  padding: 0;
}
#oe_mainarea .mm-featured-grid li {
  list-style: none;
  margin: 0;
  padding: 0;
}
.mm-card {
  border: 1px solid #ddd;
  border-radius: 4px;
  background: #fff;
  display: flex;
  flex-direction: column;
  transition: box-shadow 0.15s;
}
.mm-card:hover {
  box-shadow: 0 1px 6px rgba(0,0,0,0.12);
}
.mm-card-body {
  padding: 16px;
  flex: 1;
  display: flex;
  gap: 12px;
}
.mm-card-icon {
  flex-shrink: 0;
  width: 64px;
  height: 64px;
  border-radius: 4px;
  background: #f0f0f0;
  display: flex;
  align-items: center;
  justify-content: center;
}
.mm-card-icon img {
  width: 64px;
  height: 64px;
  border-radius: 4px;
}
.mm-card-icon .mm-no-icon {
  font-size: 28px;
  color: #999;
}
.mm-card-info {
  flex: 1;
  min-width: 0;
}
.mm-card-info h3 {
  margin: 0 0 6px;
  font-size: 14px;
  line-height: 1.3;
}
.mm-card-info h3 a {
  text-decoration: none;
  color: #0073aa;
}
.mm-card-info h3 a:hover {
  color: #00a0d2;
}
.mm-card-desc {
  font-size: 12px;
  color: #555;
  margin: 0;
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.mm-card-footer {
  border-top: 1px solid #eee;
  padding: 10px 16px;
  font-size: 11px;
  color: #666;
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  align-items: center;
}
.mm-card-footer span {
  display: inline-flex;
  align-items: center;
  gap: 3px;
}
.mm-card-action {
  margin-left: auto;
}
.mm-card-action a {
  font-weight: 600;
}
.mm-badge-new {
  background: #46b450;
  color: #fff;
  font-size: 10px;
  padding: 1px 6px;
  border-radius: 3px;
  margin-left: 4px;
  vertical-align: middle;
}
.mm-badge-incompatible {
  background: #dc3232;
  color: #fff;
  font-size: 10px;
  padding: 1px 6px;
  border-radius: 3px;
  margin-left: 4px;
  vertical-align: middle;
}
.mm-badge-untested {
  background: #ffb900;
  color: #fff;
  font-size: 10px;
  padding: 1px 6px;
  border-radius: 3px;
  margin-left: 4px;
  vertical-align: middle;
}
</style>

{if isset($letter_urls)}
<p class="pagerows">
{foreach $letter_urls as $key => $url}
  {if $key == $curletter}
	<strong>{if $key == 'featured'}{$ModuleManager->Lang('featured')}{else}{$key}{/if}</strong>&nbsp;
  {else}
	<a href="{$url}" title="{if $key == 'featured'}{$ModuleManager->Lang('featured_description')}{else}{$ModuleManager->Lang('title_letter',$key)}{/if}">{if $key == 'featured'}{$ModuleManager->Lang('featured')}{else}{$key}{/if}</a>&nbsp;
  {/if}
{/foreach}
</p>
{/if}

<h3>{$ModuleManager->Lang('featured_description')} {cms_help key2='help_featured' title=$ModuleManager->Lang('featured_description')}</h3>

{if isset($message) && $message != ''}
<div class="warning"><p>{$message}</p></div>
{/if}

{if isset($itemcount) && $itemcount > 0}
<ul class="mm-featured-grid">
{foreach from=$items item=entry}
  <li>
    <div class="mm-card">
      <div class="mm-card-body">
        <div class="mm-card-icon">
          {if isset($entry->icon) && $entry->icon}
            <img src="{$entry->icon}" alt="{$entry->rawname}"/>
          {else}
            <img src="{$ModuleManager->GetModuleURLPath()}/images/icon.png" alt="" style="width:48px;height:48px;opacity:0.4;"/>
          {/if}
        </div>
        <div class="mm-card-info">
          <h3>
            {$entry->name}
            {if $entry->age=='new'}<span class="mm-badge-new">{$ModuleManager->Lang('new')}</span>{/if}
            {if $entry->incompatible}<span class="mm-badge-incompatible">{$ModuleManager->Lang('incompatible')}</span>{/if}
            {if $entry->untested && !$entry->incompatible}<span class="mm-badge-untested">{$ModuleManager->Lang('untested')}</span>{/if}
          </h3>
          <p class="mm-card-desc">{$entry->description|strip_tags|truncate:92}</p>
        </div>
      </div>
      <div class="mm-card-footer">
        <span title="{$ModuleManager->Lang('title_modulelastversion')}">v{$entry->version}</span>
        <span title="{$ModuleManager->Lang('title_modulelastreleasedate')}">{$entry->date|cms_date_format:'%b %Y'}</span>
        {if isset($entry->cmsms_tested) && $entry->cmsms_tested}
          <span title="{$ModuleManager->Lang('title_lastchecked')}">{$ModuleManager->Lang('lastchecked')}: {$entry->cmsms_tested}</span>
        {/if}
        <span class="mm-card-action">{$entry->status}</span>
      </div>
    </div>
  </li>
{/foreach}
</ul>
{/if}
