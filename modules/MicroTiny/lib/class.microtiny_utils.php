<?php
/*
 #CMS - CMS Made Simple
 #(c)2004 by Simon Brown (simon@uptoeleven.ws)
 #This project's homepage is: http://www.cmsmadesimple.org
 #
 #This program is free software; you can redistribute it and/or modify
 #it under the terms of the GNU General Public License as published by
 #the Free Software Foundation; either version 2 of the License, or
 #(at your option) any later version.
 #
 #This program is distributed in the hope that it will be useful,
 #but WITHOUT ANY WARRANTY; without even the implied warranty of
 #MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 #GNU General Public License for more details.
 #You should have received a copy of the GNU General Public License
 #along with this program; if not, write to the Free Software
 #Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 #
 #$Id$
 #
 */
 
class microtiny_utils {

  static private $_textareas = array();

	/**
	* Constructor
	*
	* @since 1.0
	*/	
	public function __construct() { }

	/**
	 * add textarea name
	 */
	static private function add_textarea($name)
	{
	  if( !in_array($name,self::$_textareas) )
	    {
	      self::$_textareas[] = $name;
	    }
	}

	/**
	* Module API wrapper function
	*
	* @since 1.0
	* @return string
	*/	
	static public function WYSIWYGTextarea($name='textarea',$columns='80',$rows='15',$encoding='',$content='',$stylesheet='',$addtext='') {

	  $mod = cms_utils::get_module('MicroTiny');

		// Check if we are in object instance
		if(!is_object($mod)) return false; // TODO: some error message?
	
		// Force WYSIWYG on
		$mod->wysiwygactive = true; // Stikki: calling this before TODO cause whole thing failing, check this Silmarillion.

		if ($stylesheet != '') {
			$_SESSION["microtiny_live_templateid"]=substr($stylesheet, strpos($stylesheet,"=")+1);
		} else {
		  $obj = cms_utils::get_app_data('editing_content');
		  if( is_object($obj) ) {
		    $mod->templateid = $obj->TemplateId();
		  }
		  else {
		    $tplops = cmsms()->GetTemplateOperations();
		    $templateid = $tplops->LoadDefaultTemplate();
		    $mod->templateid = $templateid->id;
		  }
		}
		
		$origname = $name;
		if( !startswith($name,'mt_') ) $name = 'mt_'.$name;
		self::add_textarea($name);
		
		$rows = max(5,min(50,$rows));
		$output = '';
		$output .= '<textarea id="'.$name.'" name="'.$origname.'" cols="'.$columns.'" rows="'.$rows.'" '.$addtext.'>'.cms_htmlentities($content,ENT_NOQUOTES,get_encoding($encoding)).'</textarea>';
		global $CMS_ADMIN_PAGE;
		if( $CMS_ADMIN_PAGE && $mod->CheckPermission('MicroTiny View HTML Source') )
		  {
		    $output .= '<span style="display:block; margin-top:10px;"><input type="checkbox" id="toggle_'.$name.'" onclick="toggleMicroTiny(\''.$name.'\');"/><label for="toggle_'.$name.'">'.$mod->Lang('view_html').'</label></span>';
		  }
		return $output;
	}

	/**
	* Module API wrapper function
	*
	* @since 1.0
	* @return string
	*/		
	static public function WYSIWYGGenerateHeader($htmlresult='', $junk=false) {

	  $mod = cms_utils::get_module('MicroTiny');
		// Check if we are in object instance
		if(!is_object($mod)) return false; // TODO: some error message?
		
		// Init
		$config = cms_utils::get_config();

		$frontend = FALSE;
		global $CMS_ADMIN_PAGE;
		if( !isset($CMS_ADMIN_PAGE) ) $frontend = TRUE;

		$languageid = self::GetLanguageId($frontend);

		$fn = self::SaveStaticConfig($frontend,$mod->templateid,$languageid);

		if ($mod->WYSIWYGactive()) {
		  $output='<script type="text/javascript" src="'.$config->smart_root_url().'/modules/MicroTiny/tinymce/tiny_mce.js"></script>';
		  $configurl = $config->smart_root_url().'/tmp/cache/'.$fn.'?t='.time();
		  $output.='<script type="text/javascript" src="'.$configurl.'"></script>';			
		} else {		  
		  $output="<!-- MicroTiny Session vars empty -->";
		}
		
		return $output;
	}	
	
