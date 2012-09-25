<?php
#-------------------------------------------------------------------------
# Module: ThemeManager - a module for importing and exporting template
#   and stylesheet packages.
# Version: 1.0.6, Robert Campbell <rob@techcom.dyndns.org>
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------

//
// Permissions test
//
if( !$this->CheckPermission('Manage Themes') )
  {
    $this->DisplayErrorPage($id, $params, $return_id,
			    $this->Lang('accessdenied'));
    return;
  }

$this->theme = array();
/*---------------------------------------------------------
 _extractMenuManagerTemplates
 ---------------------------------------------------------*/
function _extractMenuManagerTemplates( $template )
{
  // get all the menumanager tags
  $regex='/\{.*menumanager.*\}/i';
  $res1 = preg_match_all( $regex, $template->content, $t_matches1 );

  $regex='/\{menu.*\}/i';
  $res2 = preg_match_all( $regex, $template->content, $t_matches2 );
  
  $t_matches_a = array();
  if( $res1 !== FALSE && $res1 != 0 )
    {
      $t_matches_a[] = $t_matches1;
    }
  if( $res2 !== FALSE && $res2 != 0 )
    {
      $t_matches_a[] = $t_matches2;
    }

  // $t_matches[1] is all important
  // now parse the cms_module tag, and look for a template parameter
  $result = array();
  foreach( $t_matches_a as $t_matches )
    {
      if( is_array( $t_matches ) && count($t_matches) >= 1 )
	{
	  foreach( $t_matches[0] as $t_match )
	    {
	      if( preg_match( "/template\s*=[\\\"']{0,1}([a-zA-Z0-9._\ \:\-\/]+)[\\\"']{0,1}/i", $t_match, $t_matches3 ) )
		{
		  $result[] = $t_matches3[1];
		}
	      else
		{
		  // no template named, so assume bulletmenu.tpl
		  $result[] = 'default';
		}
	    }
	}
    }
  return $result;
}


/*---------------------------------------------------------
 _extractStylesheetImages
 ---------------------------------------------------------*/
function _extractStylesheetImages($stylesheet)
{
  $regex='/url\s*\(\"*(.*)\"*\)/i';
  preg_match_all( $regex, $stylesheet->value, $matches );
  // $matches[1] is all immportant
  // now strip out any that have ^http in them
  $result = array();
  foreach( $matches[1] as $match )
    {
      if( !startswith($match,'http') || startswith($match,$config['root_url']) )
	{
	  $result[] = $match;
	}
    }
  
  return $result;
}


/*---------------------------------------------------------
 _extractTemplateImages
 ---------------------------------------------------------*/
function _extractTemplateImages($template)
{
  $result = array();
  $config = cmsms()->GetConfig();
  $urls = get_urls($template->content);
  // we're only concerned about the src urls
  foreach( $urls['src'] as $src )
    {
      // if it's an external link we ignore it
      // if it's an internal link, we add it
      if( !startswith($src,'http') || startswith($src,$config['root_url']) )
	{
	  $result[] = $src;
	}
    }

  return $result;
}

/*---------------------------------------------------------
 _trim_stylesheet
 ---------------------------------------------------------*/
function _trim_stylesheet( $name )
{
  return _trim_themename( $name, 'stylesheet');
}

/*---------------------------------------------------------
 _trim_template
 ---------------------------------------------------------*/
function _trim_template( $name )
{
  return _trim_themename( $name, 'template');
}

/*---------------------------------------------------------
 _trim_mmtemplate
 ---------------------------------------------------------*/
function _trim_mmtemplate( $name )
{
  return _trim_themename( $name, 'mmtemplate');
}

/*---------------------------------------------------------
 _trim_themename
 ---------------------------------------------------------*/
function _trim_themename( $old_name, $type = 'stylesheet')
{
  $obj = cms_utils::get_module('ThemeManager');
  if (!isset($obj->theme[$type]) )
  { 
    $obj->theme[$type] = array();
  }
  $t_name_arr = explode(':',$old_name);
  if( count( $t_name_arr ) == 1 )
    {
      $name = trim($t_name_arr[0]);
    }
  else
    {
      $name = trim($t_name_arr[1]);
    }
  $i = 0;
  $orig_name = $name;
  while( isset($obj->theme[$type][$name]) )
  {
    $name = $orig_name.'_'.$i++;
  }
  $obj->theme[$type][$name] = $old_name;
  return $name;
}

//
// begin
//
$gCms = cmsms();
$styleops =& $gCms->GetStylesheetOperations();

$themename = trim($params['input_themename'] );
if( $themename == '' )
  {
    $themename = 'theme';
  }
$themename = str_replace(' ','_',$themename);

