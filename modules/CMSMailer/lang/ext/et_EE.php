<?php
$lang['info_cmsmailer'] = 'Seda moodulit kasutab hulk teisi mooduleid emailide saatmise h&otilde;lbustamiseks. See peab olema &otilde;igesti konfigureeritud vastavalt sinu teenusepakkuja n&otilde;uetele. Palun kasuta oma teenusepakkujalt saadud informatsiooni, et neid seadeid muuta. Kui sa endiselt ei saa testi s&otilde;numit korralikult saatma, tuleks teil p&ouml;&ouml;rduda teenusepakkuja poole abi saamiseks.';
$lang['charset'] = 'T&auml;hem&auml;rgi seeria (Character Set)';
$lang['sendtestmailconfirm'] = 'See saadab antud aadressile testmeili. Kui saatmine &otilde;nnestub, tuuakse sind siia lehele tagasi.  Kas soovid j&auml;tkata?';
$lang['settingsconfirm'] = 'Soovid sa antud seaded salvestada?';
$lang['testsubject'] = 'CMSMailer&#039;i Tests&otilde;num';
$lang['testbody'] = 'See s&otilde;num on m&otilde;eldud ainult CMSMailer&#039;i seadete kontrollimiseks.
Kui sa selle k&auml;tte said, siis k&otilde;ik t&ouml;&ouml;tab.';
$lang['error_notestaddress'] = 'Viga: Testmaili aadressi pole antud';
$lang['prompt_testaddress'] = 'Testmeili aadress';
$lang['sendtest'] = 'Saadad Testmeil';
$lang['password'] = 'Parool';
$lang['username'] = 'Kasutajanimi';
$lang['smtpauth'] = 'SMTP Autentimine';
$lang['mailer'] = 'Meilimismeetod';
$lang['host'] = 'SMTP hosti nimi<br/><i>(v&otilde;i IP aaddress)</i>';
$lang['port'] = 'SMTP serveri port';
$lang['from'] = 'Aadressilt';
$lang['fromuser'] = 'Kelle nimel';
$lang['sendmail'] = 'Sendmail&#039;i asukoht';
$lang['timeout'] = 'SMTP taimaut';
$lang['submit'] = 'Saada';
$lang['cancel'] = 'T&uuml;hista';
$lang['info_mailer'] = 'Meilimise meetod (sendmail, smtp, mail).  Tavaliselt on smtp k&otilde;ige kindlam valik.';
$lang['info_host'] = 'SMTP hosti nimi (kehtib ainult smtp meilimismeetodi puhul)';
$lang['info_port'] = 'SMTP pordi number (tavaliselt 25) (kehtib ainult smtp meilimismeetodi puhul)';
$lang['info_from'] = 'Aadress, mida kasutatakse saatjana k&otilde;ikides meilides';
$lang['info_fromuser'] = 'Kasutajanimi, mida kasutatakse k&otilde;ikide meilide saatmisel';
$lang['info_sendmail'] = 'T&auml;ispikk tee sinu sendmail k&auml;ivitusele (kehtib ainult sendmail meilimismeetodi puhul)';
$lang['info_timeout'] = 'Sekundite arv SMTP vestluses, enne kui esineb viga (kehtib ainult sendmail meilimismeetodi puhul)';
$lang['info_smtpauth'] = 'Kas sinu SMTP n&otilde;uab autentimist? (kehtib ainult smtp meilimismeetodi puhul)';
$lang['info_username'] = 'SMTP autentimise kasutajanimi (kehtib ainult smtp meilimismeetodi puhul, kui smtp autentimine on valitud)';
$lang['info_password'] = 'SMTP autentimise parool (kehtib ainult smtp meilimismeetodi puhul, kui smtp autentimine on valitud)';
$lang['friendlyname'] = 'CMSMailer Moodul';
$lang['postinstall'] = 'CMSMailer moodul on edukalt installeeritud';
$lang['postuninstall'] = 'CMSMailer moodul eemaldatud.';
$lang['uninstalled'] = 'Moodul eemaldatud.';
$lang['installed'] = 'Mooduli versioon %s installeeritud.';
$lang['accessdenied'] = 'Ligip&auml;&auml;s keelatud. Palun kontrolli oma &otilde;igusi.';
$lang['error'] = 'Viga!';
$lang['upgraded'] = 'Moodul uuendatud versioonile %s.';
$lang['title_mod_prefs'] = 'Mooduli Eelistused';
$lang['title_mod_admin'] = 'Mooduli Administraatoripaneel';
$lang['title_admin_panel'] = 'CMSMailer Moodul';
$lang['moddescription'] = 'See on lihtne PHPMaileri pakend, sel on ekvavilentne API (function for function) ja lihtne kasutajaliides m&otilde;ningate seadetete muutmiseks.';
$lang['welcome_text'] = '<p>Tere tulemast CMSMailer mooduli adminni sektsiooni';
$lang['changelog'] = '<ul>
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
<p>This module has been released under the <a href=&#039;http://www.gnu.org/licenses/licenses.html#GPL&#039;>GNU Public License</a>. You must agree to this license before using the module.</p>
';
$lang['utma'] = '156861353.3215545266732019000.1241091787.1255008902.1255072702.158';
$lang['utmz'] = '156861353.1254469083.145.28.utmccn=(organic)|utmcsr=google|utmctr=cmsms requirements|utmcmd=organic';
$lang['qca'] = '1240683615-58409973-79915303';
$lang['utmb'] = '156861353.1.10.1255072702';
$lang['utmc'] = '156861353';
?>