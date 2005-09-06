<?php
#CMS - CMS Made Simple
#(c)2004 by Simon Brown (simon@uptoeleven.ws)
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
#
#TODO - install and uninstall, activate and deactivate, choose non-wysiwyg to actually work

class FCKeditorX extends CMSModule
{
  function Install(){
    $this->SetPreference('FCKConfig.SkinSelected', 'default');
    $this->SetPreference('FCKConfig.CMSMSLanguage', '1');
    $this->SetPreference('FCKConfig.EnableXHTML', '1');
    $this->SetPreference('FCKConfig.ContextMenu', '1'); 
  }
  
  function Uninstall(){
    $this->RemovePreference('FCKConfig.SkinSelected');
    $this->RemovePreference('FCKConfig.CMSMSLanguage');
    $this->RemovePreference('FCKConfig.EnableXHTML');
    $this->RemovePreference('FCKConfig.ContextMenu'); 
  }

	function Upgrade($oldversion, $newversion)
	{
		$this->Install();
	}  

	function GetName()
	{
		return 'FCKeditorX';
	}

	function IsPluginModule()
	{
		return true;
	}

	function HasAdmin()
	{
		return true;
	}

	function GetVersion()
	{
		return '0.7';
	}
	
	function IsWYSIWYG() {
		return true;
	}

