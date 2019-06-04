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

/**
 * This file contains the class that wraps PHPMailer usage for CMSMS.
 *
 * @package CMS
 * @license GPL
 */
use PHPMailer\PHPMailer\PHPMailer;

/**
 * A class for sending email.
 *
 * Prior to CMSMS 2.0 this class was implemented as a core module.
 *
 * This class should not be instantiated directly.
 *
 * @package CMS
 * @license GPL
 * @since 2.0
 * @author Robert Campbell (calguy1000@cmsmadesimple.org)
 * @see CmsApp::create_new_mailer()
 */
class cms_mailer
{

    /**
     * @ignore
     */
    private $mailer;

    /**
     * Constructor
     *
     * @param bool $exceptions Optionally disable exceptions, and rely on error strings.
     * @param bool $reset Whether or not to reset the mailer object using internal preferences.
     */
    public function __construct($exceptions = true, $reset = true)
    {
        $this->mailer = new PHPMailer($exceptions);
        if( $reset ) $this->reset();
    }

    /**
     * __call
     *
     * @ignore
     * @param string $method Call method to call from PHP Mailer
     * @param array $args Arguments passed to PHP Mailer method
     */
    public function __call($method,$args)
    {
        if(method_exists($this->mailer, $method))
        return call_user_func_array(array($this->mailer,$method), $args);
    }

    /**
     * Reset the mailer to standard settings
     */
    public function reset()
    {
        // note: should be passsing in preferences in the constructor.
	    // but that would break existing modules that construct this object directly
        $prefs = unserialize(cms_siteprefs::get('mailprefs'));
        if( !$prefs ) throw new \RuntimeException( 'CMS Mailer has not been configured' );
        $this->mailer->Mailer = get_parameter_value($prefs,'mailer','mail');
        $this->mailer->Sendmail = get_parameter_value($prefs,'sendmail','/usr/sbin/sendmail');
        $this->mailer->Timeout = get_parameter_value($prefs,'timeout',60);
        $this->mailer->Port = get_parameter_value($prefs,'port',25);
        $this->mailer->FromName = get_parameter_value($prefs,'fromuser');
        $this->mailer->From = get_parameter_value($prefs,'from');
        $this->mailer->Host = get_parameter_value($prefs,'host');
        $this->mailer->SMTPAuth = get_parameter_value($prefs,'smtpauth',0);
        $this->mailer->Username = get_parameter_value($prefs,'username');
        $this->mailer->Password = get_parameter_value($prefs,'password');
        $this->mailer->SMTPSecure = get_parameter_value($prefs,'secure');
        $this->mailer->CharSet = get_parameter_value($prefs,'charset','utf-8');
        $this->mailer->ErrorInfo = '';
        $this->mailer->ClearAllRecipients();
        $this->mailer->ClearAttachments();
        $this->mailer->ClearCustomHeaders();
        $this->mailer->ClearReplyTos();
    }

    /**
     * Retrieve the alternate body of the email message
     * @return string
     */
    public function GetAltBody()
    {
        return $this->mailer->AltBody;
    }

    /**
     * Set the alternate body of the email message
     *
     * For HTML messages the alternate body contains a text only string for email clients without HTML support.
     * @param string $txt
     */
    public function SetAltBody( $txt )
    {
        $this->mailer->AltBody = $txt;
    }

    /**
     * Retrieve the body of the email message
     *
     * @return string
     */
    public function GetBody()
    {
        return $this->mailer->Body;
    }

    /**
     * Set the body of the email message.
     *
     * If the email message is in HTML format this can contain HTML code.  Otherwise it should contain only text.
     * @param string $txt
     */
    public function SetBody( $txt )
    {
        $this->mailer->Body = $txt;
    }

    /**
     * Return the character set for the email
     * @return string
     */
    public function GetCharSet()
    {
        return $this->mailer->CharSet;
    }

    /**
     * Set the character set for the message.
     * Normally, the reset routine sets this to a system wide default value.
     *
     * @param string $charset
     */
    public function SetCharSet( $charset )
    {
        $this->mailer->CharSet = $charset;
    }

    /**
     * Retrieve the reading confirmation email address
     *
     * @return string The email address (if any) that will recieve the reading confirmation.
     */
    public function GetConfirmReadingTo()
    {
        return $this->mailer->ConfirmReadingTo;
    }

    /**
     * Set the email address that confirmations of email reading will be sent to.
     *
     * @param string $email
     */
    public function SetConfirmReadingTo( $email )
    {
        $this->mailer->ConfirmReadingTo = $email;
    }

