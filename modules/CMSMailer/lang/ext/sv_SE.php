<?php
$lang['info_cmsmailer'] = 'Den h&auml;r modulen anv&auml;nds av flera andra moduler f&ouml;r att g&ouml;ra det m&ouml;jligt att skicka e-postmeddelanden. Den m&aring;ste konfigureras beroende p&aring; ditt webbhotell. V&auml;nligen anv&auml;nd informationen fr&aring;n ditt webbhotell f&ouml;r att korrigera de h&auml;r inst&auml;llningarna. Om testmeddelandet &auml;nd&aring; inte skickas som det ska, kan du beh&ouml;va kontakta ditt webbhotell f&ouml;r hj&auml;lp.';
$lang['charset'] = 'Teckenupps&auml;ttning';
$lang['sendtestmailconfirm'] = 'Ett meddelande kommer att skickas till den adress du angav. Om leveransen lyckas kommer du att &aring;terv&auml;nda till denna sida. Vill du forts&auml;tta?';
$lang['settingsconfirm'] = 'Skriv dessa v&auml;rden till CMSMailer-inst&auml;llningarna?';
$lang['testsubject'] = 'CMSMailer Testmeddelande';
$lang['testbody'] = 'Syftet med detta meddelande &auml;r enbart att verifiera att inst&auml;llningarna i CMSMailer-modulen &auml;r korrekta.
Om du l&auml;ser detta meddelande fungerar allt som det ska.';
$lang['error_notestaddress'] = 'Fel: Testadress ej angiven';
$lang['prompt_testaddress'] = 'Testadress';
$lang['sendtest'] = 'Skicka testmeddelande';
$lang['password'] = 'L&ouml;senord';
$lang['username'] = 'Anv&auml;ndarnamn';
$lang['smtpauth'] = 'SMTP-autentisering';
$lang['mailer'] = 'Mail-metod';
$lang['host'] = 'SMTP-server<br /><em>(eller IP-adress)</em>';
$lang['port'] = 'Port f&ouml;r SMTP-server';
$lang['from'] = 'Fr&aring;n - epostadress';
$lang['fromuser'] = 'Fr&aring;n - anv&auml;ndarnamn';
$lang['sendmail'] = 'S&ouml;kv&auml;g till sendmail';
$lang['timeout'] = 'SMTP-timeout';
$lang['submit'] = 'Skicka';
$lang['cancel'] = '&Aring;ngra';
$lang['info_mailer'] = 'Mailmetod att anv&auml;nda (sendmail, SMTP, mail). Oftast &auml;r SMTP mest p&aring;litlig.';
$lang['info_host'] = 'Namn p&aring; SMTP-server (g&auml;ller bara n&auml;r SMTP anv&auml;nds som mailmetod)';
$lang['info_port'] = 'Portnummer f&ouml;r SMTP (vanligen 25) (g&auml;ller bara n&auml;r SMTP anv&auml;nds som mailmetod)';
$lang['info_from'] = 'Adress som anv&auml;nds som avs&auml;ndare i alla epostmeddelanden';
$lang['info_fromuser'] = 'Namn p&aring; t.ex. person eller f&ouml;retag f&ouml;r alla skickade epostmeddelanden';
$lang['info_sendmail'] = 'Komplett s&ouml;kv&auml;g till d&auml;r dina sendmail-filer finns (g&auml;ller bara n&auml;r sendmail anv&auml;nds som mailmetod)';
$lang['info_timeout'] = 'Antal sekunder i en SMTP-konversation innan ett felmeddelande uppkommer (g&auml;ller n&auml;r SMTP anv&auml;nds som mailmetod)';
$lang['info_smtpauth'] = 'Kr&auml;ver din SMTP-server autentisering (g&auml;ller bara n&auml;r SMTP anv&auml;nds som mailmetod)?';
$lang['info_username'] = 'Anv&auml;ndarnamn vid SMTP-autentisering (g&auml;ller bara n&auml;r SMTP anv&auml;nds som mailmetod och SMTP-autentisering &auml;r valt)';
$lang['info_password'] = 'L&ouml;senord vid SMTP-autentisering (g&auml;ller bara n&auml;r SMTP anv&auml;nds som mailmetod och SMTP-autentisering &auml;r valt)';
$lang['friendlyname'] = 'CMS Mailer';
$lang['postinstall'] = 'CMSMailer-modulen har installerats';
$lang['postuninstall'] = 'CMSMailer-modulen &auml;r avinstallerad... ledsen att se dig g&aring;...';
$lang['uninstalled'] = 'Modulen avinstallerad.';
$lang['installed'] = 'Modulversion %s installerad.';
$lang['accessdenied'] = '&Aring;tkomst nekad. V&auml;nligen kontrollera dina r&auml;ttigheter.';
$lang['error'] = 'Fel!';
$lang['upgraded'] = 'Modulen uppgraderad till version %s.';
$lang['title_mod_prefs'] = 'Modulinst&auml;llningar';
$lang['title_mod_admin'] = 'Moduladministration';
$lang['title_admin_panel'] = 'CMSMailer';
$lang['moddescription'] = 'Detta &auml;r ett enkelt h&ouml;lje (wrapper) runt PHPMailer och har en likadan API (funktion f&ouml;r funktion) och ett enkelt gr&auml;nssnitt f&ouml;r n&aring;gra standardinst&auml;llningar. ';
$lang['welcome_text'] = '<p>V&auml;lkommen till administrationen f&ouml;r CMSMailer-modulen';
$lang['changelog'] = '<ul>
<li>Version 1.73.1. Oktober, 2005. F&ouml;rsta utg&aring;van.</li>
<li>Version 1.73.2. Oktober, 2005. Mindre buggfixar f&ouml;r administrationen. Rullgardinsmenyn representerade inte det valda v&auml;rdet fr&aring;n inst&auml;llningarna i databasen.</li>
<li>Version 1.73.3. Oktober, 2005. Mindre buggfix f&ouml;r att skicka HTML-epost</li>
<li>Version 1.73.4. November, 2005. Formul&auml;rf&auml;lten i inst&auml;llningarna &auml;r st&ouml;rre, fixade ett problem med fr&aring;n-anv&auml;ndaren, och called reset within the constructor</li>
<li>Version 1.73.5. November, 2005. La till formul&auml;rf&auml;lt och funktion f&ouml;r SMTP-autentisering.</li>
<li>Version 1.73.6. December, 2005. Standardmailmetoden &auml;r SMTP vid installation, och f&ouml;rb&auml;ttrad dokumentation, och bilagor och adresser mm rensas vid &aring;terst&auml;llning.</li>
<li>Version 1.73.7. Januari, 2006. &Ouml;kade f&auml;ltl&auml;ngden f&ouml;r de flesta f&auml;lt.</li>
<li>Version 1.73.8. Januari, 2006. &Auml;ndrade inst&auml;llningspanelen, s&aring; att den &auml;r mer beskrivande.</li>
<li>Version 1.73.9. January, 2006. Added test email capability, and confirmation on each button (except cancel)</li>
<li>Version 1.73.10. August, 2006. Modified to use lazy loading to minimize memory footprint when CMSMailer is not being used.</li>
<li>Version 1.73.13. January, 2008.  Added more permissions checks.</li>
</ul>';
$lang['help'] = '<h3>What Does This Do?</h3>
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
<p>This module has been released under the <a href=&#039;http://www.gnu.org/licenses/licenses.html#GPL&#039;>GNU Public License</a>. You must agree to this license before using the module.</p>';
$lang['utma'] = '156861353.1844854326557378000.1220712016.1233479596.1233505303.313';
$lang['utmz'] = '156861353.1233505303.313.30.utmccn=(referral)|utmcsr=forum.cmsmadesimple.org|utmcct=/|utmcmd=referral';
$lang['utmc'] = '156861353';
$lang['utmb'] = '156861353';
?>