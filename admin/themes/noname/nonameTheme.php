<?php
#Modern Theme - A theme for CMS Made Simple
#(c)2005 by Alexander Endresen - alexander@endresen.org
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

class nonameTheme extends CmsAdminThemeBase
{
  private $_errors = array();
  private $_messages = array();

  public function ShowErrors($errors,$get_var = '')
  {
    // cache errors for use in the template.
    if( $get_var != '' && isset($_GET[$get_var]) && !empty($_GET[$get_var]) )
      {
	if( is_array($_GET[$get_var]) )
	  {
	    foreach( $_GET[$get_var] as $one )
	      {
		$this->_errors[] = lang(cleanValue($one));
	      }
	  }
	else
	  {
	    $this->_errors[] = lang(cleanValue($_GET[$get_var]));
	  }
      }
    else if( is_array($errors) )
      {
	foreach( $errors as $one )
	  {
	    $this->_errors[] = $one;
	  }
      }
    else if( is_string($errors) )
      {
	$this->_errors[] = $errors;
      }
  }

  public function ShowMessage($message,$get_var='')
  {
    // cache message for use in the template.
    if( $get_var != '' && isset($_GET[$get_var]) && !empty($_GET[$get_var]) )
      {
	if( is_array($_GET[$get_var]) )
	  {
	    foreach( $_GET[$get_var] as $one )
	      {
		$this->_messages[] = lang(cleanValue($one));
	      }
	  }
	else
	  {
	    $this->_messages[] = lang(cleanValue($_GET[$get_var]));
	  }
      }
    else if( is_array($message) )
      {
	foreach( $message as $one )
	  {
	    $this->_messages[] = $one;
	  }
      }
    else if( is_string($message) )
      {
	$this->_messages[] = $message;
      }
  }

