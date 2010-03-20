<?php
$lang['info_cmsmailer'] = 'Questo modulo &egrave; usato da numerosi altri moduli per facilitare l&#039;invio di emai. Esso deve per&ograve; essere propriamente configurato secondo le richieste del Vostro provider. Si prega di utilizzare queste informazioni per configurare adeguatamente il modulo. Se non arriva il messaggio di test, dovete contattare l&#039;assistenza del Vostro provider.';
$lang['charset'] = 'Insieme di caratteri';
$lang['sendtestmailconfirm'] = 'Questo spedisce un messaggio di test all&#039;indirizzo specificato. Se la spedizione ha successo, dovreste ritornare a questa pagina. Vuoi procedere?';
$lang['settingsconfirm'] = 'Scrivo i valori correnti nella configurazione di CMSMailer?';
$lang['testsubject'] = 'Messaggio di test di CMSMailer';
$lang['testbody'] = 'Questo messaggio &egrave; inteso a verificare solamente la validit&agrave; della configurazione del modulo CMSMailer.
Se ricevete questo messaggio significa che sta funzionando a dovere.';
$lang['error_notestaddress'] = 'Errore: Indirizzo di test non specificato';
$lang['prompt_testaddress'] = 'Indirizzo email di test';
$lang['sendtest'] = 'Spedisci il messaggio di test';
$lang['password'] = 'Password';
$lang['username'] = 'Nome utente';
$lang['smtpauth'] = 'Autenticazione SMTP';
$lang['mailer'] = 'Metodo di spedizione';
$lang['host'] = 'Nome server SMTP<br /><i>(o indirizzo IP)</i>';
$lang['port'] = 'Porta del server SMTP';
$lang['from'] = 'Indirizzo From (Da)';
$lang['fromuser'] = 'Nome utente From (Da)';
$lang['sendmail'] = 'Locazione del Sendmail';
$lang['timeout'] = 'Timeout SMTP';
$lang['submit'] = 'Inoltra';
$lang['cancel'] = 'Cancella';
$lang['info_mailer'] = 'Metodo usato per la spedizione (sendmail, smtp, mail). Di solito smtp &egrave; il pi&ugrave; utilizzato.';
$lang['info_host'] = 'Nome server SMTP (valido solo per il metodo di spedizione smtp)';
$lang['info_port'] = 'Numero porta SMTP (di solito 25) (valido solo per il metodo di spedizione smtp)';
$lang['info_from'] = 'Indirizzo usato per la spedizione di tutte le email.<br/><strong>Nota</strong>, questo indirizzo email deve essere configurato correttamente per il vostro host o Voi avrete difficolt&agrave; a spedire email.<br/>Se Voi non conoscete il valore corretto per questa configurazione, Voi dovreste contattare il vostro host.';
$lang['info_fromuser'] = 'Nome reale per la spedizione di tutte le email';
$lang['info_sendmail'] = 'Il percorso completo del programma sendmail (valido solo per il metodo di spedizione smtp)';
$lang['info_timeout'] = 'Il numero di secondi nella conversazione SMTP prima che accada un errore (valido solo per il metodo di spedizione smtp)';
$lang['info_smtpauth'] = 'Se il server smtp richiede autenticazione (valido solo per il metodo di spedizione smtp)';
$lang['info_username'] = 'Nome utente dell&#039;autenticazione SMTP (valido solo per il metodo di spedizione smtp, quando &egrave; selezionata l&#039;autenticazione smtp)';
$lang['info_password'] = 'Password dell&#039;autenticazione SMTP (valido solo per il metodo di spedizione smtp, quando &egrave; selezionata l&#039;autenticazione smtp)';
$lang['friendlyname'] = 'CMSMailer';
$lang['postinstall'] = 'Il modulo CMSMailer &egrave; stato installato con successo';
$lang['postuninstall'] = 'Disinstallazione del Modulo CMSMailer ...';
$lang['uninstalled'] = 'Modulo disinstallato.';
$lang['installed'] = 'Versione del modulo %s installata.';
$lang['accessdenied'] = 'Accesso negato. Si prega di controllare i permessi.';
$lang['error'] = 'Errore!';
$lang['upgraded'] = 'Modulo aggiornato alla versione %s.';
$lang['title_mod_prefs'] = 'Preferenze modulo';
$lang['title_mod_admin'] = 'Pannello di amministrazione del modulo';
$lang['title_admin_panel'] = 'Modulo CMSMailer';
$lang['moddescription'] = 'Questo &egrave; un semplice wrapper per PHPMailer, ha una equivalente API (funzione di funzione) ed una semplice interfaccia per alcuni valori predefiniti.';
$lang['welcome_text'] = '<p>Benvenuto nella sezione di amministrazione del modulo CMSMailer';
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
$lang['help'] = '<h3>Che cosa fa?</h3>
<p>Questo modulo non provvede nessuna funzionalit&agrave; utente. E&#039; disegnato per essere integrato in altri moduli per provvedere la capacit&agrave; di spedizione emails.</p>
<h3>Come usarlo</h3>
<p>Questo modulo provvede un semplice wrapper per tutti i metodi e le variabili di phpmailer. E&#039; disegnato per essere usato da altri sviluppatori di moduli, sotto viene riportato un esempio ed un breve riferimento di API. Si prega, per pi&ugrave; informazioni, di leggere la documentazione di PHPMailer inclusa.</p>
<h3>Un esempio</h3>
<pre>
  $cmsmailer = $this->GetModuleInstance(&#039;CMSMailer&#039;);
  $cmsmailer->AddAddress(&#039;calguy1000@hotmail.com&#039;,&#039;calguy&#039;);
  $cmsmailer->SetBody(&#039;<h1>This is a test message<h1>&#039;);
  $cmsmailer->IsHTML(true);
  $cmsmailer->SetSubject(&#039;Test message&#039;);
  $cmsmailer->Send();
</pre>
<h3>API</h3>
<ul>
<li><p><b>void reset()</b></p>
<p>Resetta l&#039;oggetto ai valori specificati nel pannello di amministrazione</p>
</li>
<li><p><b>string GetAltBody()</b></p>
<p>Restituisce il corpo alternativo della mail</p>
</li>
<li><p><b>void SetAltBody( $string )</b></p>
<p>Imposta il corpo alternativo della email</p>
</li>
<li><p><b>string GetBody()</b></p>
<p>Restituisce il corpo principale della mail</p>
</li>
<li><p><b>void SetBody( $string )</b></p>
<p>Imposta il corpo principale della mail</p>
</li>
<li><p><b>string GetCharSet()</b></p>
<p>Default: iso-8859-1</p>
<p>Restituisce il set di caratteri del mailer</p>
</li>
<li><p><b>void SetCharSet( $string )</b></p>
<p>Imposta il set di caratteri del mailer</p>
</li>
<li><p><b>string GetConfirmReadingTo()</b></p>
<p>Restituisce il flag di conferma di lettura della mail</p>
</li>
<li><p><b>void SetConfirmReadingTo( $address )</b></p>
<p>Imposta o annulla l&#039;indirizzo per la conferma di lettura</p>
</li>
<li><p><b>string GetContentType()</b></p>
<p>Default: text/plain</p>
<p>Restituisce il tipo di contenuto (content type)</p>
</li>
<li><p><b>void SetContentType()</b></p>
<p>Imposta il tipo di contenuto (content type)</p>
</li>
<li><p><b>string GetEncoding()</b></p>
<p>Restituisce la codifica</p>
</li>
<li><p><b>void SetEncoding( $encoding )</b></p>
<p>Imposta la codifica</p>
<p>Le opzioni sono: 8bit, 7bit, binary, base64, quoted-printable</p>
</li>
<li><p><b>string GetErrorInfo()</b></p>
<p>Restituisce qualsiasi informazione di errore</p>
</li>
<li><p><b>string GetFrom()</b></p>
<p>Restituisce l&#039;attuale indirizzo di origine</p>
</li>
<li><p><b>void SetFrom( $address )</b></p>
<p>Imposta l&#039;indirizzo di origine</p>
</li>
<li><p><b>string GetFromName()</b></p>
<p>Restituisce l&#039;attuale nome di origine</p>
</li>
<li><p><b>SetFromName( $name )</b></p>
<p>Imposta l&#039;attuale nome di origine</p>
</li>
<li><p><b>string GetHelo()</b></p>
<p>Restituisce la stringa di HELO</p>
</li>
<li><p><b>SetHelo( $string )</b></p>
<p>Imposta la stringa di HELO</p>
<p>Default value: $hostname</p>
</li>
<li><p><b>string GetHost()</b></p>
<p>Restituisce gli host SMTP separati da un punto e virgola</p>
</li>
<li><p><b>void SetHost( $string )</b></p>
<p>Imposta l&#039;host</p>
</li>
<li><p><b>string GetHostName()</b></p>
<p>Restituisce il nome host usato per per l&#039;Helo SMTP</p>
</li>
<li><p><b>void SetHostName( $hostname )</b></p>
<p>Imposta il nome host usato per l&#039;Helo SMTP</p>
</li>
<li><p><b>string GetMailer()</b></p>
<p>Restituisce il mailer</p>
</li>
<li><p><b>void SetMailer( $mailer )</b></p>
<p>Imposta il mailer o il sendmail, mail o smtp</p>
</li>
<li><p><b>string GetPassword()</b></p>
<p>Restituisce la password per l&#039;autenticazione smtp</p>
</li>
<li><p><b>void SetPassword( $string )</b></p>
<p>Imposta la password per l&#039;autenticazione smtp</p>
</li>
<li><p><b>int GetPort()</b></p>
<p>Restituisce il numero di porta per smtp</p>
</li>
<li><p><b>void SetPort( $int )</b></p>
<p>Imposta la porta per le connessioni smtp</p>
</li>
<li><p><b>int GetPriority()</b></p>
<p>Restituisce la priorit&agrave; del messaggio</p>
</li>
<li><p><b>void SetPriority( int )</b></p>
<p>Imposta la priorit&agrave; del messaggio</p>
<p>I valori sono 1=Alta, 3 = Normale, 5 = Bassa</p>
</li>
<li><p><b>string GetSender()</b></p>
<p>Restituisce la stringa del mittente mail (percorso di ritorno)</p>
</li>
<li><p><b>void SetSender( $address )</b></p>
<p>Imposta la stringa del mittente</p>
</li>
<li><p><b>string GetSendmail()</b></p>
<p>Restituisce il percorso del sendmail</p>
</li>
<li><p><b>void SetSendmail( $path )</b></p>
<p>Imposta il percorso del sendmail</p>
</li>
<li><p><b>bool GetSMTPAuth()</b></p>
<p>Restituisce il valore attuale del flag di autenticazione smtp</p>
</li>
<li><p><b>SetSMTPAuth( $bool )</b></p>
<p>Imposta il flag di autenticazione smtp</p>
</li>
<li><p><b>bool GetSMTPDebug()</b></p>
<p>Restituisce il valore del flag di debug SMTP</p>
</li>
<li><p><b>void SetSMTPDebug( $bool )</b></p>
<p>Imposta il flag di debug SMTP</p>
</li>
<li><p><b>bool GetSMTPKeepAlive()</b></p>
<p>Restituisce il valore del flag keep alive SMTP</p>
</li>
<li><p><b>SetSMTPKeepAlive( $bool )</b></p>
<p>Imposta il flag keep alive smtp</p>
</li>
<li><p><b>string GetSubject()</b></p>
<p>Restituisce l&#039;attuale stringa dell&#039;oggetto mail</p>
</li>
<li><p><b>void SetSubject( $string )</b></p>
<p>Imposta la stringa oggetto della mail</p>
</li>
<li><p><b>int GetTimeout()</b></p>
<p>Restituisce il valore di timeout</p>
</li>
<li><p><b>void SetTimeout( $seconds )</b></p>
<p>Imposta il valore di timeout</p>
</li>
<li><p><b>string GetUsername()</b></p>
<p>Restituisce il nome utente per l&#039;autenticazione smtp</p>
</li>
<li><p><b>void SetUsername( $string )</b></p>
<p>Imposta il nome utente per l&#039;autenticazione smtp</p>
</li>
<li><p><b>int GetWordWrap()</b></p>
<p>Restituisce il valore dell&#039;a capo automatico</p>
</li>
<li><p><b>void SetWordWrap( $int )</b></p>
<p>Imposta il valore dell&#039;a capo automatico</p>
</li>
<li><p><b>AddAddress( $address, $name = &#039;&#039; )</b></p>
<p>Aggiunge un indirizzo di destinazione</p>
</li>
<li><p><b>AddAttachment( $path, $name = &#039;&#039;, $encoding = &#039;base64&#039;, $type = &#039;application/octent-stream&#039; )</b></p>
<p>Aggiunge un allegato</p>
</li>
<li><p><b>AddBCC( $address, $name = &#039;&#039; )</b></p>
<p>Aggiunge un indirizzo di destinazione nel campo BCC</p>
</li>
<li><p><b>AddCC( $address, $name = &#039;&#039; )</b></p>
<p>Aggiunge un indirizzo di detinazione nel campo CC</p>
</li>
<li><p><b>AddCustomHeader( $txt )</b></p>
<p>Aggiunge un header personalizzato alla mail</p>
</li>
<li><p><b>AddEmbeddedImage( $path, $cid, $name = &#039;&#039;, $encoding = &#039;base64&#039;, $type = &#039;application/octent-stream&#039; )</b></p>
<p>Aggiunge un immagine incorporata</p>
</li>
<li><p><b>AddReplyTo( $address, $name = &#039;&#039; )</b></p>
<p>Aggiunge un indirizzo per le risposte</p>
</li>
<li><p><b>AddStringAttachment( $string, $filename, $encoding = &#039;base64&#039;, $type = &#039;application/octent-stream&#039; )</b></p>
<p>Aggiunge un allegato</p>
</li>
<li><p><b>ClearAddresses()</b></p>
<p>Cancella tutti gli indirizzi</p>
</li>
<li><p><b>ClearAllRecipients()</b></p>
<p>Cancella tutti i destinatari</p>
</li>
<li><p><b>ClearAttachments()</b></p>
<p>Cancella tutti gli allegati</p>
</li>
<li><p><b>ClearBCCs()</b></p>
<p>Cancella gli indirizzi nel campo BCC</p>
</li>
<li><p><b>ClearCCs()</b></p>
<p>Cancella gli indirizzi nel campo CC</p>
</li>
<li><p><b>ClearCustomHeaders()</b></p>
<p>Cancella gli header personalizzati</p>
</li>
<li><p><b>ClearReplyto()</b></p>
<p>Cancella l&#039;indirizzo per le risposte</p>
</li>
<li><p><b>IsError()</b></p>
<p>Controlla se esistono condizioni di errore</p>
</li>
<li><p><b>bool IsHTML( $bool )</b></p>
<p>Imposta il flag html</p>
<p><i>Nota</i> questo dovrebbe essere un metodo get e set</p>
</li>
<li><p><b>bool IsMail()</b></p>
<p>Controlla se stiamo usando la funzione mail</p>
</li>
<li><p><b>bool IsQmail()</b></p>
<p>Controlla se stiamo usando la funzione qmail</p>
</li>
<li><p><b>IsSendmail()</b></p>
<p>Controlla se stiamo usando la funzione sendmail</p>
</li>
<li><p><b>IsSMTP()</b></p>
<p>Controlla se stiamo usando la funzione smtp</p>
</li>
<li><p><b>Send()</b></p>
<p>Invia la mail preparata ora</p>
</li>
<li><p><b>SetLanguage( $lang_type, $lang_path = &#039;&#039; )</b></p>
<p>Imposta la lingua corrente e <em>(opzionale)</em> il percorso della lingua</p>
</li>
<li><p><b>SmtpClose()</b></p>
<p>Chiude la connessione smtp</p>
</li>
</ul>
<h3>Supporto</h3>
<p>Questo modulo non include supporto commerciale. Tuttavia, ci sono delle risorse utilizzabili per aiutarvi con questo:</p>
<ul>
<li>Per l&#039;ultima versione del modulo, FAQs, Bug Report o per acquistare supporto commerciale, visitare la homepage di calguy a <a href=&#039;http://techcom.dyndns.org&#039;>techcom.dyndns.org</a>.</li>
<li>Ulteriori discussioni di questo modulo potete trovarle nei <a href=&#039;http://forum.cmsmadesimple.org&#039;>CMS Made Simple Forums</a>.</li>
<li>L&#039;autore, calguy1000, spesso pu&ograve; essere contattato nel <a href=&#039;irc://irc.freenode.net/#cms&#039;>CMS IRC Channel</a>.</li>
<li>Infine, puoi avere successo tramite email, direttamente all&#039;autore.</li>  
</ul>
<p>Come per la GPL, questo software &egrave; fornito cos&igrave; com&#039;&egrave;. Si prega di leggere il testo della licenza.</p>

<h3>Copyright e licenza</h3>
<p>Copyright &copy; 2005, Robert Campbell <a href=&#039;mailto:calguy1000@hotmail.com&#039;><calguy1000@hotmail.com></a>. Tutti i diritti sono riservati.</p>
<p>Questo modulo &egrave; stato rilasciato sotto la <a href=&#039;http://www.gnu.org/licenses/licenses.html#GPL&#039;>GNU Public License</a>. Devi aderire a questa licenza prima di usare il modulo.</p>';
$lang['utma'] = '156861353.880130626.1236353467.1240505117.1241641977.36';
$lang['utmz'] = '156861353.1240505117.35.31.utmccn=(referral)|utmcsr=forum.cmsmadesimple.org|utmcct=/index.php|utmcmd=referral';
$lang['utmb'] = '156861353';
$lang['utmc'] = '156861353';
?>