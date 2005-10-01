<?php
# This is a string replacement module (c) 2005 by INTESOL SRL (Mihai Cimpoeru & Sorin Sbarnea)
# to enhance the project
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://cmsmadesimple.sf.net
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

class PngTransparencyIE extends CMSModule
{
    function GetName()
  {
      return 'PngTransparencyIE';
  }
  
    function GetVersion()
  {
      return '0.2';
  }
  
    function GetHelp($lang = 'en_US')
  {
      return '
        <h3>What does this do?</h3>
        <p>As you maybe already know the PNG Transparency in Internet Explorer it\'s buggy and Microsoft will repair this only in a future version that will work only on a newer operating system. This module will do some hacks in order to make Internet Explorer to display them correctly. </p>
        <p>This plugin will solve the problem for users of IE 5.5 or grater. As a developer I think that you are already using Firefox for a long time.</p>
        <p>Work was based on already written php script at <a href="http://koivi.com/ie-png-transparency">koivi.com</a>.</p>
      ';
  }
  
    function GetDescription($lang = 'en_US')
  {
      return 'Transparent workaround Internet Explorer bug for PNG files with transparency. Will work site wide and should not interfear with other plugins.';
  }
  
    function GetChangeLog()
  {
      return '<p>0.1 First version</p>';
      return '<p>0.2 Updated PNG Alpha IMG Tag Replacer (Justin Koivisto) to 2.0.11</p>';
 
  }
  
    function GetAuthor()
  {
      return 'INTERSOL SRL';
  }
  
    function GetAuthorEmail()
  {
      return 'support@intersol.ro';
  }
  

    function IsContentModule()
  {
      return true;
  }
  
    function HasAdmin()
  {
    return false;
  }
  
    function InstallPostMessage()
  {
    return 'No further action required.';
  }
  
      function Install()
  {      
      $this->Audit( 0, 'Module_PngTransparencyIE', 'Install V'.$this->getVersion());
  }
  
  function Upgrade($oldversion, $newversion) {
    
    $current_version = $oldversion;
    $ret = 0; 
    
    switch($current_version)
    {
      default:break;
    }
    if ($current_version == $this->GetVersion()) {
      $this->Audit( 0, 'Module_PngTransparencyIE', 'Update V'.$oldversion.' &rarr; V'.$newversion);
      return FALSE;
    }
    else return 'Module_PngTransparencyIE upgrade failed!';
  }
  
  
    function Uninstall()
  {
      $this->Audit( 0, 'Module_PngTransparencyIE', 'Uninstall V'.$this->getVersion());
  }
  
    function ContentPostRender(&$content)
    {
    	$content = $this->replacePngTags($content,'/modules/PngTransparencyIE');
    }
  /**
    * ------------------------------------------------------------------
    * Navigation Related Functions
    * ------------------------------------------------------------------
  */

