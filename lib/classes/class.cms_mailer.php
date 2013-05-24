<?php
#-------------------------------------------------------------------------
# Module: CMSMailer - a simple wrapper around phpmailer
# copyright (c) Robert Campbell <rob@techcom.dyndns.org>
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

/**
 * @package CMS
 */

/**
 * A class for sending email.
 *
 * @package CMS
 * @since 1.10
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 */
final class cms_mailer
{
  private $_mailer;

  public function __construct()
  {
    $dir = dirname(__DIR__).'/phpmailer';
    require_once($dir.'/class.phpmailer.php');

    $this->_mailer = new PHPMailer;
    $this->_mailer->PluginDir = $dir;
    $this->reset();
  }

  public function reset()
  {
    $prefs = unserialize(cms_siteprefs::get('mailprefs'));
    $this->_mailer->Mailer = get_parameter_value($prefs,'mailer','mail');
    $this->_mailer->Sendmail = get_parameter_value($prefs,'sendmail','/usr/sbin/sendmail');
    $this->_mailer->Timeout = get_parameter_value($prefs,'timeout',60);
    $this->_mailer->Port = get_parameter_value($prefs,'port',25);
    $this->_mailer->FromName = get_parameter_value($prefs,'fromuser');
    $this->_mailer->From = get_parameter_value($prefs,'from');
    $this->_mailer->Host = get_parameter_value($prefs,'host');
    $this->_mailer->SMTPAuth = get_parameter_value($prefs,'smtpauth',0);
    $this->_mailer->Username = get_parameter_value($prefs,'username');
    $this->_mailer->Password = get_parameter_value($prefs,'password');
    $this->_mailer->SMTPSecure = get_parameter_value($prefs,'secure');
    $this->_mailer->CharSet = get_parameter_value($prefs,'charset','utf-8');
    $this->_mailer->ErrorInfo = '';
    $this->_mailer->ClearAddresses();
    $this->_mailer->ClearAttachments();
    $this->_mailer->ClearCustomHeaders();
    $this->_mailer->ClearBCCs();
    $this->_mailer->ClearCCs();
    $this->_mailer->ClearReplyTos();
  }

  public function GetAltBody()
  {
    return $this->_mailer->AltBody;
  }

  function SetAltBody( $txt )
  {
    $this->_mailer->AltBody = $txt;
  }

  function GetBody()
  {
    return $this->_mailer->Body;
  }

  function SetBody( $txt )
  {
    $this->_mailer->Body = $txt;
  }

  function GetCharSet()
  {
    return $this->_mailer->CharSet;
  }

  function SetCharSet( $txt )
  {
    $this->_mailer->CharSet = $txt;
  }

  function GetConfirmReadingTo()
  {
    return $this->_mailer->ConfirmReadingTo;
  }

  function SetConfirmReadingTo( $txt )
  {
    $this->_mailer->ConfirmReadingTo = $txt;
  }

  function GetContentType()
  {
    return $this->_mailer->ContentType;
  }

  function SetContentType( $txt )
  {
    $this->_mailer->ContentType = $txt;
  }

  function GetEncoding()
  {
    return $this->_mailer->Encoding;
  }

  function SetEncoding( $txt )
  {
    $this->_mailer->Encoding = $txt;
  }

  function GetErrorInfo()
  {
    return $this->_mailer->ErrorInfo;
  }

  function GetFrom()
  {
    return $this->_mailer->From;
  }

  function SetFrom( $txt )
  {
    $this->_mailer->From = $txt;
  }

  function GetFromName()
  {
    return $this->_mailer->FromName;
  }

  function SetFromName( $txt )
  {
    $this->_mailer->FromName = $txt;
  }

  function GetHelo()
  {
    return $this->_mailer->Helo;
  }

  function SetHelo( $txt )
  {
    $this->_mailer->Helo = $txt;
  }

  function GetHost()
  {
    return $this->_mailer->Host;
  }

  function SetHost( $txt )
  {
    $this->_mailer->Host = $txt;
  }

  function GetHostname()
  {
    return $this->_mailer->Hostname;
  }

  function SetHostname( $txt )
  {
    $this->_mailer->Hostname = $txt;
  }

  function GetMailer()
  {
    return $this->_mailer->Mailer;
  }

  function SetMailer( $txt )
  {
    $this->_mailer->Mailer = $txt;
  }

  function GetPassword()
  {
    return $this->_mailer->Password;
  }

  function SetPassword( $txt )
  {
    $this->_mailer->Password = $txt;
  }

  function GetPort()
  {
    return $this->_mailer->Port;
  }

  function SetPort( $txt )
  {
    $this->_mailer->Port = $txt;
  }

  function GetPriority()
  {
    return $this->_mailer->Priority;
  }

