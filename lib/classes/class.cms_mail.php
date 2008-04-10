<?php
#CMS - CMS Made Simple
#(c)2004-2006 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
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
#$Id: class.cms_anonymous_user.php 4093 2007-08-27 00:44:25Z wishy $

class CmsMail extends CmsObject
{
  var $mailer_object;

  function __construct()
  {
    parent::__construct();
    $this->mailer_object = null;

    $config = cms_config();
    $fn = cms_join_path($config['root_path'],'lib',
			'phpmailer','class.phpmailer.php');
    require_once($fn);
    $this->mailer_object = new PHPMailer;
    $this->reset();
  }


  public function really_reset()
  {
    $this->mailer_object = new PHPMailer;
    $this->mailer_object->Timeout  = CmsApplication::get_preference('mail_timout');
    $this->mailer_object->Mailer   = CmsApplication::get_preference('mail_mailer');
    $this->mailer_object->Host     = CmsApplication::get_preference('mail_host');
    $this->mailer_object->Port     = CmsApplication::get_preference('mail_port');
    $this->mailer_object->SMTPAuth = CmsApplication::get_preference('mail_smtpauth');
    $this->mailer_object->Username = CmsApplication::get_preference('mail_smtpauthuser');
    $this->mailer_object->Password = CmsApplication::get_preference('mail_smtpauthpw');
    $this->mailer_object->Sendmail = CmsApplication::get_preference('mail_sendmail');
    $this->mailer_object->FromName = CmsApplication::get_preference('mail_fromuser');
    $this->mailer_object->From     = CmsApplication::get_preference('mail_from');
  }


  public function reset()
  {
    $this->mailer_object->Timeout  = CmsApplication::get_preference('mail_timout');
    $this->mailer_object->Mailer   = CmsApplication::get_preference('mail_mailer');
    $this->mailer_object->Host     = CmsApplication::get_preference('mail_host');
    $this->mailer_object->Port     = CmsApplication::get_preference('mail_port');
    $this->mailer_object->SMTPAuth = CmsApplication::get_preference('mail_smtpauth');
    $this->mailer_object->Username = CmsApplication::get_preference('mail_smtpauthuser');
    $this->mailer_object->Password = CmsApplication::get_preference('mail_smtpauthpw');
    $this->mailer_object->Sendmail = CmsApplication::get_preference('mail_sendmail');
    $this->mailer_object->FromName = CmsApplication::get_preference('mail_fromuser');
    $this->mailer_object->From     = CmsApplication::get_preference('mail_from');
    $this->mailer_object->ClearAddresses();
    $this->mailer_object->ClearAttachments();
    $this->mailer_object->ClearCustomHeaders();
    $this->mailer_object->ClearBCCs();
    $this->mailer_object->ClearCCs();
    $this->mailer_object->ClearReplyTos();
  }


  function get_alt_body()
  {
    return $this->mailer_object->AltBody();
  }


  function set_alt_body($txt)
  {
    $this->mailer_object->AltBody = $txt;
  }


  function get_body()
  {
    return $this->mailer_object->Body;
  }


  function set_body( $txt )
  {
    $this->mailer_object->Body = $txt;
  }


  function get_charset()
  {
    return $this->mailer_object->CharSet;
  }


  function set_charset($txt)
  {
    $this->mailer_object->CharSet = $txt;
  }


  function get_confirm_reading_to()
  {
    return $this->mailer_object->ConfirmReadingTo;
  }


  function set_confirm_reading_to($val)
  {
    $this->mailer_object->ConfirmReadingTo = $val;
  }


  function get_content_type()
  {
    return $this->mailer_object->ContentType;
  }


  function set_content_type($val)
  {
    $this->mailer_object->ContentType = $val;
  }


  function get_encoding()
  {
    return $this->mailer_object->Encoding;
  }


  function set_encoding($val)
  {
    $this->mailer_object->Encoding = $val;
  }


  function get_error_info()
  {
    return $this->mailer_object->ErrorInfo;
  }
 

  function get_from()
  {
    return $this->mailer_object->From;
  }


  function set_from($val)
  {
    $this->mailer_object->From = $val;
  }


  function get_from_name()
  {
    return $this->mailer_object->FromName;
  }


  function set_from_name($val)
  {
    $this->mailer_object->FromName = $val;
  }


  function get_helo()
  {
    return $this->mailer_object->Helo;
  }


  function set_helo($val)
  {
    $this->mailer_object->Helo = $val;
  }


  function get_host()
  {
    return $this->mailer_object->Host;
  }


  function set_host($val)
  {
    $this->mailer_object->Host = $val;
  }


  function get_hostname()
  {
    return $this->mailer_object->Hostname;
  }


  function set_hostname($val)
  {
    $this->mailer_object->Hostname = $val;
  }


  function get_mailer()
  {
    return $this->mailer_object->Mailer;
  }


