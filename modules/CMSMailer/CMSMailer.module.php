<?php
#-------------------------------------------------------------------------
# Module: CMSMailer - a simple wrapper around cms_mailer class and PHPMailer
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# Visit our homepage at: http://www.cmsmadesimple.org
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

class CMSMailer extends CMSModule
{
  protected $the_mailer;

  public function __construct()
  {
    parent::__construct();
    $this->the_mailer = new cms_mailer(FALSE);
  }

  #[\ReturnTypeWillChange]
  public function __call($method,$args)
  {
    if( method_exists($this->the_mailer,$method) ) {
      return call_user_func_array(array($this->the_mailer,$method),$args);
    }
    if( is_callable('parent::__call') ) {
      return parent::__call($method,$args);
    }
    throw new CmsException('Call to invalid method '.$method.' on '.get_class($this->the_mailer).' object');
  }

  function GetName() { return 'CMSMailer'; }
  function GetFriendlyName() { return $this->Lang('friendlyname'); }
  function GetVersion() { return '6.2.15'; }
  function MinimumCMSVersion() { return '1.99-alpha0'; }
  function GetHelp() { return $this->Lang('help'); }
  function GetAuthor() { return 'Calguy1000'; }
  function GetAuthorEmail() { return ''; }
  function GetChangeLog() { return file_get_contents(__DIR__.'/changelog.inc'); }
  function IsPluginModule() { return FALSE; }
  function HasAdmin() { return FALSE; }
  function GetAdminSection() { return 'extensions'; }
  function GetAdminDescription() { return $this->Lang('moddescription'); }
  function VisibleToAdminUser() { return FALSE; }
  function InstallPostMessage() { return $this->Lang('postinstall'); }
  function LazyLoadFrontend() { return TRUE; }
  function LazyLoadAdmin() { return TRUE; }
  function UninstallPostMessage() { return $this->Lang('postuninstall'); }

  //// API SECTION - cms_mailer CLASS METHODS ACCESSIBLE VIA AN ALTERNATE NAME
  // these were deprecated in May 2013, when the cms_mailer class was released
  // instead use the methods of that class directly

  public function GetHost()
  {
    return $this->the_mailer->GetSMTPHost();
  }

  public function SetHost($txt)
  {
    $this->the_mailer->SetSMTPHost($txt);
  }

  public function GetPort()
  {
    return $this->the_mailer->GetSMTPPort();
  }

  public function SetPort($txt)
  {
    $this->the_mailer->SetSMTPPort($txt);
  }

  public function GetTimeout()
  {
    return $this->the_mailer->GetSMTPTimeout();
  }

  public function SetTimeout($txt)
  {
    $this->the_mailer->SetSMTPTimeout($txt);
  }

  public function GetUsername()
  {
    return $this->the_mailer->GetSMTPUsername();
  }

  public function SetUsername($txt)
  {
    $this->the_mailer->SetSMTPUsername($txt);
  }

  public function GetPassword()
  {
    return $this->the_mailer->GetSMTPPassword();
  }

  public function SetPassword($txt)
  {
    $this->the_mailer->SetSMTPPassword($txt);
  }

  public function GetSecure()
  {
    return $this->the_mailer->GetSMTPSecure();
  }

  public function SetSecure($txt)
  {
    $this->the_mailer->SetSMTPSecure($txt);
  }
} // end of class

?>