$template_ids = array();
$templates = array();
$stylesheet_ids = array();
$associations = array();
$mmtemplatenames = array();
$urls = array();
foreach( $params as $key => $value )
{
  if( !preg_match( '/^export/i', $key ) )
    { 
      continue; 
    } 

  $id = substr( $key, strlen('export') );
  $template_ids[] = $id;

  // we are exporting a template
  // get the template
  $gCms = cmsms();
  $templateops =& $gCms->GetTemplateOperations();
  $templates[$id] = $templateops->LoadTemplateByID( $id );
  if( !$templates[$id] )
    {
      $this->DisplayErrorPage( $id, $params, $returnid, 
			       $this->Lang('error_templatenotfound'));
      return;
    }

  // get the images, etc attached to the template
  $t_urls = _extractTemplateImages($templates[$id]);
  $urls = array_merge( $urls, $t_urls );

  // get the stylesheets attached to that template
  $stylesheets = $styleops->GetTemplateAssociatedStylesheets( $id );
  $assoc = array();
  $assoc['tname'] = $templates[$id]->name;
  $tmp = array();
  foreach( $stylesheets as $cssid )
    {
      $stylesheet_ids[] = $cssid;
      $css = $styleops->LoadStylesheetByID( $cssid );
      $tmp[] = $css->name;
      $cssurls = _extractStylesheetImages($css);
      $urls = array_merge( $urls, $cssurls );
    }
  $assoc['css'] = $tmp;
  $associations[] = $assoc;

  // get any menumanager templates attached to this template
  $t_mmtemplates = _extractMenuManagerTemplates($templates[$id]);
  $mmtemplatenames = array_merge( $mmtemplatenames, $t_mmtemplates );
}

// make sure we're not outputting the same thing twice
$template_ids = array_unique( $template_ids );
$stylesheet_ids = array_unique( $stylesheet_ids );
$mmtemplatenames = array_unique( $mmtemplatenames );
$urls = array_unique( $urls );

// checks to see if there is actually anything to output
if( count( $template_ids ) == 0 || count( $stylesheet_ids ) == 0 )
  {
    // nothing to output
    $this->DisplayErrorPage( $id, $params, $returnid, 
			     $this->Lang('error_nooutput'));
    return;
  }

$instance = $this->GetModuleInstance('MenuManager');
if( !$instance )
  {
    // nothing to output
    $this->DisplayErrorPage( $id, $params, $returnid, 
			     $this->Lang('error_nomenumanager'));
    return;
  }

// preprocess the templates
// make sure that we replace all {cms_module module=menumanager blah,blah,blah}
// where there's no template name specified with one that has a template name
foreach( $templates as $key => $onetemplate )
{
  $newcontent = $onetemplate->content;

  // step 1
  $pattern = "/\{cms_module\s+module\s*=\s*[\\\"']{0,1}menumanager[\\\"']{0,1}\s+template\s*=\s*[\\\"']{0,1}([.a-zA-Z0-9\ \:._\-\/]+)[\\\"']{0,1}(.*?)\}/i";
  $replacement = "{placeholder module='menumanager' template='$1' $2}";
  $newcontent = preg_replace( $pattern, $replacement, $newcontent );

  // step 2
  $pattern = "/\{cms_module\s+module\s*=\s*[\\\"']{0,1}menumanager[\\\"']{0,1}\s+(.*?)\}/i";
  $replacement = "{cms_module module='menumanager' template='bulletmenu' $1}";
  $newcontent = preg_replace( $pattern, $replacement, $newcontent );

  // step 3
  $pattern = "/\{placeholder\s+module\s*=\s*[\\\"']{0,1}menumanager[\\\"']{0,1}\s+(.*?)\}/i";
  $replacement = "{cms_module module='menumanager' $1}";
  $newcontent = preg_replace( $pattern, $replacement, $newcontent );

  // step 4
  $pattern = "/\{menu\s+template\s*=\s*[\\\"']{0,1}}([.a-zA-Z0-9\ \:._\-\/]+)[\\\"']{0,1}(.*?)\}/i";
  $replacement = "{menu template='$1' $2}";
  $newcontent = preg_replace( $pattern, $replacement, $newcontent );

//   // step 5
//   $pattern = "\{menu\s+(.*?)\}/i";
//   $replacement = "{menu template='bulletmenu' $1}";
//   $newcontent = preg_replace( $pattern, $replacement, $newcontent );

  $onetemplate->content = $newcontent;
  $templates[$key] = $onetemplate;	
}
    
// output the xml header
$output = '<?xml version="1.0" encoding="ISO-8859-1"?>';
$output .= $this->dtd;
$output .= "<theme>\n";
$output .= "  <name>".$themename."</name>\n";
$output .= "  <dtdversion>".DTD_VERSION."</dtdversion>\n";

