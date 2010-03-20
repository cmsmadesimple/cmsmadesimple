<?php
$lang['info_cmsmailer'] = 'Este m&oacute;dulo es usado por un sin numero de modulos  para facilitar el envio de Emails. Debe ser configurado apropiadamente respeco de los requerimientos de su Hosting (hospedaje).  Por favor use la informaci&oacute;n provista por su Hospedaje para ajustar la configuraci&oacute;n. Si aun no puede enviar un Email de prueba, es posible que necesite contactar a su Hospedaje para ayuda.';
$lang['charset'] = 'Juego de Caracteres';
$lang['sendtestmailconfirm'] = 'Esto env&iacute;a un mensaje de prueba a la direcci&oacute;n especificada. Si todo va bien, seras redirigido a esta pagina.  &iquest;Quieres continuar?';
$lang['settingsconfirm'] = '&iquest;Guardar los valores actuales de CMSMailer?';
$lang['testsubject'] = 'Mensaje de prueba de CMSMailer';
$lang['testbody'] = 'Este mensaje es solo para verificar la validez de los valores del m&oacute;dulo CMSMailer.
Si lo has recibido, todo funciona correctamente.';
$lang['error_notestaddress'] = 'Error: Direcci&oacute;n de prueba no especificada';
$lang['prompt_testaddress'] = 'Direcci&oacute;n de Prueba';
$lang['sendtest'] = 'Enviar Mensaje de Prueba';
$lang['password'] = 'Clave';
$lang['username'] = 'Usuario';
$lang['smtpauth'] = 'Autenticaci&oacute;n SMTP';
$lang['mailer'] = 'M&eacute;todo de envio';
$lang['host'] = 'Host SMTP<br/><i>(o direcci&oacute;n IP)</i>';
$lang['port'] = 'Puerto del servidor SMTP';
$lang['from'] = 'Direcci&oacute;n &quot;desde&quot;';
$lang['fromuser'] = 'Usuario &quot;desde&quot;';
$lang['sendmail'] = 'Localizaci&oacute;n';
$lang['timeout'] = 'Tiempo de espera SMTP';
$lang['submit'] = 'Enviar';
$lang['cancel'] = 'Cancelar';
$lang['info_mailer'] = 'Protocolo a usar (sendmail, smtp, mail).  Normalmente se usa SMTP.';
$lang['info_host'] = 'Nombre de Host SMTP (s&oacute;lo valido para el protocolo SMTP)';
$lang['info_port'] = 'Puerto SMTP (normalmente 25) (s&oacute;lo valido para el protocolo SMTP)';
$lang['info_from'] = 'Direcci&oacute;n desde donde enviar los mensajes';
$lang['info_fromuser'] = 'Nombre del usuario que envia los mensajes';
$lang['info_sendmail'] = 'Path completo del ejectable sendamil (s&oacute;lo valido para el protocolo sendmailer)';
$lang['info_timeout'] = 'N&uacute;mero de segundos en una conversaci&oacute;n SMTP antes de ocurrir un error (s&oacute;lo valido para el protocolo SMTP)';
$lang['info_smtpauth'] = 'Tu Host SMTP requiere autenticaci&oacute;n (s&oacute;lo valido para el protocolo SMTP)';
$lang['info_username'] = 'Usuario autenticaci&oacute;n SMTP (valido para el m&eacute;todo smtp, cuando esta activa la autenticaci&oacute;n)';
$lang['info_password'] = 'Clave de autenticaci&oacute;n SMTP (valido solo para el m&eacute;todo smtp, cuando esta activa la autenticaci&oacute;n)';
$lang['friendlyname'] = 'M&oacute;dulo CMSMailer';
$lang['postinstall'] = 'M&oacute;dulo CMSMailer instalado correctamente';
$lang['postuninstall'] = 'M&oacute;dulo CMSMailer desinstalado... siento que nos dejes';
$lang['uninstalled'] = 'M&oacute;dulo Desistalado.';
$lang['installed'] = 'M&oacute;dulo versi&oacute;n %s instalado.';
$lang['accessdenied'] = 'Acceso Denegado. revisa tus permisos.';
$lang['error'] = '&iexcl;Error!';
$lang['upgraded'] = 'M&oacute;dulo actualizado a versi&oacute;n %s.';
$lang['title_mod_prefs'] = 'Preferencias del M&oacute;dulo';
$lang['title_mod_admin'] = 'Administraci&oacute;n del M&oacute;dulo';
$lang['title_admin_panel'] = 'M&oacute;dulo CMSMailer';
$lang['moddescription'] = 'Esto es una simple cobertura sobre PHPMailer, tiene un API equivalente (funci&oacute;n a funci&oacute;n) y una interfase simple.';
$lang['welcome_text'] = '<p>Bienvenido a la administraci&oacute;n del m&oacute;dulo CMSMailer';
$lang['changelog'] = '<ul>
<li>Version 1.73.1. October, 2005. Initial Release.</li>
<li>Version 1.73.2. October, 2005. Minor bug fix with the admin panel.  The dropdown was not representing the current value from the preferences database</li>
<li>Version 1.73.3. October, 2005. Minor bug fix with sending html email</li>
<li>Version 1.73.4. November, 2005. Form fields in preferences are larger, fixed a problem with the fromuser, and called reset within the constructor</li>
<li>Version 1.73.5. November, 2005. Added the form fields and functionality for SMTP authentication.</li>
<li>Version 1.73.6. December, 2005. Default mailer method is SMTP on install, and improved documentation, and now I clear all the attachments, and addresses, etc. on reset.</li>
<li>Version 1.73.7. January, 2006. Increased field lengths in most fields</li>
<li>Version 1.73.8. January, 2006. Changed the preferences panel to be a bit more descriptive.</li>
</ul>';
$lang['help'] = '<h3>&iquest;Qu&eacute; hace esto?</h3>
<p>Este m&oacute;dulo no provee funcionalidad directa para el usuario del portal.  Esta dise&ntilde;ado para ser integrado con otros m&oacute;dulos y proveerles capacidades de email.  Esa es simplemente su utilidad.</p>
<h3>Como lo puedo usar</h3>
<p>Este m&oacute;dulo provee una simple cobertura alrededor de todos los m&eacute;todos y variables de phpmailer.  Esta dise&ntilde;ado para ser utilizado por los dise&ntilde;adores de otros m&oacute;dulos, abajo encontrar&aacute; un ejemplo, y una breve referencia del API.  Por favor lea la documentaci&oacute;n de PHPMailer incluida para mayor informaci&oacute;n.</p>
<h3>Un Ejemplo</h3>
<pre>
  $cmsmailer = $this->GetModuleInstance(&#039;CMSMailer&#039;);
  $cmsmailer->AddAddress(&#039;calguy1000@hotmail.com&#039;,&#039;calguy&#039;);
  $cmsmailer->SetBody(&#039;<h1>Este es un mensaje de prueba<h1>&#039;);
  $cmsmailer->IsHTML(true);
  $cmsmailer->SetSubject(&#039;Mensaje de prueba&#039;);
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
<h3>Soporte</h3>
<p>Este m&oacute;dulo no incluye soporte comercial. Sin embargo, hay un n&uacute;mero de medios disponibles para ayudar a usted:</p>
<ul>
<li>Para la &uacute;ltima versi&oacute;n del m&oacute;dulo, FAQs, o para presentar un informe de Bug o bien comprar soporte comercial, por favor visite la p&aacute;gina web de calguys en <a href="http://techcom.dyndns.org">techcom.dyndns.org</a>.</li>
<li>Intercambio de informaci&oacute;n e ideas respecto a este m&oacute;dulo se puede encontrar tambi&eacute;n en el <a href="http://forum.cmsmadesimple.org">Foro de CMS Made Simple</a>.</li>
<li>El autor, calguy1000, se puede encontrar con frecuencia en el <a href="irc://irc.freenode.net/#cms">Canal CMS IRC</a>.</li>
<li>Finalmente, puede que obtenga alg&uacute;n &eacute;xito poni&eacute;ndose en comunicaci&oacute;n por email directamente con el autor.</li>  
</ul>
<p>De acuerdo a la GPL, este software se provee as&iacute; como es. Por favor lea el texto de la licencia para obtener lo que all&iacute; se afirma.</p>

<h3>Copyright y Licencia</h3>
<p>Copyright &copy; 2005, Robert Campbell <a href="mailto:calguy1000@hotmail.com"><calguy1000@hotmail.com></a>. All Rights Are Reserved.</p>
<p>Este m&oacute;dulo se ha realizado bajo la <a href="http://www.gnu.org/licenses/licenses.html#GPL">Licencia P&uacute;blica GNU</a>. Usted debe estar de acuerdo con &eacute;sta licencia antes de hacer uso del m&oacute;dulo.</p>
';
$lang['utma'] = '156861353.3874850749547776000.1214866856.1225056594.1225131274.192';
$lang['utmz'] = '156861353.1222939950.153.16.utmccn=(organic)|utmcsr=google|utmctr=cmsms pretty urls|utmcmd=organic';
$lang['utmc'] = '156861353';
$lang['utmb'] = '156861353.1.10.1225131274';
?>