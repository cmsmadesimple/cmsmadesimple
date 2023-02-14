<?php
#-------------------------------------------------------------------------
# Module: CMSMailer - a simple wrapper around phpmailer
# copyright (c) Robert Campbell <rob@techcom.dyndns.org>
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
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * This file contains the class that wraps PHPMailer usage for CMSMS.
 *
 * @package CMS
 * @license GPL
 */

/**
 * A class for sending email.
 *
 * Prior to CMSMS 2.0 this class was implemented as a core module.
 *
 * @package CMS
 * @license GPL
 * @since 2.0
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 */
class cms_mailer
{
  /**
   * @ignore
   */
  private $_mailer;

  /**
   * Constructor
   *
   * @param bool $exceptions Optionally disable exceptions, and rely on error strings.
   */
  public function __construct($exceptions = true)
  {
    $dir = dirname(__DIR__).'/phpmailer/';
    
    require_once($dir.'Exception.php');
    require_once($dir.'PHPMailer.php');
    require_once($dir.'SMTP.php');

    $this->_mailer = new PHPMailer($exceptions);
    $this->reset();
  }

  /**
   * __call
   *
   * @ignore
   * @param string $method Call method to call from PHP Mailer
   * @param array $args Arguments passed to PHP Mailer method
   */
  public function __call($method, $args)
  {
    if(method_exists($this->_mailer, $method))
    {
      return call_user_func_array([$this->_mailer, $method], $args);
    }
  }
  
  /**
   * @param $name
   * @param $value
   */
  public function __set($name, $value)
  {
    if(property_exists($this->_mailer, $name))
    {
      $this->_mailer->$name = $value;
    }
  }
  
  /**
   * @param $name
   */
  public function __get($name)
  {
    if(property_exists($this->_mailer, $name))
    {
      return $this->_mailer->$name;
    }
    
    return null;
  }
  
  /**
   * @param $name
   *
   * @return bool
   */
  public function __isset($name)
  {
    return property_exists($this->_mailer, $name);
  }

  /**
   * Reset the mailer to standard settings
   * We use the set global preferences here
   */
  public function reset()
  {
    $prefs                      = unserialize(cms_siteprefs::get('mailprefs'));
    $this->_mailer->Mailer      = get_parameter_value($prefs, 'mailer', 'mail');
    $this->_mailer->Sendmail    = get_parameter_value($prefs, 'sendmail', '/usr/sbin/sendmail');
    $this->_mailer->Timeout     = get_parameter_value($prefs, 'timeout', 60);
    $this->_mailer->Port        = get_parameter_value($prefs, 'port', 25);
    $this->_mailer->FromName    = get_parameter_value($prefs, 'fromuser');
    $this->_mailer->From        = get_parameter_value($prefs, 'from');
    $this->_mailer->Host        = get_parameter_value($prefs, 'host');
    $this->_mailer->SMTPAuth    = get_parameter_value($prefs, 'smtpauth', 0);
    $this->_mailer->SMTPAutoTLS = get_parameter_value($prefs, 'smtpautotls', 1);
    $this->_mailer->Username    = get_parameter_value($prefs, 'username');
    $this->_mailer->Password    = get_parameter_value($prefs, 'password');
    $this->_mailer->SMTPSecure  = get_parameter_value($prefs, 'secure');
    $this->_mailer->CharSet     = get_parameter_value($prefs, 'charset', 'utf-8');
    $this->_mailer->ErrorInfo   = '';
    $this->_mailer->ClearAllRecipients();
    $this->_mailer->ClearAttachments();
    $this->_mailer->ClearCustomHeaders();
    $this->_mailer->ClearReplyTos();
  }

  /**
   * Retrieve the alternate body of the email message
   * @return string
   */
  public function GetAltBody()
  {
    return $this->_mailer->AltBody;
  }

  /**
   * Set the alternate body of the email message
   *
   * For HTML messages the alternate body contains a text only string for email clients without HTML support.
   * @param string $txt
   */
  function SetAltBody( $txt )
  {
    $this->_mailer->AltBody = $txt;
  }

