function microTiny_init(idlist_string) {
 tinyMCE.init({
   {* Setup *}
   mode : "exact",
   elements : idlist_string,
   body_class : "CMSMSBody",
   {if isset($templateid)}
   content_css : "{cms_stylesheet designid=$themeid nolinks=1 stripbackground=$strip_background forceblackonwhite=$force_blackonwhite}",
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
   plugins : "paste,inlinepopups{if $isfrontend == false},cmslinker{/if}",
   paste_auto_cleanup_on_paste : true,
   paste_remove_spans : true,
   paste_remove_styles : true,
   theme_advanced_buttons1 : "{$toolbar}",
   theme_advanced_buttons2 : "",
   theme_advanced_buttons3 : "",
   {if $isfrontend == false}
   setup: function(ed) {
     ed.onChange.add(function(ed,l) {
       if(typeof(jQuery) != 'undefined') {
	 $(ed.getElement()).trigger('cmsms_formchange',{
           'elem': ed.getElement(),
           'value': l.content
         });
	 $(ed.getElement()).trigger('cmsms_textchange',{
           'elem': ed.getElement(),
	   'module': 'MicroTiny',
           'content': l.content 
         });
       }
     });
   },
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
  });
}

{if $isfrontend==false}
function microTiny_toggle() {
  var id = this.name.substr(10);
  if (!tinyMCE.getInstanceById(id))
    tinyMCE.execCommand('mceAddControl', false, id);
  else
    tinyMCE.execCommand('mceRemoveControl', false, id);
}

function CMSMSFilePicker (field_name, url, type, win) {
  var cmsURL = "{$filepickerurl}&type="+type+"&showtemplate=false";
  tinyMCE.activeEditor.windowManager.open({
    file : cmsURL,
    title : '{$filepickertitle}',
    width : '800',
    height : '500',
    resizable : "yes",
    scrollbars : "yes",
    inline : "yes",  {* This parameter only has an effect if you use the inlinepopups plugin! *}
    close_previous : "no"
  }, {
    window : win,
    input : field_name
  });
  return false;
}
{/if}

{
  var mt_elements = document.querySelectorAll('textarea.MicroTiny');
  var mt_ids = [];
  if( mt_elements.length > 0 ) {
    for( var i = 0; i < mt_elements.length; i++ ) {
      mt_ids.push(mt_elements[i].id);
      {if $isfrontend == false && isset($allow_viewsource) && $allow_viewsource}
      var tid = 'mt_toggle_'+i;
      var tname = 'mt_toggle_'+mt_elements[i].id;
      var html = '<div class="htmlbutton"><label><input type="checkbox" id="'+tid+'" name="'+tname+'">{$view_source}</label></div>';
      mt_elements[i].insertAdjacentHTML('afterend',html);
      var elm1 = document.getElementById(tid);
      elm1.addEventListener('click', microTiny_toggle, false);
      {/if}
    }
    microTiny_init(mt_ids.join());
  }
}