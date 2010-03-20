<?php
$lang['info_cmsmailer'] = 'Ce module est utilis&eacute; par de nombreux autres modules pour faciliter l&#039;envoi d&#039;emails. Il doit &ecirc;tre correctement configur&eacute; pour votre h&eacute;bergement. Utiliser les informations fournies par votre h&eacute;bergeur pour r&eacute;gler ces param&egrave;tres. Si vous ne parvenez toujours pas  &agrave; envoyer le message de test correctement, vous devez contacter votre h&eacute;bergeur pour obtenir de l&#039;aide.';
$lang['charset'] = 'Format d&#039;encodage des caract&egrave;res&nbsp;';
$lang['sendtestmailconfirm'] = 'Ceci va envoyer un message de test &agrave; l&#039;adresse sp&eacute;cifi&eacute;e. Si le processus d&#039;envoi s&#039;effectue avec succ&egrave;s, vous serez redirig&eacute; &agrave; nouveau vers cette page. Voulez-vous continuer&nbsp;?';
$lang['settingsconfirm'] = 'Enregistrer les valeurs actuelles dans la configuration de CMSMailer&nbsp;?';
$lang['testsubject'] = 'Message de test de CMSMailer';
$lang['testbody'] = 'Ce message est une v&eacute;rification de la validit&eacute; des param&egrave;tres du module CMSMailer.
Si vous le recevez, tout fonctionne bien.';
$lang['error_notestaddress'] = 'Erreur&nbsp;: adresse de test non sp&eacute;cifi&eacute;e';
$lang['prompt_testaddress'] = 'Adresse email de test&nbsp;';
$lang['sendtest'] = 'Envoyer le message de test';
$lang['password'] = 'Mot de passe&nbsp;';
$lang['username'] = 'Identifiant&nbsp;';
$lang['smtpauth'] = 'Authentification SMTP&nbsp;';
$lang['mailer'] = 'M&eacute;thode d&#039;envoi des emails&nbsp;';
$lang['host'] = 'Nom du serveur SMTP<br/><i>(ou adresse IP)</i>&nbsp;';
$lang['port'] = 'Port du serveur SMTP&nbsp;';
$lang['from'] = 'Adresse de l&#039;exp&eacute;diteur&nbsp;';
$lang['fromuser'] = 'Identifiant de l&#039;exp&eacute;diteur&nbsp;';
$lang['sendmail'] = 'Emplacement de Sendmail&nbsp;';
$lang['timeout'] = 'D&eacute;lai SMTP avant erreur&nbsp;';
$lang['submit'] = 'Envoyer';
$lang['cancel'] = 'Annuler';
$lang['info_mailer'] = 'M&eacute;thode mail &agrave; utiliser (sendmail, smtp, mail). SMTP est habituellement le plus s&ucirc;r.';
$lang['info_host'] = 'Nom du serveur SMTP (seulement valable pour la m&eacute;thode mail SMTP).';
$lang['info_port'] = 'Num&eacute;ro de port SMTP (usuellement 25) (seulement valable pour la m&eacute;thode mail SMTP).';
$lang['info_from'] = 'Adresse utilis&eacute;e en tant qu&#039;exp&eacute;diteur pour tous les courriels (emails)<br/> <strong> Remarque </strong>, cette adresse email doit &ecirc;tre configur&eacute;e correctement pour votre h&eacute;bergement pour &eacute;viter des difficult&eacute;s d&#039;envoi des emails. <br/> Si vous ne connaissez pas la valeur correcte de ce param&egrave;tre, vous devez prendre contact avec votre h&eacute;bergeur.';
$lang['info_fromuser'] = 'Nom d&#039;exp&eacute;diteur complet utilis&eacute; pour l&#039;envoi des courriers';
$lang['info_sendmail'] = 'Chemin complet &agrave; votre ex&eacute;cutable sendmail (seulement valable pour la m&eacute;thode sendmail)';
$lang['info_timeout'] = 'Nombre de secondes dans une conversation SMTP avant qu&#039;une erreur n&#039;apparaisse (seulement valable pour la m&eacute;thode mail SMTP)';
$lang['info_smtpauth'] = 'Votre serveur SMTP requiert-il une authentification&nbsp;? (seulement valable pour la m&eacute;thode mail SMTP)';
$lang['info_username'] = 'Nom d&#039;utilisateur de l&#039;authentification SMTP (seulement valable pour la m&eacute;thode mail SMTP, quand l&#039;authentification est s&eacute;lectionn&eacute;e)';
$lang['info_password'] = 'Mot de passe de l&#039;authentification SMTP (seulement valable pour la m&eacute;thode mail SMTP, quand l&#039;authentification est s&eacute;lectionn&eacute;e)';
$lang['friendlyname'] = 'CMSMailer ';
$lang['postinstall'] = 'Le module CMSMailer a &eacute;t&eacute; install&eacute; avec succ&egrave;s';
$lang['postuninstall'] = 'Le module CMSMailer a &eacute;t&eacute; d&eacute;sinstall&eacute;. D&eacute;sol&eacute;e de vous voir partir...';
$lang['uninstalled'] = 'Module d&eacute;sinstall&eacute;.';
$lang['installed'] = 'La version %s du module a &eacute;t&eacute; install&eacute;e.';
$lang['accessdenied'] = 'Acc&egrave;s refus&eacute;. Veuillez v&eacute;rifier vos permissions.';
$lang['error'] = 'Erreur&nbsp;!';
$lang['upgraded'] = 'Le module a &eacute;t&eacute; mis &agrave; jour &agrave; la version %s.';
$lang['title_mod_prefs'] = 'Pr&eacute;f&eacute;rences du module';
$lang['title_mod_admin'] = 'Panneau d&#039;administration du module';
$lang['title_admin_panel'] = 'CMSMailer';
$lang['moddescription'] = 'Ceci englobe simplement PHPMailer, il a un &eacute;quivalent API (fonction pour fonction) et une interface simple pour les param&egrave;tres par d&eacute;faut.';
$lang['welcome_text'] = '<p>Bienvenue dans le panneau d&#039;administration du module CMSMailer';
$lang['changelog'] = '<ul>
<li>Version 1.73.1. Octobre 2005. Version initiale.</li>
<li>Version 1.73.2. Octobre 2005. Correction dans le panneau d&#039;administration.  La liste d&eacute;roulante ne repr&eacute;sentait pas la valeur actuelle de la base de donn&eacute;es des pr&eacute;f&eacute;rences</li>
<li>Version 1.73.3. Octobre 2005. Petite correction lors de l&#039;envoi d&#039;email en format html</li>
<li>Version 1.73.4. Novembre 2005. Les champs du formulaire des pr&eacute;f&eacute;rences sont plus grands, r&eacute;solu un probl&egrave;me avec fromuser, et appel du reset dans le constructeur</li>
<li>Version 1.73.5. Novembre 2005. Ajout de champs et fonctionnalit&eacute;s pour l&#039;authentification SMTP.</li>
<li>Version 1.73.6. D&eacute;cembre 2005. La m&eacute;thode mail par d&eacute;faut lors de l&#039;installation est SMTP, documentation am&eacute;lior&eacute;e, les attachements sont supprim&eacute;s, ainsi que les adresses, etc. lors du reset.</li>
<li>Version 1.73.7. Janvier 2006. Augment&eacute; la taille des champs pour la plupart d&#039;entre eux</li>
<li>Version 1.73.8. Janvier 2006. Modifi&eacute; le panneau des pr&eacute;f&eacute;rences pour le rendre un peu plus descriptif.</li>
<li>Version 1.73.9. Janvier 2006. Ajout&eacute; la possibilit&eacute; du test email, et une confirmation &agrave; chaque bouton (sauf Annuler)</li>
<li>Version 1.73.10. Ao&ucirc;t 2006. Modifi&eacute; pour l&#039;utilisation du &quot;lazy loading&quot; pour minimiser les besoins m&eacute;moire lorsque CMSMailer n&#039;est pas utilis&eacute;.</li>
<li>Version 1.73.13. Janvier 2008.  Ajout de plus de v&eacute;rifications de permissions.</li>
</ul>';
$lang['help'] = '<h3>Que fait ce module&nbsp;?</h3>
<p>Ce module n&#039;apporte pas de fonctionnalit&eacute; pour l&#039;utilisateur final.  Il a &eacute;t&eacute; &eacute;crit pour &ecirc;tre int&eacute;gr&eacute; dans d&#039;autres modules et fournir des possibilit&eacute;s d&#039;envoi de courriels.  Voil&agrave;, rien de plus.</p>
<h3>Comment l&#039;utiliser&nbsp;?</h3>
<p>Ce module englobe toutes les m&eacute;thodes et variables de phpmailer. Il est construit de mani&egrave;re &agrave; pouvoir &ecirc;tre utilis&eacute; par les d&eacute;veloppeurs des autres modules, voir l&#039;exemple ci-dessous et une br&egrave;ve r&eacute;f&eacute;rence API. Veuillez lire la documentation de PHPMailer pour plus d&#039;information.</p>
<h3>Un exemple</h3>
<pre>
  $cmsmailer = $this->GetModuleInstance(&#039;CMSMailer&#039;);
  $cmsmailer->AddAddress(&#039;calguy1000@hotmail.com&#039;,&#039;calguy&#039;);
  $cmsmailer->SetBody(&#039;<b>Ceci est un message de test</b>&#039;);
  $cmsmailer->IsHTML(true);
  $cmsmailer->SetSubject(&#039;Test message&#039;);
  $cmsmailer->Send();
</pre>
<h3>API</h3>
<ul>
<li><p><b>void reset()</b></p>
<p>R&eacute;initialise l&#039;object &agrave; la valeur sp&eacute;cifi&eacute;e dans le panneau d&#039;administration</p>
</li>
<li><p><b>string GetAltBody()</b></p>
<p>Retourne le corps de remplacement de l&#039;email</p>
</li>
<li><p><b>void SetAltBody( $string )</b></p>
<p>D&eacute;fini le corps de remplacement de l&#039;email</p>
</li>
<li><p><b>string GetBody()</b></p>
<p>Retourne le corps principal de l&#039;email</p>
</li>
<li><p><b>void SetBody( $string )</b></p>
<p>D&eacute;fini le corps principal de l&#039;email</p>
</li>
<li><p><b>string GetCharSet()</b></p>
<p>D&eacute;faut: iso-8859-1</p>
<p>Retourne l&#039;encodage des caract&egrave;res du mailer</p>
</li>
<li><p><b>void SetCharSet( $string )</b></p>
<p>D&eacute;fini l&#039;encodage des caract&egrave;res</p>
</li>
<li><p><b>string GetConfirmReadingTo()</b></p>
<p>Retourne l&#039;adresse de confirmation de lecture</p>
</li>
<li><p><b>void SetConfirmReadingTo( $address )</b></p>
<p>D&eacute;fini ou enl&egrave;ve l&#039;adresse de confirmation de lecture</p>
</li>
<li><p><b>string GetContentType()</b></p>
<p>D&eacute;faut: text/plain</p>
<p>Retourne le content type</p>
</li>
<li><p><b>void SetContentType()</b></p>
<p>D&eacute;fini le content type</p>
</li>
<li><p><b>string GetEncoding()</b></p>
<p>Retourne l&#039;encodage</p>
</li>
<li><p><b>void SetEncoding( $encoding )</b></p>
<p>D&eacute;fini l&#039;encodage</p>
<p>Les options sont: 8bit, 7bit, binary, base64, quoted-printable</p>
</li>
<li><p><b>string GetErrorInfo()</b></p>
<p>Retourne toute information d&#039;erreur</p>
</li>
<li><p><b>string GetFrom()</b></p>
<p>Retourne l&#039;adresse d&#039;origine en cours</p>
</li>
<li><p><b>void SetFrom( $address )</b></p>
<p>D&eacute;fini l&#039;adresse d&#039;origine</p>
</li>
<li><p><b>string GetFromName()</b></p>
<p>Retourne le nom d&#039;origine en cours</p>
</li>
<li><p><b>SetFromName( $name )</b></p>
<p>D&eacute;fini le nom d&#039;origine</p>
</li>
<li><p><b>string GetHelo()</b></p>
<p>Retourne le string HELO</p>
</li>
<li><p><b>SetHelo( $string )</b></p>
<p>D&eacute;fini  le string HELO</p>
<p>Valeur par d&eacute;faut: $hostname</p>
</li>
<li><p><b>string GetHost()</b></p>
<p>Retourne les hosts SMTPs s&eacute;par&eacute;s par des points-virgules</p>
</li>
<li><p><b>void SetHost( $string )</b></p>
<p>D&eacute;fini les hosts</p>
</li>
<li><p><b>string GetHostName()</b></p>
<p>Retourne le nom du host utilis&eacute; pour le Helo SMTP</p>
</li>
<li><p><b>void SetHostName( $hostname )</b></p>
<p>D&eacute;fini le nom du host utilis&eacute; pour le Helo SMTP</p>
</li>
<li><p><b>string GetMailer()</b></p>
<p>Retourne le mailer</p>
</li>
<li><p><b>void SetMailer( $mailer )</b></p>
<p>D&eacute;fini le mailer, soit sendmail,mail, ou smtp</p>
</li>
<li><p><b>string GetPassword()</b></p>
<p>Retourne le mot de passe pour l&#039;authentification smtp</p>
</li>
<li><p><b>void SetPassword( $string )</b></p>
<p>D&eacute;fini le mot de passe pour l&#039;authentification smtp</p>
</li>
<li><p><b>int GetPort()</b></p>
<p>Retourne le port pour les connexions smtp</p>
</li>
<li><p><b>void SetPort( $int )</b></p>
<p>D&eacute;fini le port pour les connexions smtp</p>
</li>
<li><p><b>int GetPriority()</b></p>
<p>Retourne la priorit&eacute; du message</p>
</li>
<li><p><b>void SetPriority( int )</b></p>
<p>D&eacute;fini la priorit&eacute; du message</p>
<p>Les valeurs sont 1=Haute, 3 = Normale, 5 = Basse</p>
</li>
<li><p><b>string GetSender()</b></p>
<p>Retourne l&#039;email de l&#039;exp&eacute;diteur (adresse de r&eacute;ponse)</p>
</li>
<li><p><b>void SetSender( $address )</b></p>
<p>D&eacute;fini l&#039;email de l&#039;exp&eacute;diteur</p>
</li>
<li><p><b>string GetSendmail()</b></p>
<p>Retourne le chemin &agrave; sendmail</p>
</li>
<li><p><b>void SetSendmail( $path )</b></p>
<p>D&eacute;fini le chemin &agrave; sendmail</p>
</li>
<li><p><b>bool GetSMTPAuth()</b></p>
<p>Retourne la valeur en cours du flag d&#039;authentification de smtp</p>
</li>
<li><p><b>SetSMTPAuth( $bool )</b></p>
<p>D&eacute;fini la valeur en cours du flag d&#039;authentification de smtp</p>
</li>
<li><p><b>bool GetSMTPDebug()</b></p>
<p>Retourne la valeur du debug flag de SMTP</p>
</li>
<li><p><b>void SetSMTPDebug( $bool )</b></p>
<p>D&eacute;fini la valeur du debug flag de SMTP</p>
</li>
<li><p><b>bool GetSMTPKeepAlive()</b></p>
<p>Retourne la valeur du keep alive flag de SMTP</p>
</li>
<li><p><b>SetSMTPKeepAlive( $bool )</b></p>
<p>D&eacute;fini la valeur du keep alive flag de SMTP</p>
</li>
<li><p><b>string GetSubject()</b></p>
<p>Retourne le sujet en cours</p>
</li>
<li><p><b>void SetSubject( $string )</b></p>
<p>D&eacute;fini le sujet</p>
</li>
<li><p><b>int GetTimeout()</b></p>
<p>Retourne la valeur du timeout</p>
</li>
<li><p><b>void SetTimeout( $seconds )</b></p>
<p>D&eacute;fini la valeur du timeout</p>
</li>
<li><p><b>string GetUsername()</b></p>
<p>Retourne le nom d&#039;utilisateur de l&#039;authentification smtp</p>
</li>
<li><p><b>void SetUsername( $string )</b></p>
<p>D&eacute;fini le nom d&#039;utilisateur de l&#039;authentification smtp</p>
</li>
<li><p><b>int GetWordWrap()</b></p>
<p>Retourne la valeur wordwrap</p>
</li>
<li><p><b>void SetWordWrap( $int )</b></p>
<p>Retourne la valeur wordwrap</p>
</li>
<li><p><b>AddAddress( $address, $name = &#039;&#039; )</b></p>
<p>Ajoute une adresse de destination</p>
</li>
<li><p><b>AddAttachment( $path, $name = &#039;&#039;, $encoding = &#039;base64&#039;, $type = &#039;application/octent-stream&#039; )</b></p>
<p>Ajoute un fichier attach&eacute;</p>
</li>
<li><p><b>AddBCC( $address, $name = &#039;&#039; )</b></p>
<p>Ajoute une adresse de destination cach&eacute;e (BCC)</p>
</li>
<li><p><b>AddCC( $address, $name = &#039;&#039; )</b></p>
<p>Ajoute une adresse de destination copie (CC)</p>
</li>
<li><p><b>AddCustomHeader( $txt )</b></p>
<p>Ajoute un en-t&ecirc;te personnalis&eacute;e au message</p>
</li>
<li><p><b>AddEmbeddedImage( $path, $cid, $name = &#039;&#039;, $encoding = &#039;base64&#039;, $type = &#039;application/octent-stream&#039; )</b></p>
<p>Ajoute une image incluse</p>
</li>
<li><p><b>AddReplyTo( $address, $name = &#039;&#039; )</b></p>
<p>Ajoute une adresse de r&eacute;ponse</p>
</li>
<li><p><b>AddStringAttachment( $string, $filename, $encoding = &#039;base64&#039;, $type = &#039;application/octent-stream&#039; )</b></p>
<p>Ajoute un fichier attach&eacute;</p>
</li>
<li><p><b>ClearAddresses()</b></p>
<p>Efface toutes les adresses</p>
</li>
<li><p><b>ClearAllRecipients()</b></p>
<p>Efface tous les destinataires</p>
</li>
<li><p><b>ClearAttachments()</b></p>
<p>Efface tous les attachements</p>
</li>
<li><p><b>ClearBCCs()</b></p>
<p>Efface toutes les adresses BCC</p>
</li>
<li><p><b>ClearCCs()</b></p>
<p>Efface toutes les adresses CC</p>
</li>
<li><p><b>ClearCustomHeaders()</b></p>
<p>Efface toutes les en-t&ecirc;tes personnalis&eacute;es</p>
</li>
<li><p><b>ClearReplyto()</b></p>
<p>Efface l&#039;adresse de r&eacute;ponse</p>
</li>
<li><p><b>IsError()</b></p>
<p>V&eacute;rifie une condition d&#039;erreur</p>
</li>
<li><p><b>bool IsHTML( $bool )</b></p>
<p>D&eacute;fini le html flag</p>
<p><i>Note</i> si possible cela devrait &ecirc;tre une m&eacute;thode get et set</p>
</li>
<li><p><b>bool IsMail()</b></p>
<p>V&eacute;rifie si on utilise &#039;mail&#039;</p>
</li>
<li><p><b>bool IsQmail()</b></p>
<p>V&eacute;rifie si on utilise &#039;qmail&#039;</p>
</li>
<li><p><b>IsSendmail()</b></p>
<p>V&eacute;rifie si on utilise &#039;sendmail&#039;</p>
</li>
<li><p><b>IsSMTP()</b></p>
<p>V&eacute;rifie si on utilise &#039;smtp&#039;</p>
</li>
<li><p><b>Send()</b></p>
<p>Envoie le mail en cours de pr&eacute;paratioon</p>
</li>
<li><p><b>SetLanguage( $lang_type, $lang_path = &#039;&#039; )</b></p>
<p>D&eacute;fini la langue <em>(optional)</em> language path</p>
</li>
<li><p><b>SmtpClose()</b></p>
<p>Ferme la connexion smtp</p>
</li>
</ul>
<h3>Support</h3>
<p>Ce module ne contient aucun support commercial. Cependant, ces ressources sont disponibles pour vous aider&nbsp;:</p>
<ul>
<li>Pour la derni&egrave;re version de ce module, les FAQs, ou pour annoncer un bug, veuillez visiter la <a href="http://dev.cmsmadesimple.org/" target="_blank">forge de CMS Made Simple</a>.</li>
<li>Des discussions compl&eacute;mentaires relatives &agrave; ce module peuvent aussi &ecirc;tre trouv&eacute;es sur les <a href="http://forum.cmsmadesimple.org" target="_blank">Forums CMS Made Simple</a>.</li>
<li>L&#039;auteur, Calguy1000, est souvent sur IRC sur canal #cms: irc.freenode.net/#cms.</li>
<li>Et enfin, vous pouvez rencontrer un certain succ&egrave;s en envoyant un email directement &agrave; l&#039;auteur.</li>  
</ul>
<p>Conform&eacute;ment &agrave; la licence GPL, ce module est distribu&eacute; tel quel. Veuillez vous r&eacute;f&eacute;rer directement &agrave; la licence pour tout avis de non responsabilit&eacute;.</p>

<h3>Copyright et Licence</h3>
<p>Copyright &copy; 2005, Robert Campbell <a href="mailto:calguy1000@hotmail.com">calguy1000@hotmail.com</a>. Tous droits r&eacute;serv&eacute;s.</p>
<p>Ce module est distribu&eacute; sous la licence <a href="http://www.gnu.org/licenses/licenses.html#GPL" target="_blank">GNU Public License</a>. Vous devez agr&eacute;er aux termes de cette licence pour pouvoir utiliser ce module.</p>
';
$lang['qca'] = 'P0-1624099198-1262715852421';
$lang['utma'] = '156861353.2010162014.1266244671.1266244671.1266244671.1';
$lang['utmc'] = '156861353';
$lang['utmz'] = '156861353.1266244671.1.1.utmccn=(direct)|utmcsr=(direct)|utmcmd=(none)';
$lang['utmb'] = '156861353';
?>