  function SetPriority( $txt )
  {
    $this->_mailer->Priority = $txt;
  }

  function GetSender()
  {
    return $this->_mailer->Sender;
  }

  function SetSender( $txt )
  {
    $this->_mailer->Sender = $txt;
  }

  function GetSendmail()
  {
    return $this->_mailer->Sendmail;
  }

  function SetSendmail( $txt )
  {
    $this->_mailer->Sendmail = $txt;
  }

  function GetSMTPAuth()
  {
    return $this->_mailer->SMTPAuth;
  }

  function SetSMTPAuth( $txt )
  {
    $this->_mailer->SMTPAuth = $txt;
  }

  function GetSMTPDebug()
  {
    return $this->_mailer->SMTPDebug;
  }

  function SetSMTPDebug( $txt )
  {
    $this->_mailer->SMTPDebug = $txt;
  }

  function GetSMTPKeepAlive()
  {
    return $this->_mailer->SMTPKeepAlive;
  }

  function SetSMTPKeepAlive( $txt )
  {
    $this->_mailer->SMTPKeepAlive = $txt;
  }

  function GetSubject()
  {
    return $this->_mailer->Subject;
  }

  function SetSubject( $txt )
  {
    $this->_mailer->Subject = $txt;
  }

  function GetTimeout()
  {
    return $this->_mailer->Timeout;
  }

  function SetTimeout( $txt )
  {
    $this->_mailer->Timeout = $txt;
  }

  function GetUsername()
  {
    return $this->_mailer->Username;
  }

  function SetUsername( $txt )
  {
    $this->_mailer->Username = $txt;
  }

  function GetWordWrap()
  {
    return $this->_mailer->WordWrap;
  }

  function SetWordWrap( $txt )
  {
    $this->_mailer->WordWrap = $txt;
  }

  function AddAddress( $address, $name = '' )
  {
    return $this->_mailer->AddAddress( $address, $name );
  }

  function AddAttachment( $path, $name = '', $encoding = 'base64', $type = 'application/octet-stream' )
  {
    return $this->_mailer->AddAttachment( $path, $name, $encoding, $type );
  }

  function AddBCC( $addr, $name = '' )
  {
    $this->_mailer->AddBCC( $addr, $name );
  }

  function AddCC( $addr, $name = '' )
  {
    $this->_mailer->AddCC( $addr, $name );
  }

  function AddCustomHeader( $txt )
  {
    $this->_mailer->AddCustomHeader( $txt );
  }

  function AddEmbeddedImage( $path, $cid, $name = '', $encoding = 'base64', $type = 'application/octet-stream' )
  {
    return $this->_mailer->AddEmbeddedImage( $path, $cid, $name, $encoding, $type );
  }

  function AddReplyTo( $addr, $name = '' )
  {
    $this->_mailer->AddReplyTo( $addr, $name );
  }


  function AddStringAttachment( $string, $filename, $encoding = 'base64', $type = 'application/octet-stream' )
  {
    $this->_mailer->AddStringAttachment( $string, $filename, $encoding, $type );
  }

  function ClearAddresses()
  {
    $this->_mailer->ClearAddresses();
  }

  function ClearAllRecipients()
  {
    $this->_mailer->ClearAllRecipients();
  }

  function ClearAttachments()
  {
    $this->_mailer->ClearAttachments();
  }

  function ClearBCCs()
  {
    $this->_mailer->ClearBCCs();
  }

  function ClearCCs()
  {
    $this->_mailer->ClearCCs();
  }

  function ClearCustomHeaders()
  {
    $this->_mailer->ClearCustomHeaders();
  }

  function ClearReplyTos()
  {
    $this->_mailer->ClearReplyTos();
  }
  
  function IsError()
  {
    return $this->_mailer->IsError();
  }

  function IsHTML($var = true)
  {
    return $this->_mailer->IsHTML($var);
  }

  function IsMail()
  {
    return $this->_mailer->IsMail();
  }

  function IsQmail()
  {
    return $this->_mailer->IsQmail();
  }

  function IsSendmail()
  {
    return $this->_mailer->IsSendmail();
  }

  function IsSMTP()
  {
    return $this->_mailer->IsSMTP();
  }

  function Send()
  {
    return $this->_mailer->Send();
  }

  function SetLanguage( $lang_type, $lang_path = '' )
  {
    return $this->_mailer->SetLanguage( $lang_type, $lang_path );
  }

  function SmtpClose()
  {
    return $this->_mailer->SmtpClose();
  }

  function GetSecure()
  {
    return $this->_mailer->SMTPSecure;
  }

  function SetSecure($value)
  {
    $value = strtolower($value);
    if( $value == '' || $value == 'ssl' || $value == 'tls' ) {
      $this->_mailer->SMTPSecure = $value;
    }
  }

} // end of class