	/**
	* Generate dynamic config file
	*
	* @since 1.0
	* @param boolean Frontend true/false
	* @param string Templateid
	* @param string A2 Languageid
	* @return string
	*/	
	static public function GenerateConfig($frontend=false, $templateid="", $languageid="en") 
	  {

	  $mod = cms_utils::get_module('MicroTiny');
		// Check if we are in object instance
		if(!is_object($mod)) return false; // TODO: return static file if this fails, or something
		
		// Init
		$config = cms_utils::get_config();	
		$result="";		
		$linker="";		
		
		if ($frontend) {  
			$mod->smarty->assign("isfrontend",true);
		} else {
			$mod->smarty->assign("isfrontend",false);
			$result .= self::GetCMSLinker();
			$linker="cmslinker,";
		}

		if( count(self::$_textareas) )
		  {
		    $tmp = implode(',',self::$_textareas);
		    self::$_textareas = array();
		    $mod->smarty->assign('textareas',$tmp);		    
		  }

		if( $templateid <= 0 )
		  {
		    $pageinfo = cmsms()->get_variable('pageinfo');
		    if( is_object($pageinfo) )
		      {
			$templateid = $pageinfo->template_id;
		      }
		    else
		      {
			$contentobj = cms_utils::get_app_data('editing_content');
			if( is_object($contentobj) )
			  {
			    $templateid = $contentobj->TemplateId();
			  }
			else
			  {
			    $templateops = cmsms()->GetTemplateOperations();
			    $dflt_template = $templateops->LoadDefaultTemplate();
			    if( is_object($dflt_template) ) $templateid = $dflt_template->id;
			  }
		      }
		  }
		if( $templateid > 0 )
		  {
		    $mod->smarty->assign('templateid',$templateid);
		  }

		$urlext="";
		if (isset($_SESSION[CMS_USER_KEY])) {
		  $urlext=CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
		}
		$mod->smarty->assign("urlext",$urlext);
		//,pasteword,,|,undo,redo
		$image="";
		if ($mod->GetPreference("allowimages",0) && !$frontend ) {
			$image=",image,|";
		}
		$toolbar="undo,|,bold,italic,underline,|,cut,copy,paste,pastetext,removeformat,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,".$linker."link,unlink,|".$image.",formatselect"; //,separator,styleselect

		// handle css styles... newline OR comma separated (why, kinda dumb?)
		$tmp = $mod->GetPreference('css_styles');
		$tmp = str_replace("\r\n","\n",$tmp);
		$tmp = explode("\n",$tmp);
		$tmp2 = array();
		foreach( $tmp as $one )
		  {
		    $one = trim($one);
		    if( empty($one) ) continue;

		    $tmp3 = explode(',',$one);
		    foreach( $tmp3 as $one2 )
		      {
			$tmp2[] = trim($one2);
		      }
		  }

		$tmp3 = array();
		foreach( $tmp2 as $one )
		  {
		    $tmp4 = explode('=',trim($one),2);
		    if( count($tmp4) == 1 )
		      {
			$tmp3[$tmp4[0]] = $tmp4[0];
		      }
		    else
		      {
			$tmp3[$tmp4[0]] = $tmp4[1];
		      }
		  }

		$css_styles = '';
		foreach( $tmp3 as $key => $value )
		  {
		    $css_styles .= $key.'='.$value.';';
		  }
		$css_styles = substr($css_styles,0,-1);
		if ($css_styles!='') {
		  $toolbar.=",separator,styleselect";
		  $mod->smarty->assign("css_styles",$css_styles);
		}

		// give the rest to smarty.
		$mod->smarty->assign('show_statusbar',$mod->GetPreference('show_statusbar',0));
		$mod->smarty->assign('allow_resize',$mod->GetPreference('allow_resize',0));
		$mod->smarty->assign('strip_background',$mod->GetPreference('strip_background',1));
		$mod->smarty->assign('force_blackonwhite',$mod->GetPreference('force_blackonwhite',0));
		$mod->smarty->assign("toolbar",$toolbar);				
		$mod->smarty->assign("language",$languageid);			
		$mod->smarty->assign("filepickertitle",$mod->Lang("filepickertitle"));
        $fpurl=$mod->create_url("","filepicker");
        $fpurl=str_replace("&amp;","&",$fpurl);
        $mod->smarty->assign("filepickerurl", $fpurl);
		
		$result .= $mod->ProcessTemplate('microtinyconfig.tpl');
		return $result;

	}	
	
