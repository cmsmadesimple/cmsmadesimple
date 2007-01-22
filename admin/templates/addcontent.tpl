{validation_errors for=$page_object}

<div class="pagecontainer">

  <div class="pageoverflow">
    {$header_name}
  </div>


<div id="page_tabs">
	<div id="content">Content</div>
	<div id="advanced">Advanced</div>
	{if $can_preview eq true}
	  <div id="preview"{if $showpreview eq true} class="active"{/if} onclick="xajax_ajaxpreview(xajax.getFormValues('contentform'));return false;">Preview</div>
	{/if}
</div>    


  {if $error_msg}
  <div class="pageerrorcontainer">
    <p>{$error_msg}</p>
  </div>
  {/if}

  {if $message}
  <div class="pagemcontainer">
    <p>{$message}</p>
  </div>
  {/if}

  <form method="post" name="contentform" enctype="multipart/form-data" id="contentform">

    {html_hidden id='serialized_content' name='serialized_content' value=$serialized_object}
    {html_hidden id='orig_page_type' name='orig_page_type' value=$orig_page_type}
<div id="page_content">
<div id="content_c">
    {* Page Type *}
    <div class="pageoverflow">
    	<p class="pagetext">{lang string='contenttype'}:</p>
    	<p class="pageinput">
        <select name="page_type" onchange="document.contentform.submit()" class="standard">
          {html_options options=$page_types selected=$selected_page_type}
        </select>
    	</p>
    </div>

    {* Page Title *}
    {if $page_object->field_used('name')}
      <div class="pageoverflow50">
      	<p class="pagetext">{lang string='title'}:</p>
      	<p class="pageinput">
      	  {html_input id='content_name' name='content[name]' value=$page_object->name useentities='true'}
      	</p>
      </div>
    {/if}


    {* Menu Text *}
    {if $page_object->field_used('menu_text')}
      <div class="pageoverflow50">
      	<p class="pagetext">{lang string='menutext'}:</p>
      	<p class="pageinput">
      	  {html_input id='content_menu_text' name='content[menu_text]' value=$page_object->menu_text useentities='true'}
      	</p>
      </div>
    {/if}

    {section name=onefile loop=$include_templates}
      {include file=$include_templates[onefile]}
    {/section}
    
    {* Parent Dropdown *}
    {if $show_parent_dropdown eq true}
      <div class="pageoverflow">
      	<p class="pagetext">{lang string='parent'}:</p>
      	<p class="pageinput">
      	  {$parent_dropdown}
      	</p>
      </div>
    {/if}
</div>

<div id="advanced_c">
    {* Template Dropdown *}
    {if $page_object->field_used('template_id')}
      <div class="pageoverflow">
      	<p class="pagetext">{lang string='template'}:</p>
      	<p class="pageinput">
      	  {$template_names}
      	</p>
      </div>
    {/if}

    {* Active Checkbox *}
    {if $page_object->field_used('active')}
      <div class="pageoverflow50">
      	<p class="pagetext">{lang string='active'}:</p>
      	<p class="pageinput">
      	  {html_checkbox id='content_active' name='content[active]' selected=$page_object->active}
      	</p>
      </div>
    {/if}
    
    {* Show in Menu Checkbox *}
    {if $page_object->field_used('show_in_menu')}
      <div class="pageoverflow50">
      	<p class="pagetext">{lang string='showinmenu'}:</p>
      	<p class="pageinput">
      	  {html_checkbox id='content_show_in_menu' name='content[show_in_menu]' selected=$page_object->show_in_menu}
      	</p>
      </div>
    {/if}
    
    {* Cacheable Flag *}
    {if $page_object->field_used('cachable')}
      <div class="pageoverflow50">
      	<p class="pagetext">{lang string='cachable'}:</p>
      	<p class="pageinput">
      	  {html_checkbox id='content_cachable' name='content[cachable]' selected=$page_object->cachable}
      	</p>
      </div>
    {/if}
    
    {* Owner Dropdown *}
    {if $show_owner_dropdown eq true}
      <div class="pageoverflow50">
      	<p class="pagetext">{lang string='owner'}:</p>
      	<p class="pageinput">
      	  {$owner_dropdown}
      	</p>
      </div>
    {/if}
    
    {* Metadata *}
    {if $page_object->field_used('metadata')}
      <div class="pageoverflow">
        <p class="pagetext">{lang string='metadata'}:</p>
        <p class="pageinput">
          {$metadata_box}
        </p>
      </div>
    {/if}
    
    {* Ttile Attribute *}
    {if $page_object->field_used('title_attribute')}
      <div class="pageoverflow50">
      	<p class="pagetext">{lang string='titleattribute'}:</p>
      	<p class="pageinput">
      	  {html_input id='content_title_attribute' name='content[title_attribute]' value=$page_object->title_attribute}
      	</p>
      </div>
    {/if}


    {* Tab Index *}
    {if $page_object->field_used('tab_index')}
      <div class="pageoverflow50">
      	<p class="pagetext">{lang string='tabindex'}:</p>
      	<p class="pageinput">
      	  {html_input id='content_tab_index' name='content[tab_index]' value=$page_object->tab_index}
      	</p>
      </div>
    {/if}
    
    {* Access Key *}
    {if $page_object->field_used('access_key')}
      <div class="pageoverflow50">
      	<p class="pagetext">{lang string='accesskey'}:</p>
      	<p class="pageinput">
      	  {html_input id='content_access_key' name='content[access_key]' value=$page_object->access_key}
      	</p>
      </div>
    {/if}
    

    {* Page Alias *}
    {if $page_object->field_used('alias')}
      <div class="pageoverflow50">
      	<p class="pagetext">{lang string='pagealias'}:</p>
      	<p class="pageinput">
      	  {html_input id='content_alias' name='content[alias]' value=$page_object->alias useentities='true'}
      	</p>
      </div>
    {/if}
    <div class="clearb"></div>

</div>

{if $can_preview eq true}
<div id="preview_c">
  <iframe name="previewframe" class="preview" id="previewframe" src="{$previewfname}"></iframe>
</div>
{/if}

</div>

<div class="footerbuttons">
    {* Preview *}

    
    <input type="submit" name="submitbutton" value="{lang string='submit'}" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
    <input type="submit" name="cancel" value="{lang string='cancel'}" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" />
    {if $can_apply eq true}
      <input type="submit" name="applybutton" value="{lang string='apply'}" class="pagebutton" onmouseover="this.className='pagebuttonhover'"   onmouseout="this.className='pagebutton'" />
    {/if}
    {if $can_preview eq true}
      <input type="submit" name="previewbutton" value="{lang string='preview'}" class="pagebutton" onmouseover="this.className='pagebuttonhover'" onmouseout="this.className='pagebutton'" onclick="xajax_ajaxpreview(xajax.getFormValues('contentform'));return false;" />
    {/if}
</div>


  </form>

</div>
