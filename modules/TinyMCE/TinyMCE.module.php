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

class TinyMCE extends CMSModule
{
	function GetName()
	{
		return 'TinyMCE';
	}

	function HasAdmin()
	{
		return true;
	}

	function GetVersion()
	{
		return '1.1';
	}
	
	function IsWYSIWYG() {
		return true;
	}

	/**
	 * This is NOT overriding a CMSModule function.  It's just used internallly for displaying the form, since it's used in so many places.
	 */
	function AdminForm($id, $moduleaction, $error='')
	{
		if ( $error != "" )
		{
		  echo "<ul class=\"error\">$error</ul>";
		}
		
		$striptags = 'false';
		if (isset($_REQUEST[$id.'submit'])) // may have to do isset($params["submit"])
		{
			if (isset($_REQUEST[$id.'striptags']))
			{
				$striptags = $_REQUEST[$id.'striptags'];
				$this->SetPreference('striptags', $striptags);
			}
		}
		else
		{
			$striptags = $this->GetPreference('striptags', 'false');
		}

		?>

		<div class="adminform">
		 
		<?php echo $this->CreateFormStart($id, $moduleaction)?>
		
		<table border="0">
			<tr>
				<td>Strip background tags from CSS:</td>
				<td>
					<?php $values = array();
					$values = array('true'=>'true', 'false'=>'false');
					echo $this->CreateInputDropdown($id, 'striptags', $values, -1, $striptags, '');
					?></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><?php echo $this->CreateInputSubmit($id, 'submit', 'Submit') ?></td>
			</tr>
		</table>

		<?php echo $this->CreateFormEnd()?>
		 
		</div>
		<?php
	}

	function WYSIWYGTextarea($name='textarea',$columns='80',$rows='15',$encoding='',$content='',$stylesheet='')
	{
		global $gCms;
		if ($stylesheet != '')
		{
			$gCms->variables['tinymce_stylesheet'] = $stylesheet;
		}
		if (!array_key_exists('tinymce_textareas', $gCms->variables))
		{
			$gCms->variables['tinymce_textareas'] = array();
		}
		array_push($gCms->variables['tinymce_textareas'], $name);
		return '<textarea id="'.$name.'" name="'.$name.'" cols="'.$columns.'" rows="'.($rows+5).'">'.cms_htmlentities($content,ENT_NOQUOTES,get_encoding($encoding)).'</textarea>';
	}
	
	function WYSIWYGPageFormSubmit()
	{
		return 'tinyMCE.triggerSave();';
	}
	
	function WYSIWYGGenerateHeader()

	{
		global $gCms;
		if (array_key_exists('tinymce_textareas', $gCms->variables))
		{
		?>
	
		<script language="javascript" type="text/javascript" src="<?php echo $gCms->config['root_url'] ?>/modules/TinyMCE/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
		<script language="javascript" type="text/javascript">
			tinyMCE.init({
				mode : "exact",
				elements : "<?php
					$elements = '';
					foreach ($gCms->variables['tinymce_textareas'] as $oneelement)
					{
						$elements .= $oneelement . ', ';
					}
					if (strlen($elements))
					{
						$elements = substr($elements, 0, strlen($elements) -2);
					}
					echo $elements;
				?>",
				<?php
				if (isset($gCms->variables['tinymce_stylesheet']))
				{
					$css = $gCms->variables['tinymce_stylesheet'];
	
					$striptags = $this->GetPreference('striptags', 'false');
	
					if ($striptags == 'true')
					{
						$css .= '&stripbackground=true';
					}
	
					echo 'content_css : "' . $css . "\",\n";
				}
				?>
				theme : "advanced",
				theme_advanced_toolbar_location : "top",
				verify_html : "false",
				plugins : "table,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,zoom,flash,ibrowser",
				theme_advanced_buttons1_add : "fontselect,fontsizeselect,forecolor",
				theme_advanced_buttons2_add_before: "cut,copy,paste,separator",
				theme_advanced_buttons2_add : "separator,insertdate,inserttime,preview,zoom",
				theme_advanced_buttons3_add : "tablecontrols,emotions,iespell,flash,advhr,ibrowser",
				document_base_url : "<?php echo $gCms->config['root_url']?>/",
				relative_urls : "false",
				plugin_insertdate_dateFormat : "%Y-%m-%d",
				plugin_insertdate_timeFormat : "%H:%M:%S",
				valid_elements : "*[*]"
			});
		</script>
	
		<?php
		}
	}

	function DoAction($action, $id, $params, $resultid = -1)
	{
		switch ($action) {
			case "defaultadmin":
				$this->AdminForm($id, $action, $error='');
				break;
	
			case "default":
				echo "help do something: ".$action;
				echo "<br>";
				break;
		}
	}

	function GetHelp($lang='en_US')
	{
		return "
		<h3>What does this do?</h3>
    <p>Enables TinyMCE to be used as a WYSIWYG.</p>
    <h3>How do I use it?</h3>
    <p>Install it, then go to User Preferences and Set TinyMCE  to be your wysiwyg of choice.</p>
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
		<p>Original module code for TinyMCE WYSIWYG textarea replacement tool.</p>
		</li>
		<li>
		<p>Author: Simon Brown &lt;simon@uptoeleven.ws&gt;</p>
		<p>Version: 1.1</p>
		<p>Converted for use with new CMS Module architecture.</p>
		<p>Upgraded TinyMCE to version 1.42.</p>
		</li>
		</ul>
		<?php
	}
}

# vim:ts=4 sw=4 noet
?>