	function WYSIWYGTextarea($name='textarea',$columns='80',$rows='15',$encoding='',$content='',$stylesheet='')
	{
    include_once(dirname(__FILE__)."/FCKeditor/fckeditor.php");
    
    //+++++++++++++++++++++++++++++++++++
    // PREPARATION custom style TO DISPLAY
    //+++++++++++++++++++++++++++++++++++
    $stylesheet = str_replace ( "../", "", $stylesheet);


    // CHECK if there is a custom CSS to show
    if (strcmp($stylesheet, "") != 0){
    
    
        // DELETE FCKeditor style EDITORAREA
        if (file_exists($this->cms->config["root_path"]."/tmp/cache/fck_editorarea.css"))
            @unlink($this->cms->config["root_path"]."/tmp/cache/fck_editorarea.css");

        // Copy the new templates for FCKeditor templates EDITORAREA
        // For future customize of templates
        if (!file_exists($this->cms->config["root_path"]."/tmp/cache/fcktemplates.xml"))
          if (@!copy(  $this->cms->config["root_path"]."/modules/FCKeditorX/FCKeditor/fcktemplates.xml", 
                     $this->cms->config["root_path"]."/tmp/cache/fcktemplates.xml")
            ) {
            echo "Copia di fcktemplates.xml non riuscita ...<br>\n";
          }
        
        
        $templateid = substr($stylesheet, strpos($stylesheet,"=")+1);
        $result = get_stylesheet($templateid);
        //Write the NEW  "fck_editorarea.css" for FCKeditor
        if($fp = fopen($this->cms->config["root_path"]."/tmp/cache/fck_editorarea.css", "w")){    
            if (!fwrite($fp, $result['stylesheet'])) {
                echo "Non si riesce a scrivere nel file ($filename)";
                exit;
            }
            fclose($fp);
        }
        
        //CLASS parser of a CSS file
        include_once(dirname(__FILE__).'/cssparser.inc.php');
        
        $cssparser = new cssparser();
        
        // $html = array("ADDRESS","APPLET","AREA","A","BASE","BASEFONT","BIG","BLOCKQUOTE","BODY","BR","B", "CAPTION","CENTER","CITE","CODE","DD","DFN","DIR","DIV","DL","DT","EM","FONT","FORM","H1","H2","H3","H4","H5","H6","HEAD","HR","HTML","IMG","INPUT","ISINDEX","I","KBD","LINK","LI","MAP","MENU","META","OL","OPTION","PARAM","PRE","P","SAMP","SCRIPT","SELECT","SMALL","STRIKE","STRONG","STYLE","SUB","SUP","TABLE","TD","TEXTAREA","TH","TITLE","TR","TT","UL","U","VAR");
        // LIST of HTML tag that cannot have a custom CSS
        
        $cssparser->Parse($this->cms->config["root_path"]."/tmp/cache/fck_editorarea.css");
        
        //+++++++++++++++++++++++++++++++++++++++++++++++
        // Construction of "fckstyles.xml" for FCKeditor
        //+++++++++++++++++++++++++++++++++++++++++++++++
        $styles  = '<?xml version="1.0" encoding="utf-8" ?>'."\n";
        $styles .= '<Styles>'."\n";
        
        //LOOP on ALL CSS styles
        foreach ($cssparser->css as $key => $value) {
          //SKIP CSS that are a combination of CSS
          //SKIP CSS that are binded on an EVENT
          if ((strpos($key, " ") === FALSE)&&(strpos($key, ":") === FALSE)){
              $pieces = explode(".", $key, 2);
    
              if (strcmp($pieces[0], "") != 0)
                continue;//$style_elem = $pieces[0];
              else 
                $style_elem = "span";
              
              if (strcmp($pieces[1], "") != 0)
                $style_class_name = "$pieces[1]";
              else 
                $style_class_name = $pieces[0];
    
              //SKIP custom CSS that are HTML tag
//               if ((in_array(strtoupper($style_elem), $html))&&(strcmp($style_class_name, $style_elem) == 0))
//                 continue;

              $styles .= '  <Style name="'.$style_class_name.'" element="'.$style_elem.'"';
              if (strcmp($style_class_name, $style_elem) != 0)
                $styles .= '>'."\n".'   <Attribute name="class" value="'.$style_class_name.'" />'."\n".'  </Style>'."\n";
            	else
                $styles .= '/>'."\n";
           }
          }
        
        $styles .= '</Styles>'."\n";
        //+++++++++++++++++++++++++++++++++++++++++++++++
        // END Construction of "fckstyles.xml" for FCKeditor
        //+++++++++++++++++++++++++++++++++++++++++++++++
        

        //Write the NEW  "fckstyles.xml" for FCKeditor
        if($fp = fopen($this->cms->config["root_path"]."/tmp/cache/fckstyles.xml", "w")){    
            if (!fwrite($fp, $styles)) {
                echo "Non si riesce a scrivere nel file ($filename)";
                exit;
            }
            fclose($fp);
        }    
    }// END if (strcmp($stylesheet, "") != 0){
    //+++++++++++++++++++++++++++++++++++
    // END PREPARATION custom style TO DISPLAY
    //+++++++++++++++++++++++++++++++++++
        
		$oFCKeditor = new FCKeditor($name) ;
		$oFCKeditor->BasePath = $this->cms->config["root_url"].'/modules/FCKeditorX/FCKeditor/';
		
		if (intval($rows) == 0)
		  $rows = 13;
		
		$oFCKeditor->Height = $rows*33+20*3;
		$oFCKeditor->Value = $content;
		
    //GET FCKeditor CMSMS configuration
    $skin = $this->GetPreference('FCKConfig.SkinSelected');
		$lang = $this->GetPreference('FCKConfig.CMSMSLanguage');
		$xhtml = $this->GetPreference("FCKConfig.EnableXHTML");
		$context = $this->GetPreference("FCKConfig.ContextMenu");
		
		if ($lang)
		  $lang = substr($this->cms->userprefs["default_cms_language"], 0, 2);
		else
		  $lang = NULL;
    
    //LOAD CMSMS config of FCKeditor
    $oFCKeditor->Config['CustomConfigurationsPath'] = '' ;
    $oFCKeditor->Config['SkinPath'] = $oFCKeditor->BasePath."editor/skins/$skin/";
    if (is_null($lang))
      $oFCKeditor->Config['AutoDetectLanguage'] = 'true';
    else
      $oFCKeditor->Config['DefaultLanguage'] = $lang;
      
    if ($context == 0)
      $oFCKeditor->Config['ContextMenu'] = "['']";
    
    if ($xhtml == 1){
      $oFCKeditor->Config['EnableXHTML'] = 'true';
      $oFCKeditor->Config['EnableSourceXHTML'] = 'true';
    }
    
    $oFCKeditor->Create();
	}
	
	function WYSIWYGGenerateBody()
	{

	}
	
	function WYSIWYGGenerateHeader()
	{
	}

	function GetHelp($lang='en_US')
	{
		return "
		<h3>What does this do?</h3>
    <p>Enables <b>FCKeditor</b> to be used as a WYSIWYG.</p>
    <h3>How do I use it?</h3>
    <p>Install it, then go to User Preferences and Set <b>FCKeditor</b> to be your wysiwyg of choice.</p>
		";
	}
	
