<?php
$lang['info_cmsmailer'] = 'Modul, ktor&yacute; využ&iacute;vaj&uacute; ostatn&eacute; moduly na odosielanie e-mailov. Nastavte hodnoty, podľa možnost&iacute; v&aacute;&scaron;ho poskytovateľa web priestoru. V pr&iacute;pade, že sa v&aacute;m niž&scaron;ie nepodar&iacute; odoslať testovaciu spr&aacute;vu, sk&uacute;ste kontaktovať v&aacute;&scaron;ho administr&aacute;tora.';
$lang['charset'] = 'K&oacute;dovanie';
$lang['sendtestmailconfirm'] = 'Odo&scaron;le testovaciu spr&aacute;vu na zadan&uacute; adresu. Ak odosielanie prebehne v poriadku, vr&aacute;tite sa na t&uacute;to ist&uacute; str&aacute;nku.  Chcete pokračovať?';
$lang['settingsconfirm'] = 'Zap&iacute;sať aktu&aacute;lne hodnoty?';
$lang['testsubject'] = 'CMSMailer testovacia spr&aacute;va';
$lang['testbody'] = 'Testovacia spr&aacute;va pre overenie funkčnosti nastaven&iacute; modulu CMSMailer module.

V pr&iacute;pade, že v&aacute;m spr&aacute;va dosrazila v poriadku, m&aacute;te modul nastaven&yacute; spr&aacute;vne.';
$lang['error_notestaddress'] = 'Chyba: Nebola zadan&aacute; testovacia adresa';
$lang['prompt_testaddress'] = 'Testovacia e-mailov&aacute; adresa';
$lang['sendtest'] = 'Odoslať testovaciu spr&aacute;vu';
$lang['password'] = 'Heslo';
$lang['username'] = 'Už&iacute;vateľsk&eacute; meno';
$lang['smtpauth'] = 'SMTP Authentifik&aacute;cia';
$lang['mailer'] = 'Mailer metoda';
$lang['host'] = 'N&aacute;zov SMTP hostu<br/><i>(alebo IP adresa)</i>';
$lang['port'] = 'Port SMTP serveru';
$lang['from'] = 'Z adresy';
$lang['fromuser'] = 'Z už&iacute;vateľsk&eacute;ho mena';
$lang['sendmail'] = 'Sendmail umiestnenie';
$lang['timeout'] = 'SMTP časov&yacute; limit';
$lang['submit'] = 'Poslať';
$lang['cancel'] = 'Zru&scaron;iť';
$lang['info_mailer'] = 'Sp&ocirc;sob odoslania emailu (sendmail, smtp, mail).  Bežne smtp, ak je dostupn&eacute;.';
$lang['info_host'] = 'N&aacute;zov SMTP hostu(plat&iacute; iba pri použit&iacute; smtp)';
$lang['info_port'] = 'Č&iacute;slo SMTP portu (bežne 25) (plat&iacute; iba pri použit&iacute; smtp)';
$lang['info_from'] = 'Adresa pužita, ako odosielateľ vo v&scaron;etk&yacute;ch emailoch';
$lang['info_fromuser'] = 'Priateľsk&eacute; meno použit&eacute; ako odosielateľ vo v&scaron;etk&yacute;ch emailoch';
$lang['info_sendmail'] = 'Kompletn&yacute; adres&aacute;r k sendmail in&scaron;tal&aacute;cii (plat&iacute; iba pri použit&iacute; sendmail)';
$lang['info_timeout'] = 'Počet sek&uacute;nd pri SMTP konverz&aacute;ci&iacute; pred ohl&aacute;sen&iacute;m chyby (plat&iacute; iba pri použit&iacute; smtp)';
$lang['info_smtpauth'] = 'Vyžaduje V&aacute;&scaron; SMTP server authentifik&aacute;ciu (plat&iacute; iba pri použit&iacute; smtp)';
$lang['info_username'] = 'SMTP meno (plat&iacute; iba pri použit&iacute; smtp, ak je vyžiadan&aacute; authentifik&aacute;cia)';
$lang['info_password'] = 'SMTP heslo (plat&iacute; iba pri použit&iacute; smtp, ak je vyžiadan&aacute; authentifik&aacute;cia)';
$lang['friendlyname'] = 'CMSMailer Modul';
$lang['postinstall'] = 'CMSMailer modul bol vporiadku nain&scaron;talovan&yacute;';
$lang['postuninstall'] = 'CMSMailer modul bol odin&scaron;talovna&yacute;... ľutujeme, že V&aacute;s op&uacute;&scaron;tame';
$lang['uninstalled'] = 'Modul odin&scaron;talovan&yacute;.';
$lang['installed'] = 'Modul verzie %s nain&scaron;talovan&yacute;.';
$lang['accessdenied'] = 'Nedovolen&yacute; pr&iacute;stup. Pros&iacute;m skontrolujte si opr&aacute;vnenia.';
$lang['error'] = 'Chyba!';
$lang['upgraded'] = 'Module upgradovan&yacute; na verziu %s.';
$lang['title_mod_prefs'] = 'Predvoľby modulu';
$lang['title_mod_admin'] = 'Administr&aacute;cia modulu';
$lang['title_admin_panel'] = 'CMSMailer Modul';
$lang['moddescription'] = 'toto je jednoduch&aacute; nadstavba PHPMailer triedy, je to equivalent API (funkcia pre funkciu) a jednoduch&eacute; rozhranie pre z&aacute;kladn&eacute; oper&aacute;cie.';
$lang['welcome_text'] = '<p>Vitajte v administr&aacute;cii CMSMailer modulu';
$lang['changelog'] = '<ul>
<li>Version 1.73.1. October, 2005. Prv&aacute; verzia.</li>
<li>Version 1.73.2. October, 2005. Minor bug fix with the admin panel.  The dropdown was not representing the current value from the preferences database</li>
<li>Version 1.73.3. October, 2005. Minor bug fix with sending html email</li>
<li>Version 1.73.4. November, 2005. Form fields in preferences are larger, fixed a problem with the fromuser, and called reset within the constructor</li>
<li>Version 1.73.5. November, 2005. Added the form fields and functionality for SMTP authentication.</li>
<li>Version 1.73.6. December, 2005. Default mailer method is SMTP on install, and improved documentation, and now I clear all the attachments, and addresses, etc. on reset.</li>
<li>Version 1.73.7. January, 2006. Increased field lengths in most fields</li>
<li>Version 1.73.8. January, 2006. Changed the preferences panel to be a bit more descriptive.</li>
</ul>';
$lang['help'] = 'CMS Made Simple Translation Center

