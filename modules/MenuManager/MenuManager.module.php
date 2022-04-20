<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#Visit our homepage at: http://www.cmsmadesimple.org
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
#$Id: News.module.php 2114 2005-11-04 21:51:13Z wishy $
if( !isset($gCms) ) exit;

final class MenuManager extends CMSModule
{
  function GetName() { return 'MenuManager'; }
  function GetFriendlyName() { return $this->Lang('menumanager'); }
  function IsPluginModule() { return true; }
  function HasAdmin() { return false; }
  function GetVersion() { return '1.50.3'; }
  function MinimumCMSVersion() { return '1.99-alpha0'; }
  function GetAdminDescription() { return $this->Lang('description'); }
  function GetAdminSection() { return 'layout'; }
  function LazyLoadFrontend() { return TRUE; }
  function LazyLoadAdmin() { return TRUE; }
  function GetHelp($lang='en_US') { return $this->Lang('help'); }
  function GetAuthor() { return 'Ted Kulp'; }
  function GetAuthorEmail() { return 'ted@cmsmadesimple.org'; }
  function GetChangeLog() { return file_get_contents(dirname(__FILE__).'/changelog.inc'); }

  function InstallPostMessage()
  {
    // a little 'post installation' trick, to deactivate this module on install.
    ModuleOperations::get_instance()->ActivateModule($this->GetName(),false);
  }

  public function InitializeFrontend()
  {
    $this->RestrictUnknownParams();
    $this->SetParameterType('collapse',CLEAN_INT);
    $this->SetParameterType('loadprops',CLEAN_INT);
    $this->SetParameterType('items',CLEAN_STRING);
    $this->SetParameterType('number_of_levels',CLEAN_INT);
    $this->SetParameterType('show_all',CLEAN_INT);
    $this->SetParameterType('show_root_siblings',CLEAN_INT);
    $this->SetParameterType('start_level',CLEAN_INT);
    $this->SetParameterType('start_element',CLEAN_STRING); // yeah, it's a string
    $this->SetParameterType('start_page',CLEAN_STRING);
    $this->SetParameterType('template',CLEAN_STRING);
    $this->SetParameterType('excludeprefix',CLEAN_STRING);
    $this->SetParameterType('includeprefix',CLEAN_STRING);
    $this->SetParameterType('childrenof',CLEAN_STRING);
    $this->SetParameterType('root',CLEAN_STRING);
  }

  function InitializeAdmin()
  {
    $this->CreateParameter('collapse', '1', $this->lang('help_collapse'));
    $this->CreateParameter('loadprops', '0', $this->lang('help_loadprops'));
    $this->CreateParameter('items', 'contact,home', $this->lang('help_items'));
    $this->CreateParameter('number_of_levels', '1', $this->lang('help_number_of_levels'));
    $this->CreateParameter('show_all', '0', $this->lang('help_show_all'));
    $this->CreateParameter('show_root_siblings', '1', $this->lang('help_show_root_siblings'));
    $this->CreateParameter('start_level', '2', $this->lang('help_start_level'));
    $this->CreateParameter('start_element', '1.2', $this->lang('help_start_element'));
    $this->CreateParameter('start_page', 'home', $this->lang('help_start_page'));
    $this->CreateParameter('template', '', $this->lang('help_template'));
    $this->CreateParameter('excludeprefix','',$this->Lang('help_excludeprefix'));
    $this->CreateParameter('includeprefix','',$this->Lang('help_includeprefix'));
    $this->CreateParameter('childrenof','',$this->Lang('help_childrenof'));
    $this->CreateParameter('root','',$this->Lang('help_root'));
    $this->CreateParameter('action','',$this->Lang('help_action'));
  }

