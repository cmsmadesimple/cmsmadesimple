<?php
$lang['sendtestmailconfirm'] = 'T&auml;m&auml; l&auml;hett&auml;&auml; testiviestin m&auml;&auml;ritt&auml;m&auml;&auml;si osoitteeseen. Mik&auml;li l&auml;hett&auml;minen onnistuu, ohjaudut takaisin t&auml;lle sivulle. Haluatko jatkaa?';
$lang['settingsconfirm'] = 'Tallennatko nykyiset asetukset CMSMailerin asetuksiksi?';
$lang['testsubject'] = 'CMSMailer-testiviesti';
$lang['testbody'] = 'T&auml;m&auml;n viestin tarkoitus on varmistaa CMSMailer-moduulin asetusten oikeellisuus.
Jos saat t&auml;m&auml;n viestin, niin kaikki on toimii oikein.';
$lang['error_notestaddress'] = 'Virhe: Testiosoitetta ei ole annettu';
$lang['prompt_testaddress'] = 'Testiviestin email-osoite';
$lang['sendtest'] = 'Testiviestin sis&auml;lt&ouml;';
$lang['password'] = 'Salasana';
$lang['username'] = 'K&auml;ytt&auml;j&auml;nimi';
$lang['smtpauth'] = 'SMTP-autentikointi';
$lang['mailer'] = 'Postitusmetodi';
$lang['host'] = 'SMTP-palvelimen nimi<br/><i>(tai IP-osoite)</i>';
$lang['port'] = 'SMTP-palvelimen portti';
$lang['from'] = 'L&auml;hett&auml;j&auml;n osoite';
$lang['fromuser'] = 'L&auml;hett&auml;j&auml;n nimi';
$lang['sendmail'] = 'Sendmail-sijainti';
$lang['timeout'] = 'SMTP-aikakatkaisu';
$lang['submit'] = 'L&auml;het&auml;';
$lang['cancel'] = 'Peruuta';
$lang['info_mailer'] = 'Postitusmetodi (sendmail, smtp, mail).  Yleens&auml; SMTP on luotettavin.';
$lang['info_host'] = 'SMTP-palvelimen nimi (vain SMTP-postitusmetodissa)';
$lang['info_port'] = 'SMTP-portin numero (yleens&auml; 25) (vain SMTP-postitusmetodissa)';
$lang['info_from'] = 'L&auml;hett&auml;j&auml;n osoite kaikissa viesteiss&auml;';
$lang['info_fromuser'] = 'L&auml;hett&auml;j&auml;n nimi kaikissa viesteiss&auml;';
$lang['info_sendmail'] = 'T&auml;ydellinen polku Sendmail-ohjelmaan (vain sendmail-postitusmetodissa)';
$lang['info_timeout'] = 'Aika sekunteina ennen kuin tapahtuu virhe SMTP-keskustelussa (SMTP-postitusmetodissa)';
$lang['info_smtpauth'] = 'Vaatiiko SMTP-palvelin autentikoinnin (vain SMTP-postitusmetodissa)';
$lang['info_username'] = 'K&auml;ytt&auml;j&auml;nimi SMTP-autentikointiin (vain SMTP-postitusmetodissa kun autentikointi on valittuna)';
$lang['info_password'] = 'Salasana SMTP-autentikointiin (vain SMTP postitusmetodissa kun autentikointi on valittuna)';
$lang['friendlyname'] = 'CMSMailer-moduuli';
$lang['postinstall'] = 'CMSMailer-moduuli on asennettu onnistuneesti';
$lang['postuninstall'] = 'CMSMailer-moduuli poistettu';
$lang['uninstalled'] = 'Moduuli poistettu.';
$lang['installed'] = 'Moduuliversio %s asennettu.';
$lang['accessdenied'] = 'P&auml;&auml;sy estetty. Tarkasta oikeudet.';
$lang['error'] = 'Virhe!';
$lang['upgraded'] = 'Moduuli p&auml;ivitetty versioon %s.';
$lang['title_mod_prefs'] = 'Moduulin asetukset';
$lang['title_mod_admin'] = 'Moduulin hallintapaneli';
$lang['title_admin_panel'] = 'CMSMailer-moduuli';
$lang['moddescription'] = 'T&auml;m&auml; on yksinkertainen k&auml;&auml;reohjelma PHPMailerin ymp&auml;rille. T&auml;ss&auml; on samanlainen API (funktiokohtaisesti) ja yksinkertainen k&auml;ytt&ouml;liittym&auml; joidenkin oletusvalintojen asettamiseksi.';
$lang['welcome_text'] = '<p>Tervetuloa CMSMailer-moduulin hallintaosioon';
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
</ul>';
$lang['help'] = '<h3>Mit&auml; t&auml;m&auml; moduuli tekee?</h3>
<p>T&auml;ll&auml; moduulilla ei ole ollenkaan perusk&auml;ytt&auml;j&auml;n k&auml;ytt&ouml;liittym&auml;&auml;. T&auml;m&auml; on suunniteltu integroitavaksi muihin moduuleihin s&auml;hk&ouml;postiominaisuuksien lis&auml;&auml;miseksi.</p>
<h3>Kuinka sit&auml; k&auml;ytet&auml;&auml;n</h3>
<p>T&auml;m&auml; moduuli toimii yksinkertaisena k&auml;&auml;reen&auml; phpmailerin muuttujille ja metodeille. Moduuli on tarkoitettu toisten moduulikehitt&auml;jien k&auml;ytett&auml;v&auml;ksi. Alla on esimerkki ja lyhyt API-kuvaus. Moduulin mukana toimitetaan PHPMailer-dokumentaatio, josta saa lis&auml;tietoja.</p>
<h3>Esimerkki</h3>
<pre>
  $cmsmailer = $this->GetModuleInstance(&#039;CMSMailer&#039;);
  $cmsmailer->AddAddress(&#039;calguy1000@hotmail.com&#039;,&#039;calguy&#039;);
  $cmsmailer->SetBody(&#039;<h1>T&auml;m&auml; on testiviesti<h1>&#039;);
  $cmsmailer->IsHTML(true);
  $cmsmailer->SetSubject(&#039;Testiviesti&#039;);
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
<h3>Tuki</h3>
<p>T&auml;m&auml;n moduulin mukana ei tule kaupallista tukea. Muutamista paikoista voit kuitenkin saada apua:</p>
<ul>
<li>Uusin versio, FAQ ja bugiraportin j&auml;tt&auml;minen sek&auml; kaupallisen tuen ostomahdollisuus ovat calguyn kotisivulla osoitteessa <a href=&amp;quot;http://techcom.dyndns.org&amp;quot;>techcom.dyndns.org</a>.</li>
<li>Keskustelua moduulista on <a href=&amp;quot;http://forum.cmsmadesimple.org&amp;quot;>CMS Made Simple -foorumilla</a>.</li>
<li>Moduulin tekij&auml;, calguy1000 on usein tavoitettavissa <a href=&amp;quot;irc://irc.freenode.net/#cms&amp;quot;>CMS IRC-kanavalla</a>.</li>
<li>Saatat my&ouml;s saada jonkin verran apua moduulin tekij&auml;lt&auml; s&auml;hk&ouml;postitse.</li>  
</ul>
<p>GPL:n mukaisesti, t&auml;m&auml; ohjelma toimitetaan sellaisena kuin se on (as-is). T&auml;ydellinen vastuuvapauslauseke on lisenssiss&auml;.</p>

<h3>Copyright ja lisenssi</h3>
<p>Copyright &copy; 2005, Robert Campbell <a href=&amp;quot;mailto:calguy1000@hotmail.com&amp;quot;><calguy1000@hotmail.com></a>. All Rights Are Reserved.</p>
<p>T&auml;m&auml; moduuli on julkaistu <a href=&amp;quot;http://www.gnu.org/licenses/licenses.html#GPL&amp;quot;>GNU Public License</a> -lisenssill&auml;. Lisenssin ehdot on hyv&auml;ksytt&auml;v&auml; ennen moduulin k&auml;ytt&ouml;&auml;.</p>
';
?>