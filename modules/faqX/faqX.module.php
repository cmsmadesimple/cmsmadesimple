<?php
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

require_once("faq/modulefunctions.php");
	 
class faqX extends CMSModule
{
	function GetName()
	{
		return 'faqX';
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
		return '0.4';
	}

	function Install()
	{
    $this->CreatePermission('Faq Admin', 'Faq Admin');
    Faq_module_install($this->cms);
	}


	function Upgrade($oldversion, $newversion)
	{

	}

	function Uninstall()
	{
    $this->RemovePermission('Faq Admin');
    Faq_module_uninstall($this->cms);
	}


	function DoAction($action, $id, $params, $returnid = -1)
	{
    
	 
	 switch ($action) {
			case "default":
			 Faq_module_execute($this, $id, $params, $returnid);
      break;
      default:
        echo '<div class="adminform">';
        $access = $this->CheckPermission('Faq Admin');
        if (!$access)
        {
          echo "<p class=\"error\">You need the 'Faq Admin' ";
          echo "permission to perform that function.</p>";
          return;
        }
                
                
          Faq_module_executeadmin($this,$id, $returnid);
          
        
        echo '</div>';
			break;

		}

	}

	function GetHelp($lang='en_US')
	{
		return '
		<h3>What does this do?</h3>
	<p>With this module you can manage the Faq </p>
	<h3>What parameters are there?</h3>
	<p>.. work in progress ....</p>
	<p>{cms_module module="faqX"}</p>
	<ul>
		<li><em>(optional)</em> XXX="Z" - Blabla</li>
		<li><em>(optional)</em> YYYY - Bla bla</li>
	</ul>
		';
	}

	function GetAuthor()
	{
		return 'Roberto Tagliento';
	}

	function GetAuthorEmail()
	{
		return 'megabob3@yahoo.it';
	}

	function GetChangeLog()
	{
		?>
	<p>
 This class is based on the FAQmanager perl script by Richard Lawrence,<br>
 which can be found at: http://www.fourteenminutes.com/code/faqmanager/<br>
 It looks like his with permission ;)<br>
 http://php.amnuts.com/
	</p>
		<?php
	}
}

# vim:ts=4 sw=4 noet
?>