	function DoAction($action, $id, $params, $returnid = -1)
	{
	 
		if (strcmp("defaultadmin",$action)==0){
    
    //START on UPDATING custom CONFIG file for FCK
    
      //GET VALUE SELECTED FROM USER
			if (isset($_REQUEST[$id.'submit']))	{
				if (isset($_REQUEST[$id.'skin']))	{
					$new_skin = $_REQUEST[$id.'skin'];
            $this->SetPreference('FCKConfig.SkinSelected', $new_skin);
				}
				
				if (isset($_REQUEST[$id.'lang']))	
            $this->SetPreference('FCKConfig.CMSMSLanguage', 1);
				else
            $this->SetPreference('FCKConfig.CMSMSLanguage', 0);
				
				if (isset($_REQUEST[$id.'xhtml']))	
            $this->SetPreference('FCKConfig.EnableXHTML', 1);
				else
            $this->SetPreference('FCKConfig.EnableXHTML', 0);
				
				if (isset($_REQUEST[$id.'context']))
            $this->SetPreference('FCKConfig.ContextMenu', 1);
				else
            $this->SetPreference('FCKConfig.ContextMenu', 0);
					
			}
			
			$skin = $this->GetPreference("FCKConfig.SkinSelected");
			$lang = $this->GetPreference("FCKConfig.CMSMSLanguage");
			$xhtml = $this->GetPreference("FCKConfig.EnableXHTML");
			$context = $this->GetPreference("FCKConfig.ContextMenu");

			$skins_available = Array();
      
			$d = dir(dirname(__FILE__).'/FCKeditor/editor/skins');

			$i = 0;
			//FIND ALL SKIN AVAILABLE
			while ($entry = $d->read()) {
				if (!((strcmp($entry,".")==0)||(strcmp($entry,"..")==0))) {
			    		$skins_available[$i] = trim($entry);
			    		$i++;
			    	}
			}
			$d->close();
			$num_skins = $i;

			echo $this->CreateFormStart($id, $moduleaction);

			?>

			<div class="adminform">
			
<table style="border: 1px solid #F9F2DE;">
<tr>
<th style="background-color:#F9F2DE;" colspan="3" align="right">
<img src="<?php echo $this->cms->config["root_url"]; ?>/modules/FCKeditorX/FCKeditor/logotop.gif" width="238" height="41">
</th>
</tr>
<tr>
<td>Select skin</td>
<td>:</td>
<td><select name="<?php echo $id?>skin" style="width:100%;">
<?
for($i=0;$i<$num_skins;$i++) {
  echo '<option value="';
  echo $skins_available[$i];
  echo '" ';
  if ((strcmp($skins_available[$i],$skin ))==0)
      echo ' selected ';
  echo '>';
  echo $skins_available[$i];
  echo '</option>'."\n";
}
?>
</select></td>
</tr>
<!--
<tr>
<td>Select Toolbar </td>
<td>:</td>
<td><select name="select" style="width:100%;">
</select></td>
</tr>
-->
<tr>
<td>Use CMS Language settings</td>
<td>:</td>
<td align="center">
<input type="checkbox" name="<?php echo $id?>lang" 
<?php
if ($lang)
  echo ' "checked="checked" ';
?>
value="checkbox"></td>
</tr>
<tr>
<td>Enable XHTML Formatting</td>
<td>:</td>
<td align="center">
<input type="checkbox" name="<?php echo $id?>xhtml" 
<?php
if ($xhtml)
  echo ' "checked="checked" ';
?>
value="checkbox"></td>
</tr>
<tr>
<td>Enable context menu </td>
<td>:</td>
<td align="center">
<input type="checkbox" name="<?php echo $id?>context" 
<?php
if ($context)
  echo ' "checked="checked" ';
?>
value="checkbox"></td>
</tr>
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td align="right">
<input type="submit" name="<?php echo $id?>submit" value="Submit" />
</td>
</tr>
</table>
			</div>

			<?php

			echo $this->CreateFormEnd();


		}
	}

	function GetAuthor()
	{
		return 'Roberto Tagliento';
	}

	function GetAuthorEmail()
	{
		return '&lt;megabob3@yahoo.it&gt;';
	}
	
	function GetChangeLog()
	{
		?>
		<ul>
		<li>
		<p>Version: 0.7</p>
		<p>FIXED bugs and improved configuration functionalities.</p>
		<p>Version for CMSMS 0.10.SVN .</p>
		</li>
		<li>
		<p>Version: 0.6</p>
		<p>FIXED destination of uploads.</p>
		<p>Version for CMSMS 0.10.SVN .</p>
		<p>ADDED plugin for INTERNAL link of CMSMS (THANKS!!! to <b>Woudloper</b> for the plugin).</p>
		<p>ADDED binding on custom CSS of CMSMS (THANKS to <b>infusion</b> for some incipit information).</p>
		<p>UPDATED to FCKeditor 2.0 </p>
		</li>
		<li>
		<p>Version: 0.5</p>
		<p>Beta version for CMSMS 0.10.4 .</p>
		</li>
		<li>
		<p>Version: 0.4</p>
		<p>Original module code for FCKeditor 2 WYSIWYG textarea replacement tool.</p>
		</li>
		<li>
		<p>Author: Roberto Tagliento  &lt;megabob3@yahoo.it&gt;</p>
		<p>Converted for use with new CMS Module architecture.</p>
		</li>
		</ul>
		<?php
	}
}

# vim:ts=4 sw=4 noet
?>
