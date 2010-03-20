<?php
$lang['info_cmsmailer'] = 'Dieses Modul wird von einigen anderen Modulen verwendet, um den Emailversand zu erleichtern. Es muss daher entsprechend den Vorgaben Ihres Hosters konfiguriert werden.  Wenn Sie trotzdem keine Testnachricht versenden k&ouml;nnen, sollten Sie Ihren Hoster um Hilfe bitten.';
$lang['charset'] = 'Zeichensatz';
$lang['sendtestmailconfirm'] = 'Hiermit wird eine Testnachricht an die festgelegte Adresse versandt. War der Versand erfolgreich, kehren Sie zu dieser Seite zur&uuml;ck. Wollen Sie dies wirklich?';
$lang['settingsconfirm'] = 'Sollen die CMSMailer-Einstellungen gespeichert werden?';
$lang['testsubject'] = 'Testnachricht des CMSMailer-Moduls';
$lang['testbody'] = 'Mit dieser Nachricht werden die Einstellungen des CMSMailer-Moduls getestet. Wenn Sie diese Email erhalten haben, sind die Einstellungen korrekt.';
$lang['error_notestaddress'] = 'Fehler: Die Testadresse wurde nicht festgelegt!';
$lang['prompt_testaddress'] = 'Test-Emailadresse';
$lang['sendtest'] = 'Testnachricht senden';
$lang['password'] = 'Passwort';
$lang['username'] = 'Benutzername';
$lang['smtpauth'] = 'SMTP Authentifizierung';
$lang['mailer'] = 'Mailmethode';
$lang['host'] = 'SMTP-Hostname<br /><i>(oder IP-Adresse)</i>';
$lang['port'] = 'Port des SMTP-Servers';
$lang['from'] = '&quot;Von&quot;-Adresse';
$lang['fromuser'] = '&quot;Von&quot;-Username';
$lang['sendmail'] = 'Sendmail-Pfad';
$lang['timeout'] = 'SMTP-Timeout';
$lang['submit'] = 'Speichern';
$lang['cancel'] = 'Abbrechen';
$lang['info_mailer'] = 'Verwendete Mailmethode (sendmail, smtp, mail). SMTP ist die verl&auml;sslichste Methode.';
$lang['info_host'] = 'SMTP-Hostname (nur f&uuml;r die SMTP-Mailmethode g&uuml;ltig)';
$lang['info_port'] = 'SMTP-Portnummer (&uuml;blicherweise 25) (nur f&uuml;r die SMTP-Mailmethode g&uuml;ltig)';
$lang['info_from'] = 'Email-Adresse, die als Absender f&uuml;r alle Emails verwendet werden soll.<br/><strong>Hinweis</strong> Diese Emailadresse muss genau entsprechend den Vorgaben Ihres Hosters eingestellt werden. Anderenfalls kann es beim Versand von Emails zu Schwierigkeiten kommen.<br/>Wenn Sie nicht die richtigen Werte f&uuml;r diese Einstellungen kennen, sollten Sie Ihren Hoster fragen.';
$lang['info_fromuser'] = 'Realname, der als Absender f&uuml;r alle Emails verwendet werden soll';
$lang['info_sendmail'] = 'Der komplette Pfad zu Ihrem sendmail-Programm (nur f&uuml;r die SMTP-Mailmethode g&uuml;ltig)';
$lang['info_timeout'] = 'Dauer der SMTP-Konversation in Sekunden, bevor eine Fehlermeldung ausgegeben wird (nur f&uuml;r die SMTP-Mailmethode g&uuml;ltig)';
$lang['info_smtpauth'] = 'Ben&ouml;tigt Ihr SMTP-Host eine Authentifizierung? (nur f&uuml;r die SMTP-Mailmethode g&uuml;ltig)';
$lang['info_username'] = 'SMTP-Authentifizierungs-Benutzername (nur f&uuml;r die SMTP-Mailmethode g&uuml;ltig, wenn SMTP-Authentifizierung gew&auml;hlt wurde)';
$lang['info_password'] = 'SMTP-Authentifizierungs-Passwort (nur f&uuml;r die SMTP-Mailmethode g&uuml;ltig, wenn SMTP-Authentifizierung gew&auml;hlt wurde)';
$lang['friendlyname'] = 'CMSMailer ';
$lang['postinstall'] = 'Das CMSMailer-Modul wurde erfolgreich installiert.';
$lang['postuninstall'] = 'Das CMSMailer-Modul wurde deinstalliert ... schade, Sie gehen zu sehen!';
$lang['uninstalled'] = 'Das Modul wurde deinstalliert.';
$lang['installed'] = 'Das Modul wurde in der Version %s installiert.';
$lang['accessdenied'] = 'Zugriff verweigert. Bitte pr&uuml;fen Sie Ihre Berechtigungen.';
$lang['error'] = 'Fehler!';
$lang['upgraded'] = 'Das Modul wurde auf Version %s aktualisiert.';
$lang['title_mod_prefs'] = 'Moduleinstellungen';
$lang['title_mod_admin'] = 'Modul-Administration';
$lang['title_admin_panel'] = 'CMSMailer-Modul';
$lang['moddescription'] = 'Dies ist ein einfacher Wrapper f&uuml;r den PHPMailer; er hat die gleiche API (Funktion f&uuml;r Funktion) und ein einfaches Interface f&uuml;r ein paar Einstellungen.';
$lang['welcome_text'] = '<p>Willkommen in der Administration des CMSMailer-Moduls';
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
$lang['help'] = '<h3>Was macht dieses Modul?</h3>
<p>Dieses Modul hat keine direkte Funktionalit&auml;t f&uuml;r den Webseitenbesucher. Es stellt anderen Modulen nur die Ressourcen zum Versand von Emails bereit.</p>
<h3>Wie wird es eingesetzt ?</h3>
<p>Dieses Modul ist ein einfacher Wrapper f&uuml;r alle Methoden und Variablen von <a rel="external" href="http://phpmailer.sourceforge.net/">PHPmailer</a>. Es wurde zur Unterst&uuml;tzung f&uuml;r die Modul-Programmierung konzipiert. Im folgenden finden Sie ein Anwendungsbeispiel und eine kurze API-Referenz. Weitere Informationen finden Sie in der Dokumentation zu PHPmailer.</p>
<h3>Ein Beispiel</h3>
<pre>
  $cmsmailer = $this->GetModuleInstance(&#039;CMSMailer&#039;);
  $cmsmailer->AddAddress(&#039;calguy1000@hotmail.com&#039;,&#039;calguy&#039;);
  $cmsmailer->SetBody(&#039;<h4>Das ist eine Testnachricht</h4>&#039;);
  $cmsmailer->IsHTML(true);
  $cmsmailer->SetSubject(&#039;Testnachricht&#039;);
  $cmsmailer->Send();
</pre>
<h3>API</h3>
<ul>
<li><p><tt>void reset()</tt></p>
<p>Setzt das Objekt auf die in der Administration definierten Werte zur&uuml;ck</p>
</li>
<li><p><tt>string GetAltBody()</tt></p>
<p>Gibt den alternativen Email-Body zur&uuml;ck</p>
</li>
<li><p><tt>void SetAltBody( $string )</tt></p>
<p>Definiert den alternativen Email-Body</p>
</li>
<li><p><tt>string GetBody()</tt></p>
<p>Gibt den prim&auml;ren Email-Body zur&uuml;ck</p>
</li>
<li><p><tt>void SetBody( $string )</tt></p>
<p>Definiert den prim&auml;ren Email-Body</p>
</li>
<li><p><tt>string GetCharSet()</tt></p>
<p>Standard: iso-8859-1</p>
<p>Gibt den verwendeten Zeichensatz der Email zur&uuml;ck</p>
</li>
<li><p><tt>void SetCharSet( $string )</tt></p>
<p>Definiert den verwendeten Zeichensatz der Email</p>
</li>
<li><p><tt>string GetConfirmReadingTo()</tt></p>
<p>Gibt die Email-Adresse zur&uuml;ck, an die die Lesebest&auml;tigung gesandt wird</p>
</li>
<li><p><tt>void SetConfirmReadingTo( $address )</tt></p>
<p>Definiert oder l&ouml;scht die Email-Adresse f&uuml;r die Lesebest&auml;tigung</p>
</li>
<li><p><tt>string GetContentType()</tt></p>
<p>Standard: text/plain</p>
<p>Gibt den Typ des Inhalts der Email zur&uuml;ck</p>
</li>
<li><p><tt>void SetContentType()</tt></p>
<p>Definiert den Typ des Inhalts der Email</p>
</li>
<li><p><tt>string GetEncoding()</tt></p>
<p>Gibt die Enkodierung der Email zur&uuml;ck</p>
</li>
<li><p><tt>void SetEncoding( $encoding )</tt></p>
<p>Definiert die Enkodierung der Email</p>
<p>M&ouml;gliche Optionen sind: 8bit, 7bit, binary, base64, quoted-printable</p>
</li>
<li><p><tt>string GetErrorInfo()</tt></p>
<p>Gibt eine Fehler-Information zur&uuml;ck</p>
</li>
<li><p><tt>string GetFrom()</tt></p>
<p>Gibt die aktuelle Email-Adresse des Absenders zur&uuml;ck</p>
</li>
<li><p><tt>void SetFrom( $address )</tt></p>
<p>Definiert die Email-Adresse des Absenders</p>
</li>
<li><p><tt>string GetFromName()</tt></p>
<p>Gibt den Namen des Absenders zur&uuml;ck</p>
</li>
<li><p><tt>SetFromName( $name )</tt></p>
<p>Definiert den Namen des Absenders</p>
</li>
<li><p><tt>string GetHelo()</tt></p>
<p>Gibt den HELO-String zur&uuml;ck</p>
</li>
<li><p><tt>SetHelo( $string )</tt></p>
<p>Definiert den HELO-String</p>
<p>Standardwert: $hostname</p>
</li>
<li><p><tt>string GetHost()</tt></p>
<p>Gibt die SMTP-Hosts zur&uuml;ck (getrennt durch Semikolon)</p>
</li>
<li><p><tt>void SetHost( $string )</tt></p>
<p>Definiert die Hosts</p>
</li>
<li><p><tt>string GetHostName()</tt></p>
<p>Gibt den Hostnamen zur&uuml;ck, der f&uuml;r SMTP Helo verwendet wird</p>
</li>
<li><p><tt>void SetHostName( $hostname )</tt></p>
<p>Definiert den Hostnamen, der f&uuml;r SMTP Hello verwendet wird</p>
</li>
<li><p><tt>string GetMailer()</tt></p>
<p>Gibt den verwendeten Mailer zur&uuml;ck</p>
</li>
<li><p><tt>void SetMailer( $mailer )</tt></p>
<p>Gibt den verwendeten Mailer zur&uuml;ck, entweder sendmail, mail, oder smtp</p>
</li>
<li><p><tt>string GetPassword()</tt></p>
<p>Gibt das Passwort f&uuml;r die SMTP-Authentifizierung zur&uuml;ck</p>
</li>
<li><p><tt>void SetPassword( $string )</tt></p>
<p>Definiert das Passwort f&uuml;r die SMTP-Authentifizierung</p>
</li>
<li><p><tt>int GetPort()</tt></p>
<p>Gibt die Portnummer f&uuml;r SMTP-Verbindungen zur&uuml;ck</p>
</li>
<li><p><tt>void SetPort( $int )</tt></p>
<p>Definiert die Portnummer f&uuml;r SMTP-Verbindungen</p>
</li>
<li><p><tt>int GetPriority()</tt></p>
<p>Gibt die Priorit&auml;t der Nachricht zur&uuml;ck</p>
</li>
<li><p><tt>void SetPriority( int )</tt></p>
<p>Definiert die Priorit&auml;t/Dringlichkeit der Nachricht</p>
<p>M&ouml;gliche Werte sind 1 = Hoch, 3 = Normal, 5 = Niedrig</p>
</li>
<li><p><tt>string GetSender()</tt></p>
<p>Gibt den sender-String zur&uuml;ck (return path) </p>
</li>
<li><p><tt>void SetSender( $address )</tt></p>
<p>Definiert den sender-String</p>
</li>
<li><p><tt>string GetSendmail()</tt></p>
<p>Gibt den sendmail-Pfad zur&uuml;ck</p>
</li>
<li><p><tt>void SetSendmail( $path )</tt></p>
<p>Definiert den sendmail-Pfad</p>
</li>
<li><p><tt>bool GetSMTPAuth()</tt></p>
<p>Gibt den aktuellen Wert des SMTP-Authentifizierungs-Flags zur&uuml;ck</p>
</li>
<li><p><tt>SetSMTPAuth( $bool )</tt></p>
<p>Definiert den SMTP-Authentifizierungs-Flag</p>
</li>
<li><p><tt>bool GetSMTPDebug()</tt></p>
<p>Gibt den aktuellen Wert des SMTP-Debug-Flags zur&uuml;ck</p>
</li>
<li><p><tt>void SetSMTPDebug( $bool )</tt></p>
<p>Definiert den SMTP-Debug-Flag</p>
</li>
<li><p><tt>bool GetSMTPKeepAlive()</tt></p>
<p>Gibt den aktuellen Wert des SMTP-Keep-Alive-Flags zur&uuml;ck</p>
</li>
<li><p><tt>SetSMTPKeepAlive( $bool )</tt></p>
<p>Definiert den SMTP-Keep-Alive-Flag</p>
</li>
<li><p><tt>string GetSubject()</tt></p>
<p>Gibt den aktuellen Betreff-String zur&uuml;ck</p>
</li>
<li><p><tt>void SetSubject( $string )</tt></p>
<p>Definiert den aktuellen Betreff-String</p>
</li>
<li><p><tt>int GetTimeout()</tt></p>
<p>Gibt den timeout-Wert zur&uuml;ck</p>
</li>
<li><p><tt>void SetTimeout( $seconds )</tt></p>
<p>Definiert den timeout-Wert</p>
</li>
<li><p><tt>string GetUsername()</tt></p>
<p>Gibt den Usernamen f&uuml;r die SMTP-Authentifizierung zur&uuml;ck</p>
</li>
<li><p><tt>void SetUsername( $string )</tt></p>
<p>Definiert den Usernamen f&uuml;r die SMTP-Authentifizierung</p>
</li>
<li><p><tt>int GetWordWrap()</tt></p>
<p>Gibt den wordwrap-Wert zur&uuml;ck</p>
</li>
<li><p><tt>void SetWordWrap( $int )</tt></p>
<p>Definiert den wordwrap-Wert</p>
</li>
<li><p><tt>AddAddress( $address, $name = &#039;&#039; )</tt></p>
<p>F&uuml;gt eine Zieladresse hinzu</p>
</li>
<li><p><tt>AddAttachment( $path, $name = &#039;&#039;, $encoding = &#039;base64&#039;, $type = &#039;application/octent-stream&#039; )</tt></p>
<p>F&uuml;gt einen Anhang hinzu</p>
</li>
<li><p><tt>AddBCC( $address, $name = &#039;&#039; )</tt></p>
<p>F&uuml;gt eine Zieladresse f&uuml;r eine verdeckte Kopie (BCC) hinzu</p>
</li>
<li><p><tt>AddCC( $address, $name = &#039;&#039; )</tt></p>
<p>F&uuml;gt eine Zieladresse f&uuml;r eine Kopie (CC) hinzu</p>
</li>
<li><p><tt>AddCustomHeader( $txt )</tt></p>
<p>F&uuml;gt der Email einen benutzerdefinierten Header hinzu</p>
</li>
<li><p><tt>AddEmbeddedImage( $path, $cid, $name = &#039;&#039;, $encoding = &#039;base64&#039;, $type = &#039;application/octent-stream&#039; )</tt></p>
<p>F&uuml;gt ein eingebettetes Bild hinzu</p>
</li>
<li><p><tt>AddReplyTo( $address, $name = &#039;&#039; )</tt></p>
<p>F&uuml;gt eine Adresse f&uuml;r die Antwort-Email hinzu</p>
</li>
<li><p><tt>AddStringAttachment( $string, $filename, $encoding = &#039;base64&#039;, $type = &#039;application/octent-stream&#039; )</tt></p>
<p>F&uuml;gt einen Dateianhang hinzu</p>
</li>
<li><p><tt>ClearAddresses()</tt></p>
<p>L&ouml;scht alle Email-Adressen</p>
</li>
<li><p><tt>ClearAllRecipients()</tt></p>
<p>L&ouml;scht alle Empf&auml;nger</p>
</li>
<li><p><tt>ClearAttachments()</tt></p>
<p>L&ouml;scht alle Anh&auml;nge</p>
</li>
<li><p><tt>ClearBCCs()</tt></p>
<p>L&ouml;scht alle BCC-Email-Adressen</p>
</li>
<li><p><tt>ClearCCs()</tt></p>
<p>L&ouml;scht alle CC-Email-Adressen</p>
</li>
<li><p><tt>ClearCustomHeaders()</tt></p>
<p>L&ouml;scht alle benutzerdefinierten Header</p>
</li>
<li><p><tt>ClearReplyto()</tt></p>
<p>L&ouml;scht die Email-Adresse f&uuml;r die Antwort</p>
</li>
<li><p><tt>IsError()</tt></p>
<p>Pr&uuml;ft, ob eine bestimmte Fehlerbedingung erf&uuml;llt ist</p>
</li>
<li><p><tt>bool IsHTML( $bool )</tt></p>
<p>Kennzeichnet die Email als HTML-Email</p>
<p><i>Hinweis:</i> Dies ist zwar eine M&ouml;glichkeit, jedoch sollte bevorzugt eine get- und set-Methode verwendet werden</p>
</li>
<li><p><tt>bool IsMail()</tt></p>
<p>Pr&uuml;ft, ob mail verwendet werden soll</p>
</li>
<li><p><tt>bool IsQmail()</tt></p>
<p>Pr&uuml;ft, ob qmail verwendet werden soll</p>
</li>
<li><p><tt>IsSendmail()</tt></p>
<p>Pr&uuml;ft, ob sendmail verwendet werden soll</p>
</li>
<li><p><tt>IsSMTP()</tt></p>
<p>Pr&uuml;ft, ob smtp verwendet werden soll</p>
</li>
<li><p><tt>Send()</tt></p>
<p>Versendet die aktuell vorbereitete Email</p>
</li>
<li><p><tt>SetLanguage( $lang_type, $lang_path = &#039;&#039; )</tt></p>
<p>Legt die aktuelle Sprache und <em>(optional)</em> den Sprach-Pfad fest</p>
</li>
<li><p><tt>SmtpClose()</tt></p>
<p>Schlie&szlig;t die smtp-Verbindung</p>
</li>
</ul>
<h3>Support</h3>
<p>Dieses Modul beinhaltet keinen kommerziellen Support. Sie k&ouml;nnen jedoch &uuml;ber folgende M&ouml;glichkeiten Hilfe zu dem Modul erhalten:</p>
<ul>
<li>F&uuml;r die letzte Version dieses Moduls, FAQs, dem Versand eines Fehlerreports oder dem Kauf kommerziellen Support besuchen Sie bitte calguys Homepage unter <a href="http://techcom.dyndns.org">techcom.dyndns.org</a>.</li>
<li>Eine weitere Diskussion zu diesem Modul ist auch in den Foren von <a href="http://forum.cmsmadesimple.org">CMS Made Simple</a> zu finden.</li>
<li>Der Autor calguy1000 ist h&auml;ufig im <a href="irc://irc.freenode.net/#cms">CMS IRC Channel</a> zu finden.</li>
<li>Letztlich erreichen Sie den Autor auch &uuml;ber eine direkte Email.</li>
</ul>
<p>Nach der GPL wird diese Software so ver&ouml;ffentlicht, wie sie ist. Bitte lesen Sie den Lizenztext f&uuml;r den vollen Haftungsausschluss.</p>

<h3>Copyright und Lizenz</h3>
<p>Copyright &copy; 2005, Robert Campbell <a href="mailto:calguy1000@hotmail.com"><calguy1000@hotmail.com></a>. Alle Rechte vorbehalten.</p>
<p>Dieses Modul wurde unter der <a href="http://www.gnu.org/licenses/licenses.html#GPL">GNU Public License</a> ver&ouml;ffentlicht. Sie m&uuml;ssen dieser Lizenz zustimmen, bevor Sie das Modul verwenden.</p>
';
$lang['qca'] = 'P0-617342851-1252404665837';
$lang['utma'] = '156861353.343462282.1252405474.1252405474.1252408135.2';
$lang['utmc'] = '156861353';
$lang['utmz'] = '156861353.1252405474.1.1.utmccn=(referral)|utmcsr=forum.cmsmadesimple.org|utmcct=/index.php|utmcmd=referral';
$lang['utmb'] = '156861353';
?>