  public function ShowHeader($title_name,$extra_lang_params = array(),$link_text = '',$module_help_type = FALSE)
  {
    if( $title_name ) $this->set_value('pagetitle',$title_name);
    if( is_array($extra_lang_params) && count($extra_lang_params) ) $this->set_value('extra_lang_params',$extra_lang_params);
    $this->set_value('module_help_type',$module_help_type);

    // get the image url.
    $config = cmsms()->GetConfig();
    if ($module_help_type)
      {
	// help for a module.
	$module = '';
	if( isset($_REQUEST['module']) )
	  {
	    $module = $_REQUEST['module'];
	  }
	else if( isset($_REQUEST['mact']) )
	  {
	    $tmp = explode(',',$_REQUEST['mact']);
	    $module = $tmp[0];
	  }
	$icon = "modules/{$module}/images/icon.gif";
	$path = cms_join_path($config['root_path'],$icon);
	if( file_exists($path) )
	  {
	    $url = $config->smart_root_url().'/'.$icon;
	    $this->set_value('module_icon_url',$url);
	  }
      }

    // get the wiki URL and a title for that link.
    $bc = $this->get_breadcrumbs();
    if( $bc )
      {
	$wikiUrl = $config['wiki_url'];
	for( $i = 0; $i < count($bc); $i++ )
	  {
	    $rec = $bc[$i];
	    $title = $rec['title'];
	    if( $module_help_type && $i+1 == count($bc) )
	      {
		$module_name = '';
		if( !empty($_GET['module']) )
		  {
		    $module_name = trim($_GET['module']);
		  }
		else
		  {
		    $tmp = explode(',',$_REQUEST['mact']);
		    $module_name = $tmp[0];
		  }
		$orig_module_name = $module_name;
		$module_name =  preg_replace('/([A-Z])/', "_$1", $module_name);
		$module_name =  preg_replace('/_([A-Z])_/', "$1", $module_name);
		if( $module_name[0] == '_' ) $module_name = substr($module_name,1);
		$wikiUrl .= '/'.$module_name;
	      }
	    else
	      {
		if( ($p = strrchr($title,':')) !== FALSE )
		  {
		    $title = substr($title,0,$p);
		  }
		// find the key of the item with this title.
		$title_key = $this->find_menuitem_by_title($title);
		$wikiUrl .= '/'.lang($title_key[0]);
	      }
	  } // for loop.

	// set the wiki url and wiki help text.
	if( get_preference(get_userid(),'hide_help_links') )
	  {
	    $wikiUrl = str_replace(' ','_',$wikiUrl);
	    $wikiUrl = str_raplce('&amp;','and',$wikiURL);
	    $this->set_value('wiki_url',$wikiUrl);
	    if( !empty($link_text) )
	      {
		$this->set_value('wiki_link_text',$link_text);
	      }
	    else
	      {
		$this->set_value('wiki_link_text',lang('help_external'));
	      }
	  }

	// set the module help url (this should be supplied TO the theme)
	if( $module_help_type == 'both' )
	  {
	    $urlext='?'.CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY];
	    $module_help_url = $config['admin_url'].'/listmodules.php?'.$urlext.'&amp;action=showmodulehelp&amp;module='.$orig_module_name;
	    $this->set_value('module_help_url',$module_help_url);
	  }
      }    
  }

  public function do_header() {}
  public function do_footer() {}
  public function do_toppage($section_name)
  {
    $smarty = cmsms()->GetSmarty();
    $otd = $smarty->template_dir;
    $smarty->template_dir = dirname(__FILE__).'/templates';
    
    if( $section_name )
      {
	$smarty->assign('section_name',$section_name);
	$smarty->assign('nodes',$this->get_navigation_tree($section_name,1,FALSE));
      }
    else
      {
	$nodes = $this->get_navigation_tree(-1,1,FALSE);
	$smarty->assign('nodes',$nodes);
      }

    $smarty->assign('config',cmsms()->GetConfig());
    $smarty->assign('theme',$this);
    $_contents = $smarty->fetch('topcontent.tpl');
    $smarty->template_dir = $otd;
    echo $_contents;
  }

  public function postprocess($html)
  {
    $smarty = cmsms()->GetSmarty();
    $otd = $smarty->template_dir;
    $smarty->template_dir = dirname(__FILE__).'/templates';
    $module_help_type = $this->get_value('module_help_type');

    // get a page title
    $title = $this->get_value('pagetitle');
    if( $title )
      {
	if( !$module_help_type )
	  {
	    // if not doing module help, translate the string.
	    $extra = $this->get_value('extra_lang_params');
	    if( !$extra ) $extra = array();
	    $title = lang($title,$extra);
	  }
      }
    else
      {
	// no title, get one from the breadcrumbs.
	$bc = $this->get_breadcrumbs();
	if( is_array($bc) && count($bc) )
	  {
	    $title = $bc[count($bc)-1]['title'];
	  }
      }
    $smarty->assign('pagetitle',$title);

    // module name?
    if( ($module_name = $this->get_value('module_name')) )
      {
	$smarty->assign('module_name',$module_name);
      }

    // module icon?
    if( ($module_icon_url = $this->get_value('module_icon_url')) )
      {
	$smarty->assign('module_icon_url',$module_icon_url);
      }

    // module_help_url
    if( ($module_help_url = $this->get_value('module_help_url')) )
      {
	$smarty->assign('module_help_url',$module_help_url);
      }

    // wiki help url?
    if( ($wiki_url = $this->get_value('wiki_url')) )
      {
	$smarty->assign('wiki_url',$wiki_url);
	$smarty->assign('wiki_link_test',$this->get_value('wiki_link_text'));
      }

    // and some other common variables,.
    $smarty->assign('content',$html);
    $smarty->assign('config',cmsms()->GetConfig());
    $smarty->assign('theme',$this);
    $smarty->assign('secureparam',CMS_SECURE_PARAM_NAME.'='.$_SESSION[CMS_USER_KEY]);
    $userops = cmsms()->GetUserOperations();
    $smarty->assign('user',$userops->LoadUserByID(get_userid()));

    if( is_array($this->_errors) && count($this->_errors) ) $smarty->assign('errors',$this->_errors);
    if( is_array($this->_messages) && count($this->_messages) ) $smarty->assign('messages',$this->_messages);

    $_contents = $smarty->fetch('pagetemplate.tpl');
    $smarty->template_dir = $otd;
    return $_contents;
  }
}
?>