Log out

Back to modules
CMS Mailer
Natural Language
Slovak/Slovenčina
This module is used by numerous other modules to facilitate sending emails.  It must be properly configured to your hosts requirements.  Please use the information provided by your host to adjust these settings.  If you still cannot get the test message to send properly, you may have to contact your host for assistance.
 
Character Set
 
This will send a test message to the address specified. If the send process suceeded, you will be returned to this page.  Do you want to continue?
 
Write current values to CMSMailer settings?
 
CMSMailer Test Message
 
This message is intended only to verify the validity of the settings in the CMSMailer module.
If you received it, everything is working fine.
 
Error: Test address not specified
 
Test Email Address
 
Send Test Message
 
Password
 
Username
 
SMTP Authentication
 
Mailer method
 
SMTP host name<br/><i>(or IP address)</i>
 
Port of SMTP server
 
From address
 
From Username
 
Sendmail location
 
SMTP timeout
 
Submit
 
Cancel
 
Mail method to use (sendmail, smtp, mail).  Usually smtp is the most reliable.
 
SMTP hostname (only valid for the smtp mailer method)
 
SMTP port number (usually 25) (only valid for the smtp mailer method)
 
Address used as the sender in all emails. <br/><strong>Note</strong>, this email address must be set correctly for your host or you will have difficulty sending emails.<br/>If you do not know the proper value for this setting, you may need to contact your host.
 
Friendly name used for sending all emails
 
The complete path to your sendmail executable (only valid for the sendmail mailer method)
 
The number of seconds in an SMTP conversation before an error occurs (valid for the smtp mailer method)
 
Does your smtp host require authentication (valid only for the smtp mailer method)
 
SMTP authentication username (valid only for smtp mailer method, when smtp auth is selected)
 
SMTP authentication password (valid only for smtp mailer method, when smtp auth is selected)
 
CMSMailer
 
CMSMailer module has been successfully installed
 
CMSMailer module uninstalled... sorry to see you leave
 
Module Uninstalled.
 
Module version %s installed.
 
Access Denied. Please check your permissions.
 