  /**
   * Retrieve the body of the email message
   *
   * @return string
   */
  function GetBody()
  {
    return $this->_mailer->Body;
  }

  /**
   * Set the body of the email message.
   *
   * If the email message is in HTML format this can contain HTML code.  Otherwise it should contain only text.
   * @param string $txt
   */
  function SetBody( $txt )
  {
    $this->_mailer->Body = $txt;
  }

  /**
   * Return the character set for the email
   * @return string
   */
  function GetCharSet()
  {
    return $this->_mailer->CharSet;
  }

  /**
   * Set the character set for the message.
   * Normally, the reset routine sets this to a system wide default value.
   *
   * @param string $charset
   */
  function SetCharSet( $charset )
  {
    $this->_mailer->CharSet = $charset;
  }

  /**
   * Retrieve the reading confirmation email address
   *
   * @return string The email address (if any) that will recieve the reading confirmation.
   */
  function GetConfirmReadingTo()
  {
    return $this->_mailer->ConfirmReadingTo;
  }

  /**
   * Set the email address that confirmations of email reading will be sent to.
   *
   * @param string $email
   */
  function SetConfirmReadingTo( $email )
  {
    $this->_mailer->ConfirmReadingTo = $email;
  }

  /**
   * Get the encoding of the message.
   * @return string
   */
  function GetEncoding()
  {
    return $this->_mailer->Encoding;
  }

  /**
   * Sets the encoding of the message.
   *
   * Possible values are: 8bit, 7bit, binary, base64, and quoted-printable
   * @param string $encoding
   */
  function SetEncoding( $encoding )
  {
    switch( strtolower($encoding) ) {
    case '8bit':
    case '7bit':
    case 'binary':
    case 'base64':
    case 'quoted-printable':
      $this->_mailer->Encoding = $encoding;
      break;
    default:
      // throw exception
    }
  }

  /**
   * Return the error information from the last error.
   * @return string
   */
  function GetErrorInfo()
  {
    return $this->_mailer->ErrorInfo;
  }

  /**
   * Get the from address for the email
   *
   * @return string
   */
  function GetFrom()
  {
    return $this->_mailer->From;
  }

  /**
   * Set the from address for the email
   *
   * @param string $email Th email address that the email will be from.
   */
  function SetFrom( $email )
  {
    $this->_mailer->From = $email;
  }

  /**
   * Get the real name that the email will be sent from
   * @return string
   */
  function GetFromName()
  {
    return $this->_mailer->FromName;
  }

  /**
   * Set the real name that this email will be sent from.
   *
   * @param string $name
   */
  function SetFromName( $name )
  {
    $this->_mailer->FromName = $name;
  }

  /**
   * Gets the SMTP HELO of the message
   * @return string
   */
  function GetHelo()
  {
    return $this->_mailer->Helo;
  }

  /**
   * Sets the SMTP HELO of the message (Default is $Hostname)
   * @param string $helo
   */
  function SetHelo( $helo )
  {
    $this->_mailer->Helo = $helo;
  }

  /**
   * Get the SMTP host values
   *
   * @return string
   */
  function GetSMTPHost()
  {
    return $this->_mailer->Host;
  }

  /**
   * Set the SMTP host(s).
   *
   * Only applicable when using SMTP mailer.  All hosts must be separated with a semicolon.
   * you can also specify a different port for each host by using the format hostname:port
   * (e.g. "smtp1.example.com:25;smtp2.example.com").
   * Hosts will be tried in order
   * @param string $host
   */
  function SetSMTPHost( $host )
  {
    $this->_mailer->Host = $host;
  }

  /**
   * Get the hostname that will be used in the Message-Id and Recieved headers
   * and the default HELO string.
   * @return string
   */
  function GetHostname()
  {
    return $this->_mailer->Hostname;
  }

  /**
   * Set the hostname to use in the Message-Id and Received headers
   * and as the default HELO string.  If empty the value will be calculated
   * @param string $hostname
   */
  function SetHostname( $hostname )
  {
    $this->_mailer->Hostname = $hostname;
  }

  /**
   * Retrieve the name of the mailer that will be used to send the message.
   * @return string
   */
  function GetMailer()
  {
    return $this->_mailer->Mailer;
  }

