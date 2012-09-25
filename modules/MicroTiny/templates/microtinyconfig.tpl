{literal} tinyMCE.init({ {/literal}
  {* Setup *}
  mode : "exact",
  elements : "{$textareas}",
  body_class : "CMSMSBody",
  {if isset($templateid)}
    content_css : "{cms_stylesheet templateid=$templateid nolinks=1 stripbackground=$strip_background forceblackonwhite=$force_blackonwhite}",
  {/if}
  {* //Performance *}
  entity_encoding : "raw",
  button_tile_map : true, 

	{* //Visual *}	
  theme : "advanced",
  skin : "o2k7",
  skin_variant : "black",
  theme_advanced_toolbar_location : "top",
  theme_advanced_toolbar_align : "left",
  visual : true,
	      
  accessibility_warnings : false,

  forced_root_block : '',      			
  fix_list_elements : true,
  verify_html : true,
  verify_css_classes : false,
  
  plugins : "paste,inlinepopups{if $isfrontend ==false},cmslinker{/if}",
  
  paste_auto_cleanup_on_paste : true,
  paste_remove_spans : true,
  paste_remove_styles : true,
  theme_advanced_buttons1 : "{$toolbar}",
  theme_advanced_buttons2 : "",
  theme_advanced_buttons3 : "",
  
{if $isfrontend == false}
{if $show_statusbar == 1}
  theme_advanced_statusbar_location: "bottom",
{if $allow_resize == 1}
  theme_advanced_resizing: true,
{/if} 
{/if}
{/if}

  theme_advanced_blockformats : "p,div,h1,h2,h3,h4,h5,h6,blockquote,code",
  document_base_url : "{root_url}/",

  relative_urls : true,
  remove_script_host : true,
  language: "{$language}",
  dialog_type: "modal",
  apply_source_formatting : true  
	 
{* From here statements should start with , as it's not certain anymore is coming *}
{if isset($css_styles) and $css_styles != ''}
  ,theme_advanced_styles : '{$css_styles}'
{/if}
{if $isfrontend==false}
  ,file_browser_callback : 'CMSMSFilePicker'
{/if}
  {literal}
});
  {/literal}

{if $isfrontend==false}{literal}
function toggleMicroTiny(id) {
  if (!tinyMCE.getInstanceById(id))
    tinyMCE.execCommand('mceAddControl', false, id);
  else
    tinyMCE.execCommand('mceRemoveControl', false, id);
}
{/literal}{/if}
	
  {if $isfrontend==false}
  {literal}
function CMSMSFilePicker (field_name, url, type, win) {
  {/literal}   
  var cmsURL = "{$filepickerurl}&type="+type+"&showtemplate=false";
  {literal}
  tinyMCE.activeEditor.windowManager.open({
  {/literal}
    file : cmsURL,
    title : '{$filepickertitle}',
    width : '800',
    height : '500',
    resizable : "yes",
    scrollbars : "yes",
    inline : "yes",  {* This parameter only has an effect if you use the inlinepopups plugin! *}
    close_previous : "no"
  {literal}
  }, {
    window : win,
    input : field_name
  });
  return false;
}
{/literal}
{/if}