    /**
     * Get the encoding of the message.
     * @return string
     */
    public function GetEncoding()
    {
        return $this->mailer->Encoding;
    }

    /**
     * Sets the encoding of the message.
     *
     * Possible values are: 8bit, 7bit, binary, base64, and quoted-printable
     * @param string $encoding
     */
    public function SetEncoding( $encoding )
    {
        switch( strtolower($encoding) ) {
            case '8bit':
            case '7bit':
            case 'binary':
            case 'base64':
            case 'quoted-printable':
                $this->mailer->Encoding = $encoding;
                break;
            default:
                // throw exception
        }
    }

    /**
     * Return the error information from the last error.
     * @return string
     */
    public function GetErrorInfo()
    {
        return $this->mailer->ErrorInfo;
    }

    /**
     * Get the from address for the email
     *
     * @return string
     */
    public function GetFrom()
    {
        return $this->mailer->From;
    }

    /**
     * Set the from address for the email
     *
     * @param string $email Th email address that the email will be from.
     */
    public function SetFrom( $email )
    {
        $this->mailer->From = $email;
    }

    /**
     * Get the real name that the email will be sent from
     * @return string
     */
    public function GetFromName()
    {
        return $this->mailer->FromName;
    }

    /**
     * Set the real name that this email will be sent from.
     *
     * @param string $name
     */
    public function SetFromName( $name )
    {
        $this->mailer->FromName = $name;
    }

    /**
     * Gets the SMTP HELO of the message
     * @return string
     */
    public function GetHelo()
    {
        return $this->mailer->Helo;
    }

    /**
     * Sets the SMTP HELO of the message (Default is $Hostname)
     * @param string $helo
     */
    public function SetHelo( $helo )
    {
        $this->mailer->Helo = $helo;
    }