Error!
 
Module upgraded to version %s.
 
Module Preferences
 
Module Admin Panel
 
CMSMailer Module
 
This is a simple wrapper around PHPMailer, it has an equivalent API (function for function) and a simple interface for some defaults.
 
<p>Welcome to the CMSMailer module admin section
 

<ul>
<li>Version 1.73.1. October, 2005. Initial Release.</li>
<li>Version 1.73.2. October, 2005. Minor bug fix with the admin panel.  The dropdown was not representing the current value from the preferences database</li>
<li>Version 1.73.3. October, 2005. Minor bug fix with sending html email</li>
<li>Version 1.73.4. November, 2005. Form fields in preferences are larger, fixed a problem with the fromuser, and called reset within the constructor</li>
<li>Version 1.73.5. November, 2005. Added the form fields and functionality for SMTP authentication.</li>
<li>Version 1.73.6. December, 2005. Default mailer method is SMTP on install, and improved documentation, and now I clear all the attachments, and addresses, etc. on reset.</li>
<li>Version 1.73.7. January, 2006. Increased field lengths in most fields</li>
<li>Version 1.73.8. January, 2006. Changed the preferences panel to be a bit more descriptive.</li>
<li>Version 1.73.9. January, 2006. Added test email capability, and confirmation on each button (except cancel)</li>
<li>Version 1.73.10. August, 2006. Modified to use lazy loading to minimize memory footprint when CMSMailer is not being used.</li>
<li>Version 1.73.13. January, 2008.  Added more permissions checks.</li>
</ul>
 