  /**
   * Set the name of the mailer that will be used to send the message.
   *
   * possible values for this field are 'mail','smtp', and 'sendmail'
   * @param string $mailer
   */
  function SetMailer( $mailer )
  {
    $this->_mailer->Mailer = $mailer;
  }

  /**
   * Get the SMTP password
   * @return string
   */
  function GetSMTPPassword()
  {
    return $this->_mailer->Password;
  }

  /**
   * Set the SMTP password
   *
   * Only useful when using the SMTP mailer.
   *
   * @param string $password
   */
  function SetSMTPPassword( $password )
  {
    $this->_mailer->Password = $password;
  }

  /**
   * Get the default SMTP port number
   * @return int
   */
  function GetSMTPPort()
  {
    return $this->_mailer->Port;
  }

  /**
   * Set the default SMTP port
   *
   * This method is only useful when using the SMTP mailer.
   *
   * @param int $port
   */
  function SetSMTPPort( $port )
  {
    $port = max(1,(int) $port);
    $this->_mailer->Port = $port;
  }

  /**
   * Get the priority of the message
   * @return int
   */
  function GetPriority()
  {
    return (int) $this->_mailer->Priority;
  }

  /**
   * Set the priority of the message
   * (1 = High, 3 = Normal, 5 = low)
   * @param int $priority
   */
  function SetPriority( $priority )
  {
    $priority = max(1,min(5,$priority));
    $this->_mailer->Priority = $priority;
  }

  /**
   * Get the Sender (return-path) of the message.
   * @return string The email address for the Sender field
   */
  function GetSender()
  {
    return $this->_mailer->Sender;
  }

  /**
   * Set the Sender email (return-path) of the message.
   * @param string $sender
   */
  function SetSender( $sender )
  {
    $this->_mailer->Sender = $sender;
  }

  /**
   * Get the path to the sendmail executable
   * @param string
   */
  function GetSendmail()
  {
    return $this->_mailer->Sendmail;
  }

  /**
   * Set the path to the sendmail executable
   *
   * This path is only useful when using the sendmail mailer.
   * @param string $path
   * @see cms_mailer::SetMailer
   */
  function SetSendmail( $path )
  {
    $this->_mailer->Sendmail = $path;
  }

  /**
   * Retrieve the SMTP Auth flag
   * @return bool
   */
  function GetSMTPAuth()
  {
    return $this->_mailer->SMTPAuth;
  }

  /**
   * Set a flag indicating wether or not SMTP authentication is to be used when sending
   * mails via the SMTP mailer.
   *
   * @param bool $flag
   * @see cms_mailer::SetMailer
   */
  function SetSMTPAuth( $flag = true )
  {
    $this->_mailer->SMTPAuth = $flag;
  }

  /**
   * Get the current value of the SMTP Debug flag
   * @return bool
   */
  function GetSMTPDebug()
  {
    return $this->_mailer->SMTPDebug;
  }

  /**
   * Enable, or disable SMTP debugging
   *
   * This is only useful when using the SMTP mailer.
   *
   * @param bool $flag
   * @see cms_mailer::SetMailer
   */
  function SetSMTPDebug( $flag = TRUE )
  {
    $this->_mailer->SMTPDebug = $flag;
  }

  /**
   * Return the value of the SMTP keepalive flag
   * @return bool
   */
  function GetSMTPKeepAlive()
  {
    return $this->_mailer->SMTPKeepAlive;
  }

  /**
   * Prevents the SMTP connection from being closed after sending each message.
   * If this is set to true then SmtpClose must be used to close the connection
   *
   * This method is only useful when using the SMTP mailer.
   *
   * @param bool $flag
   * @see cms_mailer::SetMailer
   * @see cms_mailer::SmtpClose
   */
  function SetSMTPKeepAlive( $flag = true )
  {
    $this->_mailer->SMTPKeepAlive = $flag;
  }

  /**
   * Retrieve the subject of the message
   * @return string
   */
  function GetSubject()
  {
    return $this->_mailer->Subject;
  }