  /**
   * Recursive function to go through all nodes and put them into a list
   */
  protected function GetChildNodes(&$parentnode, &$nodelist, &$gCms, &$prevdepth, &$count, &$params, $origdepth, &$showparents, $deep = false)
  {
    $includeprefix = '';
    $excludeprefix = '';
    if( isset($params['includeprefix']) ) {
      $includeprefix = trim($params['includeprefix']);
    }
    else if( isset($params['excludeprefix']) ) {
      $excludeprefix = trim($params['excludeprefix']);
    }

    if (isset($params['show_all'])) {
      $show_all = $params['show_all'];
    }
    else {
      $show_all = 0;
    }

    $nadded = 0;
    if (isset($parentnode)) {
      $children = $parentnode->getChildren($deep);
      if (isset($children) && count($children)) {
	reset($children);
	$nchildren = count($children);
	$nc = -1;
	while (list($key) = each($children)) {
	  $nc++;
	  $onechild =& $children[$key];
	  $content = $onechild->GetContent($deep);
	  if( !is_object($content) ) {
	    // uhm, couldn't get the content object... this is strange
	    // should I trigger an error?
	    $nchildren--;
	    continue;
	  }

	  // see if we need to explicitly include this content
	  $includeit = 1;
	  if( $includeprefix != '' ) {
	    $includeit = 0;
	    $prefixes = explode(',',$includeprefix);
	    foreach( $prefixes as $oneprefix ) {
	      if( strstr($content->Alias(),$oneprefix) !== FALSE ) {
		$includeit = 1;
		break;
	      }
	    }
	  }

	  // see if we need to explicitly exclude this content
	  $excludeit = 0;
	  if( $excludeprefix != '' ) {
	    $prefixes = explode(',',$excludeprefix);
	    foreach( $prefixes as $oneprefix ) {
	      if( strstr($content->Alias(),$oneprefix) !== FALSE ) {
		$excludeit = 1;
		break;
	      }
	    }
	  }

	  if ($content != NULL && $content->Active() &&
	      ($includeit && !$excludeit) &&
	      ($content->ShowInMenu() || ($show_all == 1  && !$content->IsSystemPage()))) {
	    $newnode = $this->FillNode($content, $onechild, $nodelist,
				       $count, $prevdepth, $origdepth, $deep, $params);
	    if( $nc == 0 ) {
	      $newnode->first = 1;
	    }
	    if( $nc >= $nchildren - 1 ) {
	      $newnode->last = 1;
	    }
	    $nadded++;

	    //Ok, this one is nasty...
	    //First part checks to see if number_of_levels is set and whether the current depth is deeper than the set number_of_levels depth (opposite logic, actually)
	    //Second part checks to see if showparents is set...  if so, then it checks to see if this hierarchy position is one of them
	    //If either of these things occurs, then try to show the children of this node
	    $n = (int)($newnode->depth);
	    $limit_levels = isset($params['number_of_levels']);
	    $have_depth = $limit_levels && ($n < (int)$params['number_of_levels']);
	    $collapsing = count($showparents) > 0;
	    $in_collapse_path = $collapsing && in_array($content->Hierarchy().'.',$showparents);
	    if( ($limit_levels && $have_depth && !$collapsing) ||
		($in_collapse_path && ($have_depth || !$limit_levels)) || (!$limit_levels && !$collapsing) ) {
	      $tmp = $this->GetChildNodes($onechild, $nodelist, $gCms, $prevdepth, $count, $params, $origdepth, $showparents, $deep);
	      if( $tmp ) {
		$nadded += $tmp;
		$newnode->haschildren = true;
	      }
	    }
	  }
	}
      }
    }
    return $nadded;
  }

