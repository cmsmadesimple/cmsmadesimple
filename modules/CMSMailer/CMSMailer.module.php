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


#-------------------------------------------------------------------------
/* Your initial Class declaration. This file's name must
   be "[class's name].module.php", or, in this case,
   Skeleton.module.php
*/ 
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


  ////////////////////////////////////////////////////////////
  //                     API FUNCTIONS                      //
  ////////////////////////////////////////////////////////////

  /* use call instead....
  function GetAltBody()
  {
    $this->_load();
    return $this->the_mailer->AltBody;
  }

  function SetAltBody( $txt )
  {
    $this->_load();
    $this->the_mailer->AltBody = $txt;
  }

  function GetBody()
  {
    $this->_load();
    return $this->the_mailer->Body;
  }

  function SetBody( $txt )
  {
    $this->_load();
    $this->the_mailer->Body = $txt;
  }

  function GetCharSet()
  {
    $this->_load();
    return $this->the_mailer->CharSet;
  }

  function SetCharSet( $txt )
  {
    $this->_load();
    $this->the_mailer->CharSet = $txt;
  }

  function GetConfirmReadingTo()
  {
    $this->_load();
    return $this->the_mailer->ConfirmReadingTo;
  }

  function SetConfirmReadingTo( $txt )
  {
    $this->_load();
    $this->the_mailer->ConfirmReadingTo = $txt;
  }

  function GetContentType()
  {
    $this->_load();
    return $this->the_mailer->ContentType;
  }

  function SetContentType( $txt )
  {
    $this->_load();
    $this->the_mailer->ContentType = $txt;
  }

  function GetEncoding()
  {
    $this->_load();
    return $this->the_mailer->Encoding;
  }

  function SetEncoding( $txt )
  {
    $this->_load();
    $this->the_mailer->Encoding = $txt;
  }

  function GetErrorInfo()
  {
    $this->_load();
    return $this->the_mailer->ErrorInfo;
  }

  function GetFrom()
  {
    $this->_load();
    return $this->the_mailer->From;
  }

  function SetFrom( $txt )
  {
    $this->_load();
    $this->the_mailer->From = $txt;
  }

  function GetFromName()
  {
    $this->_load();
    return $this->the_mailer->FromName;
  }

  function SetFromName( $txt )
  {
    $this->_load();
    $this->the_mailer->FromName = $txt;
  }

  function GetHelo()
  {
    $this->_load();
    return $this->the_mailer->Helo;
  }

  function SetHelo( $txt )
  {
    $this->_load();
    $this->the_mailer->Helo = $txt;
  }

  function GetHost()
  {
    $this->_load();
    return $this->the_mailer->Host;
  }

  function SetHost( $txt )
  {
    $this->_load();
    $this->the_mailer->Host = $txt;
  }

  function GetHostname()
  {
    $this->_load();
    return $this->the_mailer->Hostname;
  }

  function SetHostname( $txt )
  {
    $this->_load();
    $this->the_mailer->Hostname = $txt;
  }

  function GetMailer()
  {
    $this->_load();
    return $this->the_mailer->Mailer;
  }

  function SetMailer( $txt )
  {
    $this->_load();
    $this->the_mailer->Mailer = $txt;
  }

  function GetPassword()
  {
    $this->_load();
    return $this->the_mailer->Password;
  }

  function SetPassword( $txt )
  {
    $this->_load();
    $this->the_mailer->Password = $txt;
  }

  function GetPort()
  {
    $this->_load();
    return $this->the_mailer->Port;
  }

  function SetPort( $txt )
  {
    $this->_load();
    $this->the_mailer->Port = $txt;
  }

  function GetPriority()
  {
    $this->_load();
    return $this->the_mailer->Priority;
  }

  function SetPriority( $txt )
  {
    $this->_load();
    $this->the_mailer->Priority = $txt;
  }

  function GetSender()
  {
    $this->_load();
    return $this->the_mailer->Sender;
  }

  function SetSender( $txt )
  {
    $this->_load();
    $this->the_mailer->Sender = $txt;
  }

  function GetSendmail()
  {
    $this->_load();
    return $this->the_mailer->Sendmail;
  }

  function SetSendmail( $txt )
  {
    $this->_load();
    $this->the_mailer->Sendmail = $txt;
  }

  function GetSMTPAuth()
  {
    $this->_load();
    return $this->the_mailer->SMTPAuth;
  }

  function SetSMTPAuth( $txt )
  {
    $this->_load();
    $this->the_mailer->SMTPAuth = $txt;
  }

  function GetSMTPDebug()
  {
    $this->_load();
    return $this->the_mailer->SMTPDebug;
  }

  function SetSMTPDebug( $txt )
  {
    $this->_load();
    $this->the_mailer->SMTPDebug = $txt;
  }

  function GetSMTPKeepAlive()
  {
    $this->_load();
    return $this->the_mailer->SMTPKeepAlive;
  }

  function SetSMTPKeepAlive( $txt )
  {
    $this->_load();
    $this->the_mailer->SMTPKeepAlive = $txt;
  }

  function GetSubject()
  {
    $this->_load();
    return $this->the_mailer->Subject;
  }

  function SetSubject( $txt )
  {
    $this->_load();
    $this->the_mailer->Subject = $txt;
  }

  function GetTimeout()
  {
    $this->_load();
    return $this->the_mailer->Timeout;
  }

  function SetTimeout( $txt )
  {
    $this->_load();
    $this->the_mailer->Timeout = $txt;
  }

  function GetUsername()
  {
    $this->_load();
    return $this->the_mailer->Username;
  }

  function SetUsername( $txt )
  {
    $this->_load();
    $this->the_mailer->Username = $txt;
  }

  function GetWordWrap()
  {
    $this->_load();
    return $this->the_mailer->WordWrap;
  }

  function SetWordWrap( $txt )
  {
    $this->_load();
    $this->the_mailer->WordWrap = $txt;
  }

  function AddAddress( $address, $name = '' )
  {
    $this->_load();
    return $this->the_mailer->AddAddress( $address, $name );
  }

  function AddAttachment( $path, $name = '', $encoding = 'base64', $type = 'application/octet-stream' )
  {
    $this->_load();
    return $this->the_mailer->AddAttachment( $path, $name, $encoding, $type );
  }

  function AddBCC( $addr, $name = '' )
  {
    $this->_load();
    $this->the_mailer->AddBCC( $addr, $name );
  }

  function AddCC( $addr, $name = '' )
  {
    $this->_load();
    $this->the_mailer->AddCC( $addr, $name );
  }

  function AddCustomHeader( $txt )
  {
    $this->_load();
    $this->the_mailer->AddCustomHeader( $txt );
  }

  function AddEmbeddedImage( $path, $cid, $name = '', $encoding = 'base64', $type = 'application/octet-stream' )
  {
    $this->_load();
    return $this->the_mailer->AddEmbeddedImage( $path, $cid, $name, $encoding, $type );
  }

  function AddReplyTo( $addr, $name = '' )
  {
    $this->_load();
    $this->the_mailer->AddReplyTo( $addr, $name );
  }


  function AddStringAttachment( $string, $filename, $encoding = 'base64', $type = 'application/octet-stream' )
  {
    $this->_load();
    $this->the_mailer->AddStringAttachment( $string, $filename, $encoding, $type );
  }

  function ClearAddresses()
  {
    $this->_load();
    $this->the_mailer->ClearAddresses();
  }

  function ClearAllRecipients()
  {
    $this->_load();
    $this->the_mailer->ClearAllRecipients();
  }

  function ClearAttachments()
  {
    $this->_load();
    $this->the_mailer->ClearAttachments();
  }

  function ClearBCCs()
  {
    $this->_load();
    $this->the_mailer->ClearBCCs();
  }

  function ClearCCs()
  {
    $this->_load();
    $this->the_mailer->ClearCCs();
  }

  function ClearCustomHeaders()
  {
    $this->_load();
    $this->the_mailer->ClearCustomHeaders();
  }

  function ClearReplyTos()
  {
    $this->_load();
    $this->the_mailer->ClearReplyTos();
  }
  
  function IsError()
  {
    $this->_load();
    return $this->the_mailer->IsError();
  }

  function IsHTML($var = true)
  {
    $this->_load();
    return $this->the_mailer->IsHTML($var);
  }

  function IsMail()
  {
    $this->_load();
    return $this->the_mailer->IsMail();
  }

  function IsQmail()
  {
    $this->_load();
    return $this->the_mailer->IsQmail();
  }

  function IsSendmail()
  {
    $this->_load();
    return $this->the_mailer->IsSendmail();
  }

  function IsSMTP()
  {
    $this->_load();
    return $this->the_mailer->IsSMTP();
  }

  function Send()
  {
    $this->_load();
    return $this->the_mailer->Send();
  }

  function SetLanguage( $lang_type, $lang_path = '' )
  {
    $this->_load();
    return $this->the_mailer->SetLanguage( $lang_type, $lang_path );
  }

  function SmtpClose()
  {
    $this->_load();
    return $this->the_mailer->SmtpClose();
  }

  function GetSecure()
  {
    $this->_load();
    return $this->the_mailer->SMTPSecure;
  }

  function SetSecure($value)
  {
    $value = strtolower($value);
    if( $value == '' || $value == 'ssl' || $value == 'tls' ) {
      $this->_load();
      $this->the_mailer->SMTPSecure = $value;
    }
  }

  */
} // class CMSMailer

?>