	/**
	* Generates TinyMCE Entry object
	*
	* @since 1.0
	* @param string $menu
	* @param string $entry
	* @return string
	*/	
	public static function AddEntry($menu,$entry) {
	
	  // Check if we are in object instance
	  $mod = cms_utils::get_module('MicroTiny');
	  if(!is_object($mod)) return false; // TODO: some error?

	  // Init
	  $config = cms_utils::get_config();
	  $link="";
	  $result="";
	  
	  //{format : 'text'}
	  $menutext=$entry->Hierarchy()." ".$entry->MenuText();
	  if (strlen($menutext)>30) {
	    $menutext=htmlspecialchars(substr($menutext,0,30),ENT_QUOTES)." &#133;";
	  } else {
	    $menutext=htmlspecialchars($menutext,ENT_QUOTES);
	  }
	  $result.="
						".$menu.".add({title : '".$menutext."', onclick : function() {
							var sel=tinyMCE.activeEditor.selection.getContent();
							if (sel=='') sel='".htmlspecialchars($entry->MenuText(),ENT_QUOTES)."';";

		/*if ($mod->GetPreference("cmslinkerstyle","selflink")=="a") {
			if (($config['url_rewriting'] != 'none')
			 //&& $config['use_hierarchy'] == true)
    {
		  if ($config['url_rewriting'] == 'mod_rewrite') {
			  $link = $config['root_url']."/".$entry->HierarchyPath().$config['page_extension'];
			} else {
				$link= $config['root_url']."/index.php/".$entry->HierarchyPath().$config['page_extension'];
			  }
		  }	else {
			//if ($tiny->GetPreference("cmslinkerstyle","selflink")=="a") {
			$link="index.php?".$config['query_var']."=".$entry->Alias();
			//}
		  }
			$result.="
							tinyMCE.activeEditor.execCommand('mceInsertContent', false, '<a href=\"".$link."\">'+sel+'</a>');";
		} else {
			$result.="
							tinyMCE.activeEditor.execCommand('mceInsertContent', false, \"{cms_selflink page='".$entry->Alias()."' text='\"+sel+\"'}\");";
		}
		$*/
    $href="'".$entry->Alias()."'";
    $result.="
							tinyMCE.activeEditor.execCommand('mceInsertContent', false, \"<a href=\\\"{cms_selflink href=".$href."}\\\">\"+sel+\"</a>\");";
    $result.="
						}});
		";
		return $result;
	}	
	
	/**
	* Generates TinyMCE sub object
	*
	* @since 1.0
	* @param string $menu
	* @param string $entry
	* @param string $result // Stikki: why?
	* @return string
	*/	
	public static function AddSub($menu,$entry,&$result) {
	
		$menutext=$entry->Hierarchy()." ".$entry->MenuText();
		
		if (strlen($menutext)>30) {
			$menutext=htmlspecialchars(substr($menutext,0,30),ENT_QUOTES)." &#133;";
		} else {
			$menutext=htmlspecialchars($menutext,ENT_QUOTES);
		}
		
		$newmenu=$menu."m";
		$result.="					var ".$newmenu." = ".$menu.".addMenu({title : '".$menutext."'});
		";
		
		if ($entry->Type()!="sectionheader") {
		  $result.= self::AddEntry($newmenu,$entry);
		  $result.="					".$newmenu.".addSeparator();
		";
		}
		
		return $newmenu;
	}
	
	/**
	* Get CMSLinker for TinyMCE
	*
	* @since 1.0
	* @return string
	*/		
	public static function GetCMSLinker() {

	  $mod = cms_utils::get_module('MicroTiny');
		$result="";
		$config = cms_utils::get_config();
		$result.="
		//Creates a new plugin class and a custom listbox
		tinymce.create('tinymce.plugins.CMSLinkerPlugin', {
		createControl: function(n, cm) {
			switch (n) {
				case 'cmslinker':
					var c = cm.createMenuButton('cmslinker', {
						title : '".$mod->Lang("cmsmslinker")."',
						image : '".$config->smart_root_url()."/modules/MicroTiny/images/cmsmslink.gif',
						icons : false
					});

					c.onRenderMenu.add(function(c, m) {
		";

		$content_ops = cmsms()->GetContentOperations();
		$content_array=$content_ops->GetAllContent();

		$level=0;
		$menu="m";

		foreach ($content_array as $one) {
		if ($one->Active()!=1) continue;
		if ($one->FriendlyName() == 'Separator') {
			continue;
		}
			//print_r($one);
		$thislevel=substr_count($one->Hierarchy(),".");
		//echo $thislevel.":".$level;
		if ($thislevel<$level) {
		$menu=substr($menu,($level-$thislevel));
				$level-=($level-$thislevel);

			}
		//if ($thislevel==$level) {
			if ($one->ChildCount()>0) {
				$menu = self::AddSub($menu,$one,$result);
				$level++;
			} else {
				$result .= self::AddEntry($menu,$one);
			}
		}

		$result.="
					});

					// Return the new menu button instance
					return c;
			}

			return null;
		}
		});

		// Register plugin with a short name
		tinymce.PluginManager.add('cmslinker', tinymce.plugins.CMSLinkerPlugin);

		";
		return $result;
	}	

	/**
	* Get Language ID
	*
	* @since 1.0
	* @return string
	*/			
	public static function GetLanguageId() {
	
		$mylang = get_preference(get_userid(FALSE),'default_cms_language');
		if ($mylang=="") return "en"; //Lang setting "No default selected"
		$mylang = substr($mylang,0,2);
		
		switch ($mylang) {			
			case "tw" : return strtolower($mylang);
			default : return $mylang;
		}
	}


	/**
	* Save Static configuration
	*
	* @since 1.0
	* @param boolean $frontend	
	* @param string $templateid	
	* @param string $languageid	
	* @return string
	*/		
	public static function SaveStaticConfig($frontend=false, $templateid="", $languageid="") { 
	
	  $configcontent = self::GenerateConfig($frontend, $templateid, $languageid);
	  $fn = cms_join_path(TMP_CACHE_LOCATION,'mt_'.session_id().'.js');
	  $res = file_put_contents($fn,$configcontent);
	  if( !$res ) return;
	  return basename($fn);
	}
	
	/**
	* Get Thumbnail File
	*
	* @since 1.0
	* @param string $file	
	* @param string $path	
	* @param string $url	
	* @return string
	*/		
	public static function GetThumbnailFile($file,$path,$url) {
	
		$image="";
		$imagepath = self::Slashes($path."/thumb_".$file);
		$imageurl = self::Slashes($url."/thumb_".$file);
		//echo "<pre>".$imageurl."</pre><br/>";
		if (!file_exists($imagepath)) {
		  //echo $imagepath;
		  $image="";//$mod->GetFileIcon($file["ext"],$file["dir"]);
		} else {
		  $image="<img src='".$imageurl."' alt='".$file."' title='".$file."' />";
		}
		
		return $image;
	}


	/**
	* Fix Slashes
	*
	* @since 1.0	
	* @return string
	*/		
	static private function Slashes($url) {
	
		$result=str_replace("\\","/",$url);
		return $result;
	}	
	

} // end of class
?>