  /**
   * Set the subject of the message
   * @param string $subject
   */
  function SetSubject( $subject )
  {
    $this->_mailer->Subject = $subject;
  }

  /**
   * Get the SMTP server timeout (in seconds).
   * @return int
   */
  function GetSMTPTimeout()
  {
    return $this->_mailer->Timeout;
  }

  /**
   * Set the SMTP server timeout in seconds (for the SMTP mailer)
   * This function may not work with the win32 version.
   * @param int $timeout
   * @see cms_mailer::SetMailer
   */
  function SetSMTPTimeout( $timeout )
  {
    $this->_mailer->Timeout = $timeout;
  }

  /**
   * Get the SMTP username
   * @return string
   */
  function GetSMTPUsername()
  {
    return $this->_mailer->Username;
  }

  /**
   * Set the SMTP Username.
   *
   * This is only used when using the SMTP mailer with SMTP authentication.
   * @param string $username
   * @see cms_mailer::SetMailer
   */
  function SetSMTPUsername( $username )
  {
    $this->_mailer->Username = $username;
  }

  /**
   * Get the number of characters used in word wrapping.  0 indicates that no word wrapping
   * will be performed.
   * @return int
   */
  function GetWordWrap()
  {
    return $this->_mailer->WordWrap;
  }

  /**
   * Set word wrapping on the body of the message to the given number of characters
   * @param int $chars
   */
  function SetWordWrap( $chars )
  {
    $chars = max(0,min(1000,$chars));
    $this->_mailer->WordWrap = $chars;
  }

  /**
   * Add a "To" address.
   * @param string $address The email address
   * @param string $name    The real name
   * @return bool true on success, false if address already used
   */
  function AddAddress( $address, $name = '' )
  {
    return $this->_mailer->AddAddress( $address, $name );
  }

  /**
   * Adds an attachment from a path on the filesystem
   * @param string $path Complete file specification to the attachment
   * @param string $name Set the attachment name
   * @param string $encoding File encoding (see $encoding)
   * @param string $type (mime type for the attachment)
   * @return bool true on success, false on failure.
   */
  function AddAttachment( $path, $name = '', $encoding = 'base64', $type = 'application/octet-stream' )
  {
    return $this->_mailer->AddAttachment( $path, $name, $encoding, $type );
  }

  /**
   * Add a "BCC" (Blind Carbon Copy) address
   * @param string $addr The email address
   * @param string $name The real name.
   * @return bool true on success, false on failure.
   */
  function AddBCC( $addr, $name = '' )
  {
    $this->_mailer->AddBCC( $addr, $name );
  }

  /**
   * Add a "CC" (Carbon Copy) address
   * @param string $addr The email address
   * @param string $name The real name.
   * @return bool true on success, false on failure.
   */
  function AddCC( $addr, $name = '' )
  {
    $this->_mailer->AddCC( $addr, $name );
  }

  /**
   * Add a custom header to the output email
   *
   * i.e: $obj->AddCustomHeader('X-MYHEADER: some-value');
   * @param string $header
   */
  function AddCustomHeader( $header )
  {
    $this->_mailer->AddCustomHeader( $header );
  }

  /**
   * Adds an embedded attachment.  This can include images, sounds, and
   * just about any other document.  Make sure to set the $type to an
   * image type.  For JPEG images use "image/jpeg" and for GIF images
   * use "image/gif".
   * @param string $path Path to the attachment.
   * @param string $cid Content ID of the attachment.  Use this to identify
   *        the Id for accessing the image in an HTML form.
   * @param string $name Overrides the attachment name.
   * @param string $encoding File encoding (see $Encoding).
   * @param string $type File extension (MIME) type.
   * @return bool
   */
  function AddEmbeddedImage( $path, $cid, $name = '', $encoding = 'base64', $type = 'application/octet-stream' )
  {
    return $this->_mailer->AddEmbeddedImage( $path, $cid, $name, $encoding, $type );
  }

  /**
   * Adds a "Reply-to" address.
   * @param string $addr
   * @param string $name
   * @return bool
   */
  function AddReplyTo( $addr, $name = '' )
  {
    $this->_mailer->AddReplyTo( $addr, $name );
  }