    /**
      * Used for navigation between "pages" of a module.  Forms and links should
      * pass an action with them so that the module will know what to do next.
      * By default, DoAction will be passed 'default' and 'defaultadmin',
      * depending on where the module was called from.  If being used as a module
      * or content type, 'default' will be passed.  If the module was selected
      * from the list on the admin menu, then 'defaultadmin' will be passed.
      *
      * @param string Name of the action to perform
      * @param string The ID of the module
      * @param string The parameters targeted for this module
    */
      function DoAction($name, $id, $params, $returnid='')
    {    
          return "";

    }
    
/**
*  KOIVI PNG Alpha IMG Tag Replacer for PHP (C) 2004 Justin Koivisto
*  Version 2.0.11
*  Last Modified: 8/4/2005
*  
*  This library is free software; you can redistribute it and/or modify it
*  under the terms of the GNU Lesser General Public License as published by
*  the Free Software Foundation; either version 2.1 of the License, or (at
*  your option) any later version.
*  
*  This library is distributed in the hope that it will be useful, but
*  WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
*  or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public
*  License for more details.
*  
*  You should have received a copy of the GNU Lesser General Public License
*  along with this library; if not, write to the Free Software Foundation,
*  Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*  
*  Full license agreement notice can be found in the LICENSE file contained
*  within this distribution package.
*  
*  Justin Koivisto
*  justin.koivisto@gmail.com
*  http://www.koivi.com
*
*  Modifies IMG and INPUT tags for MSIE5+ browsers to ensure that PNG-24
*  transparencies are displayed correctly.  Replaces original SRC attribute
*  with a binary transparent PNG file (spacer.png) that is located in the same
*  directory as the orignal image, and adds the STYLE attribute needed to for
*  the browser. (Matching is case-insensitive. However, the width attribute
*  should come before height.
*  
*  Also replaces code for PNG images specified as backgrounds via:
*  background-image: url(image.png); or background-image: url('image.png');
*  When using PNG images in the background, there is no need to use a spacer.png
*  image. (Only supports inline CSS at this point.)
*  
*  @param string $x  String containing the content to search and replace in.
*  @param string $img_path   The path to the directory with the spacer image relative to
*                      the DOCUMENT_ROOT. If none os supplied, the spacer.png image
*                      should be in the same directory as PNG-24 image.
*  @param string $sizeMeth   String containing the sizingMethod to be used in the
*                      Microsoft.AlphaImageLoader call. Possible values are:
*                      crop - Clips the image to fit the dimensions of the object.
*                      image - Enlarges or reduces the border of the object to fit
*                              the dimensions of the image.
*                      scale - Default. Stretches or shrinks the image to fill the borders
*                              of the object.
*  @param bool   $inScript  Boolean flag indicating whether or not to replace IMG tags that
*                      appear within SCRIPT tags in the passed content. If used, may cause
*                      javascript parse errors when the IMG tags is defined in a javascript
*                      string. (Which is why the options was added.)
*  @return string
*/
function replacePngTags($x,$img_path='',$sizeMeth='scale',$inScript=FALSE){
    $arr2=array();
    // make sure that we are only replacing for the Windows versions of Internet
    // Explorer 5.5+
    $msie='/msie\s(5\.[5-9]|[6-9]\.[0-9]*).*(win)/i';
    if( !isset($_SERVER['HTTP_USER_AGENT']) ||
        !preg_match($msie,$_SERVER['HTTP_USER_AGENT']) ||
        preg_match('/opera/i',$_SERVER['HTTP_USER_AGENT']))
        return $x;

    if($inScript){
        // first, I want to remove all scripts from the page...
        $saved_scripts=array();
        $placeholders=array();
        preg_match_all('`<script[^>]*>(.*)</script>`isU',$x,$scripts);
        for($i=0;$i<count($scripts[0]);$i++){
            $x=str_replace($scripts[0][$i],'replacePngTags_ScriptTag-'.$i,$x);
            $saved_scripts[]=$scripts[0][$i];
            $placeholders[]='replacePngTags_ScriptTag-'.$i;
        }
    }

    // find all the png images in backgrounds
    preg_match_all('/background-image:\s*url\(([\\"\\\']?)([^\)]+\.png)\1\);/Uis',$x,$background);
    for($i=0;$i<count($background[0]);$i++){
        // simply replace:
        //  "background-image: url('image.png');"
        // with:
        //  "filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(
        //      enabled=true, sizingMethod=scale, src='image.png');"
        // I don't think that the background-repeat styles will work with this...
        $x=str_replace($background[0][$i],'filter:progid:DXImageTransform.'.
                'Microsoft.AlphaImageLoader(enabled=true, sizingMethod='.$sizeMeth.
                ', src=\''.$background[2][$i].'\');',$x);
    }

    // find all the IMG tags with ".png" in them
    $pattern='/<(input|img)[^>]*src=([\\"\\\']?)([^>]*\.png)\2[^>]*>/i';
    preg_match_all($pattern,$x,$images);
    for($num_images=0;$num_images<count($images[0]);$num_images++){
        // for each found image pattern
        $original=$images[0][$num_images];
        $quote=$images[2][$num_images];
        $atts=''; $width=0; $height=0; $modified=$original;

        // We do this so that we can put our spacer.png image in the same
        // directory as the image - if a path wasn't passed to the function
        if(empty($img_path)){
            $tmp=split('[\\/]',$images[3][$num_images]);
            $this_img=array_pop($tmp);
            $img_path=join('/',$tmp);
            if(empty($img_path)){
                // this was a relative URI, image should be in this directory
                $tmp=split('[\\/]',$_SERVER['SCRIPT_NAME']);
                array_pop($tmp);    // trash the script name, we only want the directory name
                $img_path=join('/',$tmp).'/';
            }else{
                $img_path.='/';
            }
        }else if(substr($img_path,-1)!='/'){
            // in case the supplied path didn't end with a /
            $img_path.='/';
        }

        // If the size is defined by styles, find them
        preg_match_all(
            '/style=([\\"\\\']).*(\s?width:\s?([0-9]+(px|%));).*'.
            '(\s?height:\s?([0-9]+(px|%));).*\\1/Ui',
               $images[0][$num_images],$arr2); 
        if(is_array($arr2) && count($arr2[0])){
            // size was defined by styles, get values
            $width=$arr2[3][0];
            $height=$arr2[6][0];

            // remove the width and height from the style
            $stripper=str_replace(' ','\s','/('.$arr2[2][0].'|'.$arr2[5][0].')/');
            // Also remove any empty style tags
            $modified=preg_replace(
                '`style='.$arr2[1][0].$arr2[1][0].'`i',
                '',
                preg_replace($stripper,'',$modified));
        }else{
            // size was not defined by styles, get values from attributes
            preg_match_all('/width=([\\"\\\']?)([0-9%]+)\\1/i',$images[0][$num_images],$arr2);
            if(is_array($arr2) && count($arr2[0])){
                $width=$arr2[2][0];
                if(is_numeric($width))
                    $width.='px';
    
                // remove width from the tag
                $modified=str_replace($arr2[0][0],'',$modified);
            }
            preg_match_all('/height=([\\"\\\']?)([0-9%]+)\\1/i',$images[0][$num_images],$arr2);
            if(is_array($arr2) && count($arr2[0])){
                $height=$arr2[2][0];
                if(is_numeric($height))
                    $height.='px';
    
                // remove height from the tag
                $modified=str_replace($arr2[0][0],'',$modified);
            }
        }

        if($width==0 || $height==0){
            // width and height not defined in HTML attributes or css style, try to get
            // them from the image itself
            // this does not work in all conditions... It is best to define width and
            // height in your img tag or with inline styles..
            if(file_exists($_SERVER['DOCUMENT_ROOT'].$img_path.$images[3][$num_images])){
                // image is on this filesystem, get width & height
                $size=getimagesize($_SERVER['DOCUMENT_ROOT'].$img_path.$images[3][$num_images]);
                $width=$size[0].'px';
                $height=$size[1].'px';
            }else if(file_exists($_SERVER['DOCUMENT_ROOT'].$images[3][$num_images])){
                // image is on this filesystem, get width & height
                $size=getimagesize($_SERVER['DOCUMENT_ROOT'].$images[3][$num_images]);
                $width=$size[0].'px';
                $height=$size[1].'px';
            }
        }
        
        // end quote is already supplied by originial src attribute
        $replace_src_with=$quote.$img_path.'spacer.png'.$quote.' style="width: '.$width.
            '; height: '.$height.'; filter: progid:DXImageTransform.'.
            'Microsoft.AlphaImageLoader(src=\''.$images[3][$num_images].'\', sizingMethod='.
            $sizeMeth.');"';

        // now create the new tag from the old
        $new_tag=str_replace($quote.$images[3][$num_images].$quote,$replace_src_with,
            str_replace('  ',' ',$modified));
        // now place the new tag into the content
        $x=str_replace($original,$new_tag,$x);
    }
    
    if($inScript){
        // before the return, put the script tags back in. (I was having problems when there was
        // javascript that had image tags for PNGs in it when using this function...
        $x=str_replace($placeholders,$saved_scripts,$x);
    }
    
    return $x;
}
}
?>