// output the templates
foreach( $templates as $id => $template )
{
  // parse through the html and extract the image tags
  // and images, etc.

  $_name = _trim_template( $template->name );
  $output .= "  <template>\n";
  $output .= "    <tname>".$_name."</tname>\n";
  $output .= "    <tencoding>".$template->encoding."</tencoding>\n";
  $output .= "    <tdata><![CDATA[".base64_encode($template->content)."]]></tdata>\n";
  $output .= "  </template>\n";
  if( $template->stylesheet != '' ) 
    {
      $output .= "  <stylesheet>\n";
      $output .= "    <cssname>".$template->name." -Attached-</cssname>\n";
      $output .= "    <cssmediatype></cssmediatype>\n";
      $output .= "    <cssdata><![CDATA[".base64_encode($template->stylesheet)."]]></cssdata>\n";
      $output .= "  </stylesheet>\n";
    }
}
	
// the menumanager templates 
foreach( $mmtemplatenames as $mmtpl_name )
{
  $mmtemplate = false;
  if( $mmtpl_name == 'default' )
    {
      // if no name was specified for a template, use bulletmenu.tpl
      $mmtemplate = $instance->GetMenuTemplate( 'bulletmenu.tpl' );
      $mmtpl_name = 'bulletmenu';
    }
  else
    {
      $mmtemplate = $instance->GetMenuTemplate( $mmtpl_name );
    }
  if( !$mmtemplate )
    {
      continue;
    }

  // trim any previous theme name off of this name
  $_name = _trim_mmtemplate( $mmtpl_name );

  $output .= "  <mmtemplate>\n";
  $output .= "    <mmtemplate_name>".$_name."</mmtemplate_name>\n";
  $output .= "    <mmtemplate_data>".base64_encode($mmtemplate)."</mmtemplate_data>\n";
  $output .= "  </mmtemplate>\n";
}

// the stylesheets
foreach( $stylesheet_ids as $id )
{
  $stylesheet = $styleops->LoadStylesheetByID( $id );
  $_name = _trim_stylesheet( $stylesheet->name );
  $output .= "  <stylesheet>\n";
  $output .= "    <cssname>".$_name."</cssname>\n";
  $output .= "    <cssmediatype>".$stylesheet->media_type."</cssmediatype>\n";
  $output .= "    <cssdata><![CDATA[".base64_encode($stylesheet->value)."]]></cssdata>\n";
  $output .= "  </stylesheet>\n";	
}

debug_to_log('associations');
debug_to_log($associations);
foreach( $associations as $assoc1 )
{
  $tname = $assoc1['tname'];
  foreach( $assoc1['css'] as $t_assoc2 )
    {
      $assoc2 = array_search( $t_assoc2, $this->theme['stylesheet'] );

      $output .= "  <assoc>\n";
      $output .= "    <assoc_tname>".$tname."</assoc_tname>\n";
      $output .= "    <assoc_cssname>".$t_assoc2."</assoc_cssname>\n";
      $output .= "  </assoc>\n";
    }
}

// the files
$config = cmsms()->GetConfig();
$smarty->left_delimiter = '[[';
$smarty->right_delimiter = ']]';
$path = parse_url($config['root_url']);
foreach( $urls as $oneurl )
{
  $oneurl = $this->ProcessTemplateFromData($oneurl);

  if( startswith($oneurl,$config['root_url']) )
    {
      // absolute url.
      $oneurl = str_replace($config['root_url'],$config['root_path'],$oneurl);
    }
  else if ( startswith($oneurl,'/') )
    {
      // relative url starting with /
      $base_path = $config['root_path'];
      if (isset($path['path']))
	{
	  $base_path = str_replace($path['path'],'',$config['root_path']);
	}
      $oneurl = $base_path.$oneurl;

    }
  else
    {
      // relative url without a /
      $tmpurl = cms_join_path($config['root_path'],'tmp','cache',$oneurl);
      if ( !file_exists($tmpurl) )
	{
	  // relative to tmp/cache I guess.
	  $oneurl = cms_join_path($config['root_path'],$oneurl);
	}
      else
	{
	  $oneurl = $tmpurl;
	}
    }
  if( !file_exists($oneurl) )
    {
      debug_to_log("file not found url: $origurl -  file:$oneurl");
      continue;
    }
  $contents = file_get_contents( $oneurl );
  if( !$contents ) 
    {
      debug_to_log('unable to read: '.$oneurl);
      continue;
    }
  debug_to_log('converted: '.$origurl.' to '.$oneurl);
  $encoded = base64_encode( $contents );
  $output .= "  <reference>\n";
  $output .= "    <refname>".basename($oneurl)."</refname>\n";
  $output .= "    <refencoded>1</refencoded>\n";
  $output .= "    <reflocation>$oneurl</reflocation>\n";
  $output .= "    <refdata><![CDATA[".$encoded."]]></refdata>\n";
  $output .= "  </reference>\n";
}
$smarty->left_delimiter = '{';
$smarty->right_delimiter = '}';

// and the theme tail
$output .= "</theme>\n";

// and spit it out
header('Content-Description: File Transfer');
header('Content-Type: application/force-download');
header('Content-Disposition: attachment; filename='.$themename.'.xml');
//         header('Content-Type: text/xml');
//         header('Content-Length: '.strlen($output));

// todo, but good enough for now.
while(@ob_end_clean());
echo $output;
exit();
?>