    /**
     * Get the SMTP host values
     *
     * @return string
     */
    public function GetSMTPHost()
    {
        return $this->mailer->Host;
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
    public function SetSMTPHost( $host )
    {
        $this->mailer->Host = $host;
    }

    /**
     * Get the hostname that will be used in the Message-Id and Recieved headers
     * and the default HELO string.
     * @return string
     */
    public function GetHostname()
    {
        return $this->mailer->Hostname;
    }

    /**
     * Set the hostname to use in the Message-Id and Received headers
     * and as the default HELO string.  If empty the value will be calculated
     * @param string $hostname
     */
    public function SetHostname( $hostname )
    {
        $this->mailer->Hostname = $hostname;
    }

    /**
     * Retrieve the name of the mailer that will be used to send the message.
     * @return string
     */
    public function GetMailer()
    {
        return $this->mailer->Mailer;
    }

    /**
     * Set the name of the mailer that will be used to send the message.
     *
     * possible values for this field are 'mail','smtp', and 'sendmail'
     * @param string $mailer
     */
    public function SetMailer( $mailer )
    {
        $this->mailer->Mailer = $mailer;
    }

    /**
     * Get the SMTP password
     * @return string
     */
    public function GetSMTPPassword()
    {
        return $this->mailer->Password;
    }

    /**
     * Set the SMTP password
     *
     * Only useful when using the SMTP mailer.
     *
     * @param string $password
     */
    public function SetSMTPPassword( $password )
    {
        $this->mailer->Password = $password;
    }

    /**
     * Get the default SMTP port number
     * @return int
     */
    public function GetSMTPPort()
    {
        return $this->mailer->Port;
    }

    /**
     * Set the default SMTP port
     *
     * This method is only useful when using the SMTP mailer.
     *
     * @param int $port
     */
    public function SetSMTPPort( $port )
    {
        $port = max(1,(int) $port);
        $this->mailer->Port = $port;
    }

    /**
     * Get the priority of the message
     * @return int
     */
    public function GetPriority()
    {
        return (int) $this->mailer->Priority;
    }

    /**
     * Set the priority of the message
     * (1 = High, 3 = Normal, 5 = low)
     * @param int $priority
     */
    public function SetPriority( $priority )
    {
        $priority = max(1,min(5,$priority));
        $this->mailer->Priority = $priority;
    }

    /**
     * Get the Sender (return-path) of the message.
     * @return string The email address for the Sender field
     */
    public function GetSender()
    {
        return $this->mailer->Sender;
    }

    /**
     * Set the Sender email (return-path) of the message.
     * @param string $sender
     */
    public function SetSender( $sender )
    {
        $this->mailer->Sender = $sender;
    }

    /**
     * Get the path to the sendmail executable
     * @param string
     */
    public function GetSendmail()
    {
        return $this->mailer->Sendmail;
    }

    /**
     * Set the path to the sendmail executable
     *
     * This path is only useful when using the sendmail mailer.
     * @param string $path
     * @see cms_mailer::SetMailer
     */
    public function SetSendmail( $path )
    {
        $this->mailer->Sendmail = $path;
    }

    /**
     * Retrieve the SMTP Auth flag
     * @return bool
     */
    public function GetSMTPAuth()
    {
        return $this->mailer->SMTPAuth;
    }

    /**
     * Set a flag indicating wether or not SMTP authentication is to be used when sending
     * mails via the SMTP mailer.
     *
     * @param bool $flag
     * @see cms_mailer::SetMailer
     */
    public function SetSMTPAuth( $flag = true )
    {
        $this->mailer->SMTPAuth = $flag;
    }

    /**
     * Get the current value of the SMTP Debug flag
     * @return bool
     */
    public function GetSMTPDebug()
    {
        return $this->mailer->SMTPDebug;
    }

    /**
     * Enable, or disable SMTP debugging
     *
     * This is only useful when using the SMTP mailer.
     *
     * @param bool $flag
     * @see cms_mailer::SetMailer
     */
    public function SetSMTPDebug( $flag = TRUE )
    {
        $this->mailer->SMTPDebug = $flag;
    }

    /**
     * Return the value of the SMTP keepalive flag
     * @return bool
     */
    public function GetSMTPKeepAlive()
    {
        return $this->mailer->SMTPKeepAlive;
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
    public function SetSMTPKeepAlive( $flag = true )
    {
        $this->mailer->SMTPKeepAlive = $flag;
    }

    /**
     * Retrieve the subject of the message
     * @return string
     */
    public function GetSubject()
    {
        return $this->mailer->Subject;
    }

    /**
     * Set the subject of the message
     * @param string $subject
     */
    public function SetSubject( $subject )
    {
        $this->mailer->Subject = $subject;
    }

    /**
     * Get the SMTP server timeout (in seconds).
     * @return int
     */
    public function GetSMTPTimeout()
    {
        return $this->mailer->Timeout;
    }

    /**
     * Set the SMTP server timeout in seconds (for the SMTP mailer)
     * This function may not work with the win32 version.
     * @param int $timeout
     * @see cms_mailer::SetMailer
     */
    public function SetSMTPTimeout( $timeout )
    {
        $this->mailer->Timeout = $timeout;
    }

    /**
     * Get the SMTP username
     * @return string
     */
    public function GetSMTPUsername()
    {
        return $this->mailer->Username;
    }

    /**
     * Set the SMTP Username.
     *
     * This is only used when using the SMTP mailer with SMTP authentication.
     * @param string $username
     * @see cms_mailer::SetMailer
     */
    public function SetSMTPUsername( $username )
    {
        $this->mailer->Username = $username;
    }

    /**
     * Get the number of characters used in word wrapping.  0 indicates that no word wrapping
     * will be performed.
     * @return int
     */
    public function GetWordWrap()
    {
        return $this->mailer->WordWrap;
    }

    /**
     * Set word wrapping on the body of the message to the given number of characters
     * @param int $chars
     */
    public function SetWordWrap( $chars )
    {
        $chars = max(0,min(1000,$chars));
        $this->mailer->WordWrap = $chars;
    }

    /**
     * Add a "To" address.
     * @param string $address The email address
     * @param string $name    The real name
     * @return bool true on success, false if address already used
     */
    public function AddAddress( $address, $name = '' )
    {
        return $this->mailer->AddAddress( $address, $name );
    }

    /**
     * Adds an attachment from a path on the filesystem
     * @param string $path Complete file specification to the attachment
     * @param string $name Set the attachment name
     * @param string $encoding File encoding (see $encoding)
     * @param string $type (mime type for the attachment)
     * @return bool true on success, false on failure.
     */
    public function AddAttachment( $path, $name = '', $encoding = 'base64', $type = 'application/octet-stream' )
    {
        return $this->mailer->AddAttachment( $path, $name, $encoding, $type );
    }

    /**
     * Add a "BCC" (Blind Carbon Copy) address
     * @param string $addr The email address
     * @param string $name The real name.
     * @return bool true on success, false on failure.
     */
    public function AddBCC( $addr, $name = '' )
    {
        $this->mailer->AddBCC( $addr, $name );
    }

    /**
     * Add a "CC" (Carbon Copy) address
     * @param string $addr The email address
     * @param string $name The real name.
     * @return bool true on success, false on failure.
     */
    public function AddCC( $addr, $name = '' )
    {
        $this->mailer->AddCC( $addr, $name );
    }

    /**
     * Add a custom header to the output email
     *
     * i.e: $obj->AddCustomHeader('X-MYHEADER: some-value');
     * @param string $header
     */
    public function AddCustomHeader( $header )
    {
        $this->mailer->AddCustomHeader( $header );
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
    public function AddEmbeddedImage( $path, $cid, $name = '', $encoding = 'base64', $type = 'application/octet-stream' )
    {
        return $this->mailer->AddEmbeddedImage( $path, $cid, $name, $encoding, $type );
    }

    /**
     * Adds a "Reply-to" address.
     * @param string $addr
     * @param string $name
     * @return bool
     */
    public function AddReplyTo( $addr, $name = '' )
    {
        $this->mailer->AddReplyTo( $addr, $name );
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
    public function AddStringAttachment( $string, $filename, $encoding = 'base64', $type = 'application/octet-stream' )
    {
        $this->mailer->AddStringAttachment( $string, $filename, $encoding, $type );
    }

    /**
     * Clears all recipients in the To list
     * @see cms_mailer::AddAddress
     */
    public function ClearAddresses()
    {
        $this->mailer->ClearAddresses();
    }

    /**
     * Clears all recipients in the To,CC, and BCC lists
     * @see cms_mailer::AddAddress
     * @see cms_mailer::AddCC
     * @see cms_mailer::AddBCC
     */
    public function ClearAllRecipients()
    {
        $this->mailer->ClearAllRecipients();
    }

    /**
     * Clears all attachments
     * @see cms_mailer::AddAttachment
     * @see cms_mailer::AddStringAttachment
     * @see cms_mailer::AddEmbeddedImage
     */
    public function ClearAttachments()
    {
        $this->mailer->ClearAttachments();
    }

    /**
     * Clear all recipients on the BCC list
     * @see cms_mailer::AddCC
     */
    public function ClearBCCs()
    {
        $this->mailer->ClearBCCs();
    }

    /**
     * Clear all recipients on the CC list
     * @see cms_mailer::AddCC
     */
    public function ClearCCs()
    {
        $this->mailer->ClearCCs();
    }

    /**
     * Clear all custom headers
     * @see cms_mailer::AddCustomHeader
     */
    public function ClearCustomHeaders()
    {
        $this->mailer->ClearCustomHeaders();
    }

    /**
     * Clear the Reply-To list
     * @see cms_mailer::AddReplyTo
     */
    public function ClearReplyTos()
    {
        $this->mailer->ClearReplyTos();
    }

    /**
     * Test if there was an error on the last message send
     * @return bool
     */
    public function IsError()
    {
        return $this->mailer->IsError();
    }

    /**
     * Set the message type to HTML.
     * @param bool $html
     */
    public function IsHTML($html = true)
    {
        return $this->mailer->IsHTML($html);
    }

    /**
     * Test if the mailer is set to 'mail'
     * @return bool
     */
    public function IsMail()
    {
        return $this->mailer->IsMail();
    }

    /**
     * Test if the mailer is set to 'sendmail'
     * @return bool
     */
    public function IsSendmail()
    {
        return $this->mailer->IsSendmail();
    }

    /**
     * Test if the mailer is set to 'SMTP'
     * @return bool
     */
    public function IsSMTP()
    {
        return $this->mailer->IsSMTP();
    }

    /**
     * Send the current message using all current settings.
     *
     * This method may throw exceptions if $exceptions were enabled in the constructor
     *
     * @return bool
     * @see cms_mailer::__construct
     */
    public function Send()
    {
        return $this->mailer->Send();
    }

    /**
     * Set the language for all error messages
     * @param string $lang_type
     */
    public function SetLanguage($lang_type)
    {
        return $this->mailer->SetLanguage($lang_type);
    }

    /**
     * Close the SMTP connection
     * Only necessary when using the SMTP mailer with keepalive enaboed.
     * @see cms_mailer::SetSMTPKeepAlive
     */
    public function SmtpClose()
    {
        return $this->mailer->SmtpClose();
    }

    /**
     * Gets the secure SMTP connection mode, or none
     * @return string
     */
    public function GetSMTPSecure()
    {
        return $this->mailer->SMTPSecure;
    }

    /**
     * Set the secure SMTP connection mode, or none
     * possible values are "", "ssl", or "tls"
     * @param string $value
     */
    public function SetSMTPSecure($value)
    {
        $value = strtolower($value);
        if( $value == '' || $value == 'ssl' || $value == 'tls' ) $this->mailer->SMTPSecure = $value;
    }
} // end of class