  function set_mailer($val)
  {
    $this->mailer_object->Host = $val;
  }

  
  function get_password()
  {
    return $this->mailer_object->Password;
  }


  function set_password($val)
  {
    $this->mailer_object->Password = $val;
  }


  function get_port()
  {
    return $this->mailer_object->Port;
  }


  function set_port($val)
  {
    $this->mailer_object->Port = $val;
  }
  
  
  function get_priority()
  {
    return $this->mailer_object->Priority;
  }


  function set_priority($val)
  {
    $this->mailer_object->Priority = $val;
  }


  function get_sender()
  {
    return $this->mailer_object->Sender;
  }


  function set_sender($val)
  {
    $this->mailer_object->Sender = $val;
  }


  function get_sendmail()
  {
    return $this->mailer_object->Sendmail;
  }


  function set_sendmail($val)
  {
    $this->mailer_object->Sendmail = $val;
  }


  function get_smtp_auth()
  {
    return $this->mailer_object->SMTPAuth;
  }


  function set_smtp_auth($val)
  {
    $this->mailer_object->SMTPAuth = $val;
  }


  function get_smtp_debug()
  {
    return $this->mailer_object->SMTPDebug;
  }


  function set_smtp_debug($val)
  {
    $this->mailer_object->SMTPDebug = $val;
  }


  function get_smtp_keepalive()
  {
    return $this->mailer_object->SMTPKeepAlive;
  }


  function set_smtp_keepalive($val)
  {
    $this->mailer_object->SMTPKeepAlive = $val;
  }


  function get_subject()
  {
    return $this->mailer_object->Subject;
  }


  function set_subject($val)
  {
    $this->mailer_object->Subject = $val;
  }


  function get_timeout()
  {
    return $this->mailer_object->Timeout;
  }


  function set_timeout($val)
  {
    $this->mailer_object->Timeout = $val;
  }


  function get_username()
  {
    return $this->mailer_object->Username;
  }


  function set_username($val)
  {
    $this->mailer_object->Username = $val;
  }


  function get_wordwrap()
  {
    return $this->mailer_object->WordWrap;
  }


  function set_wordwrap($val)
  {
    $this->mailer_object->WordWrap = $val;
  }


  function add_address( $address, $name = '' )
  {
    return $this->mailer_object->AddAddress($address,$name);
  }


  function add_attachment( $path, $name = '', $encoding = 'base64',
			   $type = 'application/octet-stream' )
  {
    return $this->mailer_object->AddAttachment( $path, $name, $encoding, $type );
  }


  function add_bcc( $addr, $name = '' )
  {
    $this->mailer_object->AddBCC( $addr, $name );
  }


  function add_cc( $addr, $name = '' )
  {
    $this->mailer_object->AddCC( $addr, $name );
  }


  function add_custom_header( $txt )
  {
    $this->mailer_object->AddCustomHeader( $txt );
  }


  function add_embedded_image( $path, $cid, $name = '', $encoding = 'base64',
			     $type = 'application/octet-stream' )
  {
    return $this->mailer_object->AddEmbeddedImage( $path, $cid, 
						$name, $encoding, $type );
  }


  function add_reply_to( $addr, $name = '' )
  {
    $this->mailer_object->AddReplyTo( $addr, $name );
  }


  function add_string_attachment( $string, $filename, $encoding = 'base64',
				$type = 'application/octet-stream' )
  {
    $this->mailer_object->AddStringAttachment( $string, $filename, $encoding, $type );
  }


  function clear_addresses()
  {
    $this->mailer_object->ClearAddresses();
  }


  function clear_all_recipients()
  {
    $this->mailer_object->ClearAllRecipients();
  }


  function clear_attachments()
  {
    $this->mailer_object->ClearAttachments();
  }


  function clear_bccs()
  {
    $this->mailer_object->ClearBCCs();
  }


  function clear_ccs()
  {
    $this->mailer_object->ClearCCs();
  }


  function clear_custom_headers()
  {
    $this->mailer_object->ClearCustomHeaders();
  }


  function clear_reply_tos()
  {
    $this->mailer_object->ClearReplyTos();
  }

  
  function is_error()
  {
    return $this->mailer_object->IsError();
  }


  function is_HTML($var = true)
  {
    return $this->mailer_object->IsHTML($var);
  }

  function is_mail()
  {
    return $this->mailer_object->IsMail();
  }


  function is_qmail()
  {
    return $this->mailer_object->IsQmail();
  }


  function is_sendmail()
  {
    return $this->mailer_object->IsSendmail();
  }


  function is_SMTP()
  {
    return $this->mailer_object->IsSMTP();
  }


  function send()
  {
    return $this->mailer_object->Send();
  }


  function set_language( $lang_type, $lang_path = '' )
  {
    return $this->mailer_object->SetLanguage( $lang_type, $lang_path );
  }


  function smtp_close()
  {
    return $this->mailer_object->SmtpClose();
  }
  
}

# vim:ts=4 sw=4 noet
?>