<?php
#-------------------------------------------------------------------------
# Module: CMSMailer - a simple wrapper around phpmailer
# Version: 1.73.10, Robert Campbell <rob@techcom.dyndns.org>
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
if( !isset($gCms) ) exit;

class CMSMailer extends CMSModule
{
  private $the_mailer = null;

  public function __construct()
  {
    $this->the_mailer = new cms_mailer();
  }

  function GetName() { return 'CMSMailer'; }
  function GetFriendlyName() { return $this->Lang('friendlyname'); }
  function GetVersion() { return '5.2.4'; }
  function MinimumCMSVersion() { return '1.11.5'; }
  function GetHelp() { return $this->Lang('help'); }
  function GetAuthor() { return 'Calguy1000'; }
  function GetAuthorEmail() { return 'calguy1000@hotmail.com'; }
  function GetChangeLog() { return __DIR__.'/changelog.inc'; }
  function IsPluginModule() { return FALSE; }
  function HasAdmin() { return FALSE; }
  function GetAdminSection() { return 'extensions'; }
  function GetAdminDescription() { return $this->Lang('moddescription'); }
  function VisibleToAdminUser() { return FALSE; }
  function InstallPostMessage() { return $this->Lang('postinstall'); }
  function LazyLoadFrontend() { return TRUE; }
  function LazyLoadAdmin() { return TRUE; }
  function UninstallPostMessage() { return $this->Lang('postuninstall'); }

  //////////////////////////////////////////////////////////////////////
  //// BEGIN API SECTION
  //////////////////////////////////////////////////////////////////////

  public function __call($method,$args)
  {
    if( method_exists($this->the_mailer,$method) ) {
      return call_user_func_array(array($this->the_mailer,$method),$args);
    }
    // todo, throw exception here.
  }
} // class CMSMailer

?>