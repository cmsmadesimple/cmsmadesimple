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

class HTMLArea extends CMSModule
{
	function GetName()
	{
		return 'HTMLArea';
	}

	function IsPluginModule()
	{
		return true;
	}

	function HasAdmin()
	{
		return false;
	}

	function GetVersion()
	{
		return '1.1';
	}
	
	function IsWYSIWYG() {
		return true;
	}

	function WYSIWYGTextarea($name='textarea',$columns='80',$rows='15',$encoding='',$content='',$stylesheet='')
	{
		global $gCms;
		$rows = $rows + 10;
		if ($stylesheet != '')
		{
			$gCms->variables['htmlarea_stylesheet'] = $stylesheet;
		}
		if (!array_key_exists('htmlarea_textareas', $gCms->variables))
		{
			$gCms->variables['htmlarea_textareas'] = array();
		}
		array_push($gCms->variables['htmlarea_textareas'], $name);
		return '<textarea id="'.$name.'" name="'.$name.'" columns="'.$columns.'" rows="'.$rows.'">'.cms_htmlentities($content,ENT_NOQUOTES,get_encoding($encoding)).'</textarea>';
	}
	
	function WYSIWYGGenerateBody()
	{
		global $gCms;
		if (array_key_exists('htmlarea_textareas', $gCms->variables))
		{
			echo "onload='page_load();'";
		}
	}
	
	function WYSIWYGGenerateHeader()
	{
		global $gCms;
	
		if (array_key_exists('htmlarea_textareas', $gCms->variables))
		{
	
		?>
		<script type="text/javascript">
			<!--
			_editor_url = "<?php echo $gCms->config["root_url"]?>/modules/HTMLArea/htmlarea/";
			<?php echo "_editor_lang = \"".$gCms->nls['htmlarea'][$gCms->current_language]."\";\n" ?>
			-->
		</script>
	
		<script type="text/javascript" src="<?php echo $gCms->config["root_url"]?>/modules/HTMLArea/htmlarea/htmlarea.js"></script>
	
		<script type="text/javascript">
			<!--
	
			HTMLArea.loadPlugin("ImageManager");
			HTMLArea.loadPlugin("InsertFile");
			HTMLArea.loadPlugin("TableOperations");
			HTMLArea.loadPlugin("ContextMenu");
			HTMLArea.loadPlugin("CharacterMap");
			HTMLArea.loadPlugin("FindReplace");
			HTMLArea.loadPlugin("InvertBackground");
			<?php if ($gCms->config["use_Indite"] == true) { ?>	
				//HTMLArea.loadPlugin("Indite");	
			<?php } ?>
	
			function initHtmlArea()
			{
				var config = new HTMLArea.Config();
				<?php
					$count = 1;
					foreach ($gCms->variables['htmlarea_textareas'] as $onearea)
					{
				?>
	
				var editor<?php echo $count?> = new HTMLArea("<?php echo $onearea?>", config);
				//editor<?php echo $count?>.registerPlugin(ImageManager);
				<?php 
					// Ugly Hack alert! making setting session var to send language setting to insertFile
					if ($gCms->config["disable_htmlarea_translation"] != true)
					{
						$_SESSION['InsertFileLang'] = $gCms->nls['htmlarea'][$gCms->current_language];
					}
					else
					{
						if (isset($_SESSION['InsertFileLang']))
						{
							unset($_SESSION['InsertFileLang']);
						}
					}
			 	?>
	
				editor<?php echo $count?>.registerPlugin(InsertFile);
				editor<?php echo $count?>.registerPlugin(TableOperations);
				editor<?php echo $count?>.registerPlugin(ContextMenu);
				editor<?php echo $count?>.registerPlugin(CharacterMap);
				editor<?php echo $count?>.registerPlugin(FindReplace);
				editor<?php echo $count?>.registerPlugin(InvertBackground);
	
				<?php
					if ($gCms->config["use_Indite"] == true)
						echo "editor".$count.".registerPlugin(Indite);";
	
					$template_id = -1;
					if (isset($_POST["template_id"])) 
					{
						$template_id = $_POST["template_id"];
					}
					else if (isset($_GET['page_id']))
					{
						$query = "SELECT template_id FROM ".cms_db_prefix()."pages WHERE ".$_GET['page_id']." = page_id";
						$template_id = $db->GetOne($query);
					}
	
					if (array_key_exists('htmlarea_stylesheet', $gCms->variables))
					{
						$obj = get_stylesheet(str_replace('../stylesheet.php?templateid=', '', $gCms->variables['htmlarea_stylesheet']));
				?>	
	
				editor<?php echo $count?>.config.pageStyle = editor<?php echo $count?>.config.pageStyle + "<?php echo preg_replace('/\r?\n/', '', str_replace('"', '\\"', str_replace("'", "\\'", $obj['stylesheet'])))?>";
				<?php
					}
				?>
	
				editor<?php echo $count?>.config.baseURL = "<?php echo $gCms->config['root_url']?>/";
				editor<?php echo $count?>.generate();
				<?php
						$count++;
					}
				?>
	
			}
			-->
		</script>
	
		<script type="text/javascript" language="Javascript">
			<!--
			function page_load()
			{
				initHtmlArea();
	
				for (var j=0; j < document.forms.length; j++)
				{
					for (var i=0; i < (document.forms[j].elements).length; i++)
					{
						if (document.forms[j].elements[i].id == "plain")
						{
							//document.forms[j].elements[i].style.display='none';
						}
					}
				}
			}
			-->
		</script>
	
		<?php
		}
	}

	function GetHelp($lang='en_US')
	{
		return "
		<h3>What does this do?</h3>
    <p>Enables HTMLArea to be used as a WYSIWYG.</p>
    <h3>How do I use it?</h3>
    <p>Install it, then go to User Preferences and Set HTMLArea  to be your wysiwyg of choice.</p>
		";
	}

	function GetAuthor()
	{
		return 'Simon Brown';
	}

	function GetAuthorEmail()
	{
		return '&lt;simon@uptoeleven.ws&gt;';
	}
	
	function GetChangeLog()
	{
		?>
		<ul>
		<li>
		<p>Version: 1.0</p>
		<p>Original module code for HTMLArea WYSIWYG textarea replacement tool.</p>
		</li>
		<li>
		<p>Author: Simon Brown &lt;simon@uptoeleven.ws&gt;</p>
		<p>Version: 1.1</p>
		<p>Converted for use with new CMS Module architecture.</p>
		</li>
		</ul>
		<?php
	}
}

# vim:ts=4 sw=4 noet
?>
