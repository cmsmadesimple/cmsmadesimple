<?php
# This is a string replacement module (c) 2005 by INTESOL SRL (Baduca Alexandru)
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

class ProtectEmail extends CMSModule
{
function GetName()
{
return 'ProtectEmail';
}

function GetVersion()
{
return '0.1';
}

function GetHelp($lang = 'en_US')
{

return '
<h3>What does this do?</h3>
<p>This module parses the content of the page and encypts any e-mail adress on the page if the adress isn\'t  link it will be converted to one.</p>
<p>This module uses a <a href="smarty.php.net">Smarty</a> function.</p>
';
}

function GetDescription($lang = 'en_US')
{
return '';
}

function GetChangeLog()
{
return '<p>0.0.1 First version</p>';
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
$this->Audit( 0, 'Module_ProtectEmail', 'Install V'.$this->getVersion());
}

function Upgrade($oldversion, $newversion) {

$current_version = $oldversion;
$ret = 0;

switch($current_version)
{
default:break;
}
if ($current_version == $this->GetVersion()) {
$this->Audit( 0, 'Module_ProtectEmail', 'Update V'.$oldversion.' &rarr; V'.$newversion);
return FALSE;
}
else return 'Module_ProtectEmail upgrade failed!';
}


function Uninstall()
{
$this->Audit( 0, 'Module_ProtectEmail', 'Uninstall V'.$this->getVersion());
}

function ContentPostRender(&$content)
{
$content = $this->hideEmail($content,'/modules/ProtectEmail');
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
*  Used for encrypting the e-mail addres. This function uses a Smarty function
*  that does the encryption. The encryption is set to hex. This function only
*  parses the content of the page in order to extract the data needed for the
*  encryption function.
*/
function hideEmail($content)
{
  require "function.mailto.php";
  $exp = "!<a([^>]+)href=[\'\"]?mailto:([^\'\"\>\s]+)[\'\"]?([^>]*)>(.*?)</a>!si";
  do
  {
    if(isset($result))
      unset($result);
    preg_match($exp, $content, $result);
    if($result[1])
    {
      $params = Array();
      $params['extra'] = $result[1].' '.$result[3];
      $params['address'] = $result[2];
      $params['text'] = $result[4];
      $params['encode'] = 'hex';
      $a = '';
      $newstring = smarty_function_mailto($params, $a);
      $content = preg_replace ('!'.$result[0].'!', $newstring, $content);
    } else
    {
      unset($result);
    }
  } while(isset($result));
  
  $exp = "!([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)!";
  do
  {
    if(isset($result))
      unset($result);
    preg_match($exp, $content, $result);
    if($result[0])
    {
      $params = Array();
      $params['address'] = $result[0];
      $params['text'] = $result[0];
      $params['encode'] = 'hex';
      $a = '';
      $newstring = smarty_function_mailto($params, $a);
      $content = preg_replace ('!'.$result[0].'!', $newstring, $content);
    } else
    {
      unset($result);
    }
  } while(isset($result));
  return $content;
}
}
?>