  protected function & FillNode(&$content, &$node, &$nodelist, &$count, &$prevdepth, $origdepth, $deep = false, $params = array())
  {
    $onenode = new stdClass();
    $onenode->id = $content->Id();
    $onenode->pagetitle = $content->Name();
    $onenode->url = $content->GetURL();
    $onenode->accesskey = $content->AccessKey();
    $onenode->type = strtolower($content->Type());
    $onenode->tabindex = $content->TabIndex();
    $onenode->titleattribute = $content->TitleAttribute();
    $onenode->modified = $content->GetModifiedDate();
    $onenode->created = $content->GetCreationDate();
    $onenode->hierarchy = $content->Hierarchy();
    $onenode->haschildren = false;
    if( $content->DefaultContent() ) {
      $onenode->default = 1;
    }
    $onenode->depth = count(explode('.', $content->Hierarchy())) - ($origdepth - 1);
    $onenode->prevdepth = $prevdepth - ($origdepth - 1);
    if ($onenode->prevdepth == 0)
      $onenode->prevdepth = 1;
    $onenode->children_exist = false;
    if (is_object($node) && $node->has_children()) {
      $children = $node->get_children();
      if( $children ) {
	for( $i = 0; $i < count($children); $i++ ) {
	  $tmpc = $children[$i]->getContent(false,true,true);
	  if( is_object($tmpc) && $tmpc->Active() && $tmpc->ShowInMenu() ) {
	    $onenode->children_exist = true;
	    break;
	  }
	}
      }
    }
    $prevdepth = $onenode->depth + ($origdepth - 1);
    $onenode->menutext = cms_htmlentities($content->MenuText());
    $onenode->raw_menutext = $content->MenuText();
    $onenode->target = '';
    $onenode->index = $count;
    $onenode->alias = $content->Alias();
    $onenode->parent = false;
    $count++;

    $gCms = \CmsApp::get_instance();
    if( $deep ) {
        $config = \cms_config::get_instance();
      $onenode->extra1 = $content->GetPropertyValue('extra1');
      $onenode->extra2 = $content->GetPropertyValue('extra2');
      $onenode->extra3 = $content->GetPropertyValue('extra3');
      $tmp = $content->GetPropertyValue('image');
      if( !empty($tmp) && $tmp != -1 ) {
	$url = get_site_preference('content_imagefield_path').'/'.$tmp;
	if( !startswith($url,'/') ) $url = '/'.$url;
	$url = $config['image_uploads_url'].$url;
	$onenode->image = $url;
      }
      $tmp = $content->GetPropertyValue('thumbnail');
      if( !empty($tmp) && $tmp != -1 ) {
	$url = get_site_preference('content_thumbnailfield_path').'/'.$tmp;
	if( !startswith($url,'/') ) $url = '/'.$url;
	$url = $config['image_uploads_url'].$url;
	$onenode->thumbnail = $url;
      }
      if ($content->HasProperty('target'))
	$onenode->target = $content->GetPropertyValue('target');
    }

    if( $onenode->id == $gCms->get_content_id() ) {
      $onenode->current = true;
    }
    else {
      $onenode->current = false;
      //So, it's not current.  Lets check to see if it's a direct parent of the current page.
      if( ContentOperations::get_instance()->CheckParentage($onenode->id) ) {
	$onenode->parent = true;
      }
    }

    $nodelist[] = $onenode;

    return $onenode;
  }

  protected function nthPos($str, $needles, $n=1)
  {
    //  Found at: http://us2.php.net/manual/en/function.strpos.php
    //  csaba at alum dot mit dot edu
    //  finds the nth occurrence of any of $needles' characters in $str
    //  returns -1 if not found; $n<0 => count backwards from end
    //  e.g. $str = "c:\\winapps\\morph\\photos\\Party\\Phoebe.jpg";
    //      substr($str, nthPos($str, "/\\:", -2)) => \Party\Phoebe.jpg
    //      substr($str, nthPos($str, "/\\:", 4)) => \photos\Party\Phoebe.jpg
    $pos = -1;
    $size = strlen($str);
    if ($reverse=($n<0)) { $n=-$n; $str = strrev($str); }
    while ($n--) {
      $bestNewPos = $size;
      for ($i=strlen($needles)-1;$i>=0;$i--) {
	$newPos = strpos($str, $needles[$i], $pos+1);
	if ($newPos===false) $needles = substr($needles,0,$i) . substr($needles,$i+1);
	else $bestNewPos = min($bestNewPos,$newPos);
      }
      if (($pos=$bestNewPos)==$size) return -1;
    }
    return $reverse ? $size-1-$pos : $pos;
  }

  final static public function smarty_cms_breadcrumbs($params,$smarty)
  {
    $params['action'] = 'breadcrumbs';
    $params['module'] = 'MenuManager';
    return cms_module_plugin($params,$smarty);
  }

  final public static function page_type_lang_callback($str)
  {
    $mod = cms_utils::get_module('MenuManager');
    if( is_object($mod) ) return $mod->Lang('type_'.$str);
  }

  public static function reset_page_type_defaults(CmsLayoutTemplateType $type)
  {
    if( $type->get_originator() != 'MenuManager' ) {
      throw new CmsLogicException('Cannot reset contents for this template type');
    }

    $fn = null;
    switch( $type->get_name() ) {
    case 'navigation':
      $fn = 'simple_navigation.tpl';
      break;
    }

    $fn = cms_join_path(dirname(__FILE__),'templates',$fn);
    if( file_exists($fn) ) {
      return @file_get_contents($fn);
    }
  }

} // End of class


?>