  /**
   * Adds a string or binary attachment (non-filesystem) to the list.
   * This method can be used to attach ascii or binary data,
   * such as a BLOB record from a database.
   * @param string $string String attachment data.
   * @param string $filename Name of the attachment.
   * @param string $encoding File encoding (see $Encoding).
   * @param string $type File extension (MIME) type.
   */
  function AddStringAttachment( $string, $filename, $encoding = 'base64', $type = 'application/octet-stream' )
  {
    $this->_mailer->AddStringAttachment( $string, $filename, $encoding, $type );
  }

  /**
   * Clears all recipients in the To list
   * @see cms_mailer::AddAddress
   */
  function ClearAddresses()
  {
    $this->_mailer->ClearAddresses();
  }

  /**
   * Clears all recipients in the To,CC, and BCC lists
   * @see cms_mailer::AddAddress
   * @see cms_mailer::AddCC
   * @see cms_mailer::AddBCC
   */
  function ClearAllRecipients()
  {
    $this->_mailer->ClearAllRecipients();
  }

  /**
   * Clears all attachments
   * @see cms_mailer::AddAttachment
   * @see cms_mailer::AddStringAttachment
   * @see cms_mailer::AddEmbeddedImage
   */
  function ClearAttachments()
  {
    $this->_mailer->ClearAttachments();
  }

  /**
   * Clear all recipients on the BCC list
   * @see cms_mailer::AddCC
   */
  function ClearBCCs()
  {
    $this->_mailer->ClearBCCs();
  }

  /**
   * Clear all recipients on the CC list
   * @see cms_mailer::AddCC
   */
  function ClearCCs()
  {
    $this->_mailer->ClearCCs();
  }

  /**
   * Clear all custom headers
   * @see cms_mailer::AddCustomHeader
   */
  function ClearCustomHeaders()
  {
    $this->_mailer->ClearCustomHeaders();
  }

  /**
   * Clear the Reply-To list
   * @see cms_mailer::AddReplyTo
   */
  function ClearReplyTos()
  {
    $this->_mailer->ClearReplyTos();
  }

  /**
   * Test if there was an error on the last message send
   * @return bool
   */
  function IsError()
  {
    return $this->_mailer->IsError();
  }

  /**
   * Set the message type to HTML.
   * @param bool $html
   */
  function IsHTML($html = true)
  {
    return $this->_mailer->IsHTML($html);
  }

  /**
   * Test if the mailer is set to 'mail'
   * @return bool
   */
  function IsMail()
  {
    return $this->_mailer->IsMail();
  }

  /**
   * Test if the mailer is set to 'sendmail'
   * @return bool
   */
  function IsSendmail()
  {
    return $this->_mailer->IsSendmail();
  }

  /**
   * Test if the mailer is set to 'SMTP'
   * @return bool
   */
  function IsSMTP()
  {
    return $this->_mailer->IsSMTP();
  }

  /**
   * Send the current message using all current settings.
   *
   * This method may throw exceptions if $exceptions were enabled in the constructor
   *
   * @return bool
   * @see cms_mailer::__construct
   */
  function Send()
  {
    return $this->_mailer->Send();
  }

  /**
   * Set the language for all error messages
   * @param string $lang_type
   */
  function SetLanguage($lang_type)
  {
    return $this->_mailer->SetLanguage($lang_type);
  }

  /**
   * Close the SMTP connection
   * Only necessary when using the SMTP mailer with keepalive enaboed.
   * @see cms_mailer::SetSMTPKeepAlive
   */
  function SmtpClose()
  {
    return $this->_mailer->SmtpClose();
  }

  /**
   * Gets the secure SMTP connection mode, or none
   * @return string
   */
  function GetSMTPSecure()
  {
    return $this->_mailer->SMTPSecure;
  }

  /**
   * Set the secure SMTP connection mode, or none
   * possible values are "", "ssl", or "tls"
   * @param string $value
   */
  function SetSMTPSecure($value)
  {
    $value = strtolower($value);
    if( $value == '' || $value == 'ssl' || $value == 'tls' ) $this->_mailer->SMTPSecure = $value;
  }

} // end of class

?>