<h3>What Does This Do?</h3>
<p>This module provides no end user functionality.  It is designed to be integrated into other modules to provide email capabilities.  Thats it, nothing more.</p>
<h3>How Do I Use It</h3>
<p>This module provides a simple wrapper around all of the methods and variables of phpmailer.  It is designed for use by other module developers, below is an example, and a brief API reference.  Please read the PHPMailer documentation included for more information.</p>
<h3>An Example</h3>
<pre>
  $cmsmailer = $this->GetModuleInstance(&#039;CMSMailer&#039;);
  $cmsmailer->AddAddress(&#039;calguy1000@hotmail.com&#039;,&#039;calguy&#039;);
  $cmsmailer->SetBody(&#039;&amp;lt;h4&amp;gt;This is a test message&amp;lt;/h4&amp;gt;&#039;);
  $cmsmailer->IsHTML(true);
  $cmsmailer->SetSubject(&#039;Test message&#039;);
  $cmsmailer->Send();
</pre>
<h3>API</h3>
<ul>
<li><p><b>void reset()</b></p>
<p>Reset the object back to the values specified in the admin panel</p>
</li>
<li><p><b>string GetAltBody()</b></p>
<p>Return the alternate body of the email</p>
</li>
<li><p><b>void SetAltBody( $string )</b></p>
<p>Set the alternate body of the email</p>
</li>
<li><p><b>string GetBody()</b></p>
<p>Return the primary body of the email</p>
</li>
<li><p><b>void SetBody( $string )</b></p>
<p>Set the primary body of the email</p>
</li>
<li><p><b>string GetCharSet()</b></p>
<p>Default: iso-8859-1</p>
<p>Return the mailer character set</p>
</li>
<li><p><b>void SetCharSet( $string )</b></p>
<p>Set the mailer character set</p>
</li>
<li><p><b>string GetConfirmReadingTo()</b></p>
<p>Return the address confirmed reading email flag</p>
</li>
<li><p><b>void SetConfirmReadingTo( $address )</b></p>
<p>Set or unset the confirm reading address</p>
</li>
<li><p><b>string GetContentType()</b></p>
<p>Default: text/plain</p>
<p>Return the content type</p>
</li>
<li><p><b>void SetContentType()</b></p>
<p>Set the content type</p>
</li>
<li><p><b>string GetEncoding()</b></p>
<p>Return the encoding</p>
</li>
<li><p><b>void SetEncoding( $encoding )</b></p>
<p>Set the encoding</p>
<p>Options are: 8bit, 7bit, binary, base64, quoted-printable</p>
</li>
<li><p><b>string GetErrorInfo()</b></p>
<p>Return any error information</p>
</li>
<li><p><b>string GetFrom()</b></p>
<p>Return the current originating address</p>
</li>
<li><p><b>void SetFrom( $address )</b></p>
<p>Set the originating address</p>
</li>
<li><p><b>string GetFromName()</b></p>
<p>Return the current originating name</p>
</li>
<li><p><b>SetFromName( $name )</b></p>
<p>Set the originating name</p>
</li>
<li><p><b>string GetHelo()</b></p>
<p>Return the HELO string</p>
</li>
<li><p><b>SetHelo( $string )</b></p>
<p>Set the HELO string</p>
<p>Default value: $hostname</p>
</li>
<li><p><b>string GetHost()</b></p>
<p>Return the SMTPs host separated by semicolon</p>
</li>
<li><p><b>void SetHost( $string )</b></p>
<p>Set the hosts</p>
</li>
<li><p><b>string GetHostName()</b></p>
<p>Return the hostname used for SMTP Helo</p>
</li>
<li><p><b>void SetHostName( $hostname )</b></p>
<p>Set the hostname used for SMTP Helo</p>
</li>
<li><p><b>string GetMailer()</b></p>
<p>Return the mailer</p>
</li>
<li><p><b>void SetMailer( $mailer )</b></p>
<p>Set the mailer, either sendmail,mail, or smtp</p>
</li>
<li><p><b>string GetPassword()</b></p>
<p>Return the password for smtp auth</p>
</li>
<li><p><b>void SetPassword( $string )</b></p>
<p>Set the password for smtp auth</p>
</li>
<li><p><b>int GetPort()</b></p>
<p>Return the port number for smtp connections</p>
</li>
<li><p><b>void SetPort( $int )</b></p>
<p>Set the port for smtp connections</p>
</li>
<li><p><b>int GetPriority()</b></p>
<p>Return the message priority</p>
</li>
<li><p><b>void SetPriority( int )</b></p>
<p>Set the message priority</p>
<p>Values are 1=High, 3 = Normal, 5 = Low</p>
</li>
<li><p><b>string GetSender()</b></p>
<p>Return the sender email (return path) string</p>
</li>
<li><p><b>void SetSender( $address )</b></p>
<p>Set the sender string</p>
</li>
<li><p><b>string GetSendmail()</b></p>
<p>Return the sendmail path</p>
</li>
<li><p><b>void SetSendmail( $path )</b></p>
<p>Set the sendmail path</p>
</li>
<li><p><b>bool GetSMTPAuth()</b></p>
<p>Return the current value of the smtp auth flag</p>
</li>
<li><p><b>SetSMTPAuth( $bool )</b></p>
<p>Set the smtp auth flag</p>
</li>
<li><p><b>bool GetSMTPDebug()</b></p>
<p>Return the value of the SMTP debug flag</p>
</li>
<li><p><b>void SetSMTPDebug( $bool )</b></p>
<p>Set the SMTP debug flag</p>
</li>
<li><p><b>bool GetSMTPKeepAlive()</b></p>
<p>Return the value of the SMTP keep alive flag</p>
</li>
<li><p><b>SetSMTPKeepAlive( $bool )</b></p>
<p>Set the SMTP keep alive flag</p>
</li>
<li><p><b>string GetSubject()</b></p>
<p>Return the current subject string</p>
</li>
<li><p><b>void SetSubject( $string )</b></p>
<p>Set the subject string</p>
</li>
<li><p><b>int GetTimeout()</b></p>
<p>Return the timeout value</p>
</li>
<li><p><b>void SetTimeout( $seconds )</b></p>
<p>Set the timeout value</p>
</li>
<li><p><b>string GetUsername()</b></p>
<p>Return the smtp auth username</p>
</li>
<li><p><b>void SetUsername( $string )</b></p>
<p>Set the smtp auth username</p>
</li>
<li><p><b>int GetWordWrap()</b></p>
<p>Return the wordwrap value</p>
</li>
<li><p><b>void SetWordWrap( $int )</b></p>
<p>Return the wordwrap value</p>
</li>
<li><p><b>AddAddress( $address, $name = &#039;&#039; )</b></p>
<p>Add a destination address</p>
</li>
<li><p><b>AddAttachment( $path, $name = &#039;&#039;, $encoding = &#039;base64&#039;, $type = &#039;application/octent-stream&#039; )</b></p>
<p>Add a file attachment</p>
</li>
<li><p><b>AddBCC( $address, $name = &#039;&#039; )</b></p>
<p>Add a BCC&#039;d destination address</p>
</li>
<li><p><b>AddCC( $address, $name = &#039;&#039; )</b></p>
<p>Add a CC&#039;d destination address</p>
</li>
<li><p><b>AddCustomHeader( $txt )</b></p>
<p>Add a custom header to the email</p>
</li>
<li><p><b>AddEmbeddedImage( $path, $cid, $name = &#039;&#039;, $encoding = &#039;base64&#039;, $type = &#039;application/octent-stream&#039; )</b></p>
<p>Add an embedded image</p>
</li>
<li><p><b>AddReplyTo( $address, $name = &#039;&#039; )</b></p>
<p>Add a reply to address</p>
</li>
<li><p><b>AddStringAttachment( $string, $filename, $encoding = &#039;base64&#039;, $type = &#039;application/octent-stream&#039; )</b></p>
<p>Add a file attachment</p>
</li>
<li><p><b>ClearAddresses()</b></p>
<p>Clear all addresses</p>
</li>
<li><p><b>ClearAllRecipients()</b></p>
<p>Clear all recipients</p>
</li>
<li><p><b>ClearAttachments()</b></p>
<p>Clear all attachments</p>
</li>
<li><p><b>ClearBCCs()</b></p>
<p>Clear all BCC addresses</p>
</li>
<li><p><b>ClearCCs()</b></p>
<p>Clear all CC addresses</p>
</li>
<li><p><b>ClearCustomHeaders()</b></p>
<p>Clear all custom headers</p>
</li>
<li><p><b>ClearReplyto()</b></p>
<p>Clear reply to address</p>
</li>
<li><p><b>IsError()</b></p>
<p>Check for an error condition</p>
</li>
<li><p><b>bool IsHTML( $bool )</b></p>
<p>Set the html flag</p>
<p><i>Note</i> possibly this should be a get and set method</p>
</li>
<li><p><b>bool IsMail()</b></p>
<p>Check wether we are using mail</p>
</li>
<li><p><b>bool IsQmail()</b></p>
<p>Check wether we are using qmail</p>
</li>
<li><p><b>IsSendmail()</b></p>
<p>Check wether we are using sendmail</p>
</li>
<li><p><b>IsSMTP()</b></p>
<p>Check wether we are using smtp</p>
</li>
<li><p><b>Send()</b></p>
<p>Send the currently prepared email</p>
</li>
<li><p><b>SetLanguage( $lang_type, $lang_path = &#039;&#039; )</b></p>
<p>Set the current language and <em>(optional)</em> language path</p>
</li>
<li><p><b>SmtpClose()</b></p>
<p>Close the smtp connection</p>
</li>
</ul>
<h3>Support</h3>
<p>This module does not include commercial support. However, there are a number of resources available to help you with it:</p>
<ul>
<li>For the latest version of this module, FAQs, or to file a Bug Report or buy commercial support, please visit calguys homepage at <a href=&#039;http://techcom.dyndns.org&#039;>techcom.dyndns.org</a>.</li>
<li>Additional discussion of this module may also be found in the <a href=&#039;http://forum.cmsmadesimple.org&#039;>CMS Made Simple Forums</a>.</li>
<li>The author, calguy1000, can often be found in the <a href=&#039;irc://irc.freenode.net/#cms&#039;>CMS IRC Channel</a>.</li>
<li>Lastly, you may have some success emailing the author directly.</li>  
</ul>
<p>As per the GPL, this software is provided as-is. Please read the text
of the license for the full disclaimer.</p>

<h3>Copyright and License</h3>
<p>Copyright &amp;copy; 2005, Robert Campbell <a href=&#039;mailto:calguy1000@hotmail.com&#039;>&amp;lt;calguy1000@hotmail.com&amp;gt;</a>. All Rights Are Reserved.</p>
<p>This module has been released under the <a href=&#039;http://www.gnu.org/licenses/licenses.html#GPL&#039;>GNU Public License</a>. You must agree to this license before using the module.</p>
 

﻿';
$lang['utmz'] = '156861353.1228691676.223.15.utmccn=(referral)|utmcsr=burner.kuzmany.biz|utmcct=/install/upgrade.php|utmcmd=referral';
$lang['utma'] = '156861353.158291335300466100.1221906470.1229195154.1229197788.231';
$lang['utmc'] = '156861353';
$lang['utmb'] = '156861353';
?>