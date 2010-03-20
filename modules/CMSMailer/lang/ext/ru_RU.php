<?php
$lang['info_cmsmailer'] = 'Этот модуль используется множеством других модулей для облегчения отправки писем. Он должен быть правильно настроен в соответсвие с требованиями почтового хостинга. Пожалуйста, используйте информацию хостинга для настройки этих параметров. Если вы все еще не можете получить тестовое сообщение отправленное должным образом, вы можете обратиться к вашему почтовому хостинг провайдеру за помощью';
$lang['charset'] = 'Кодировка';
$lang['sendtestmailconfirm'] = 'Это отправит тестовое сообщение по заданному адресу. Если процесс будет успешно завершен, Вы будете возвращены к этой странице. Вы хотите продолжить?';
$lang['settingsconfirm'] = 'Записать текущие значения в настройки CMSMailer?';
$lang['testsubject'] = 'Тестовое сообщение';
$lang['testbody'] = 'Это сообщение предназначено только для того, чтобы проверить достоверность параметров настройки модуля CMSMailer. 
Если Вы получили это, значит все работает нормально.';
$lang['error_notestaddress'] = 'Ошибка: Тестовый адрес не задан';
$lang['prompt_testaddress'] = 'Тестовый E-mail адрес';
$lang['sendtest'] = 'Отправить пробное сообщение';
$lang['password'] = 'Пароль ';
$lang['username'] = 'Имя пользователя';
$lang['smtpauth'] = 'SMTP аутентификация';
$lang['mailer'] = 'Метод отправки';
$lang['host'] = 'Имя сервера SMTP <br/><i>(или IP адрес)</i>';
$lang['port'] = 'Порт SMTP сервера';
$lang['from'] = 'От кого (адрес)';
$lang['fromuser'] = 'От кого (имя пользователя)';
$lang['sendmail'] = 'Местоположение Sendmail';
$lang['timeout'] = 'SMTP тайм-аут';
$lang['submit'] = 'Отправить';
$lang['cancel'] = 'Отмена';
$lang['info_mailer'] = 'Метод отправки (sendmail, smtp, mail). Обычно smtp является самым надежным.';
$lang['info_host'] = 'Имя хоста SMTP (только для метода отправки smtp)';
$lang['info_port'] = 'Порт SMTP (обычно 25) (только для метода отправки smtp)';
$lang['info_from'] = 'Адрес, используемый как адрес отправителя для e-mail';
$lang['info_fromuser'] = 'Дружественное имя, используемое для отправки всех писем';
$lang['info_sendmail'] = 'Полный путь к вашему sendmail (действителен только для метода отправки sendmail)';
$lang['info_timeout'] = 'Число секунд в сеансе связи SMTP перед тем, как произойдёт ошибка  (действительно только для метода отправки smtp)';
$lang['info_smtpauth'] = 'Требуется ли вашему smtp хосту аутентификация (действительно только для метода отправки smtp)';
$lang['info_username'] = 'Имя пользователя для SMTP (действительно только для метода отправки smtp)';
$lang['info_password'] = 'Пароль для SMTP (действительно только для метода отправки smtp)';
$lang['friendlyname'] = 'CMSMailer / Отправка почты';
$lang['postinstall'] = 'Модуль CMSMailer был успешно установлен';
$lang['postuninstall'] = 'Модуль CMSMailer удален';
$lang['uninstalled'] = 'Модуль удален';
$lang['installed'] = 'Модуль версии %s установлен';
$lang['accessdenied'] = 'Доступ запрещен. Пожалуйста проверьте ваши права доступа.';
$lang['error'] = 'Ошибка!';
$lang['upgraded'] = 'Модуль обновлен до версии %s';
$lang['title_mod_prefs'] = 'Настройки модуля';
$lang['title_mod_admin'] = 'Админ-панель модуля';
$lang['title_admin_panel'] = 'Модуль CMSMailer ';
$lang['moddescription'] = 'Это просто оболочка вокруг PHPMailer, она имеет эквивалент API (функции для функции), а также простой интерфейс, для некоторых значений по умолчанию.';
$lang['welcome_text'] = '<p>Добро пожаловать в раздел администрации модуля CMSMailer';
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
$lang['help'] = '<h3>Каково назначение данного элемента?</h3>
<p>This module provides no end user functionality.  It is designed to be integrated into other modules to provide email capabilities.  Thats it, nothing more.</p>
<h3>Как мне использовать это?</h3>
<p>This module provides a simple wrapper around all of the methods and variables of phpmailer.  It is designed for use by other module developers, below is an example, and a brief API reference.  Please read the PHPMailer documentation included for more information.</p>
<h3>Пример</h3>
<pre>
  $cmsmailer = $this->GetModuleInstance(\'CMSMailer\');
  $cmsmailer->AddAddress(\'calguy1000@hotmail.com\',\'calguy\');
  $cmsmailer->SetBody(\'<h4>This is a test message</h4>\');
  $cmsmailer->IsHTML(true);
  $cmsmailer->SetSubject(\'Test message\');
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
<p>Вернуть тип контента</p>
</li>
<li><p><b>void SetContentType()</b></p>
<p>Выбрать тип контента</p>
</li>
<li><p><b>string GetEncoding()</b></p>
<p>Вернуть кодирование</p>
</li>
<li><p><b>void SetEncoding( $encoding )</b></p>
<p>Выбрать кодирование</p>
<p>Options are: 8bit, 7bit, binary, base64, quoted-printable</p>
</li>
<li><p><b>string GetErrorInfo()</b></p>
<p>Return any error information</p>
</li>
<li><p><b>string GetFrom()</b></p>
<p>Return the current originating address</p>
</li>
<li><p><b>void SetFrom( $address )</b></p>
<p>Выбрать исходящий адрес </p>
</li>
<li><p><b>string GetFromName()</b></p>
<p>Вернуть текущее исходящее имя</p>
</li>
<li><p><b>SetFromName( $name )</b></p>
<p>Выбрать исходящее имяSet the originating name</p>
</li>
<li><p><b>string GetHelo()</b></p>
<p>Вернуть полосу HELO</p>
</li>
<li><p><b>SetHelo( $string )</b></p>
<p>Выбрать полосу Set the HELO</p>
<p>Default value: $hostname</p>
</li>
<li><p><b>string GetHost()</b></p>
<p>Вернуть хост SMTP отделенный точкой с запятой</p>
</li>
<li><p><b>void SetHost( $string )</b></p>
<p>Выбрать хост</p>
</li>
<li><p><b>string GetHostName()</b></p>
<p>Вернуть имя хоста, используемое для SMTP Helo</p>
</li>
<li><p><b>void SetHostName( $hostname )</b></p>
<p>Set the hostname used for SMTP Helo</p>
</li>
<li><p><b>string GetMailer()</b></p>
<p>Вернуть mailer</p>
</li>
<li><p><b>void SetMailer( $mailer )</b></p>
<p>Set the mailer, either sendmail,mail, or smtp</p>
</li>
<li><p><b>string GetPassword()</b></p>
<p>Вернуть пароль для smtp аутентификации</p>
</li>
<li><p><b>void SetPassword( $string )</b></p>
<p>Выбрать пароль для smtp аутентификации</p>
</li>
<li><p><b>int GetPort()</b></p>
<p>Вернуть номер порта для smtp соединений</p>
</li>
<li><p><b>void SetPort( $int )</b></p>
<p>Выбрать порт для smtp соединений</p>
</li>
<li><p><b>int GetPriority()</b></p>
<p>Вернуть приоритет сообщения</</p>
</li>
<li><p><b>void SetPriority( int )</b></p>
<p>Выбрать приоритет сообщения</p>
<p>Значения 1=высокий, 3 =нормальный, 5 = низкий</p>
</li>
<li><p><b>string GetSender()</b></p>
<p>Вернуть адрес отправителя (путь возвращения) строка</p>
</li>
<li><p><b>void SetSender( $address )</b></p>
<p>Выбрать строку отправителя</p>
</li>
<li><p><b>string GetSendmail()</b></p>
<p>Вернуть sendmail путь</p>
</li>
<li><p><b>void SetSendmail( $path )</b></p>
<p>Выбрать sendmail путь</p>
</li>
<li><p><b>bool GetSMTPAuth()</b></p>
<p>Вернуть текущее значение smtp auth flag</p>
</li>
<li><p><b>SetSMTPAuth( $bool )</b></p>
<p>Выбрать smtp auth flag</p>
</li>
<li><p><b>bool GetSMTPDebug()</b></p>
<p>Вернуть значение SMTP debug flag</p>
</li>
<li><p><b>void SetSMTPDebug( $bool )</b></p>
<p>Выбрать SMTP debug flag</p>
</li>
<li><p><b>bool GetSMTPKeepAlive()</b></p>
<p>Вернуть значение SMTP keep alive flag</p>
</li>
<li><p><b>SetSMTPKeepAlive( $bool )</b></p>
<p>Выбрать SMTP keep alive flag</p>
</li>
<li><p><b>строка GetSubject()</b></p>
<p>Вернуть текущий предмет строки</p>
</li>
<li><p><b>void SetSubject( $string )</b></p>
<p>Выбрать тему строки</p>
</li>
<li><p><b>int GetTimeout()</b></p>
<p>Вернуть значение  timeout</p>
</li>
<li><p><b>void SetTimeout( $seconds )</b></p>
<p>Выбрать значение timeout</p>
</li>
<li><p><b>string GetUsername()</b></p>
<p>Вернуть smtp auth имя пользователя</p>
</li>
<li><p><b>void SetUsername( $string )</b></p>
<p>Выбрать имя пользователя автора smtp</p>
</li>
<li><p><b>int GetWordWrap()</b></p>
<p>Вернуть значение wordwrap </p>
</li>
<li><p><b>void SetWordWrap( $int )</b></p>
<p>Вернуться к значению wordwrap</p>
</li>
<li><p><b>AddAddress( $address, $name = \'\' )</b></p>
<p>Добавить адрес  назначения</p>
</li>
<li><p><b>AddAttachment( $path, $name = \'\', $encoding = \'base64\', $type = \'application/octent-stream\' )</b></p>
<p>Добавить файл- аттачмент</p>
</li>
<li><p><b>AddBCC( $address, $name = \'\' )</b></p>
<p>Добавить  BCC\'d адрес назначения</p>
</li>
<li><p><b>AddCC( $address, $name = \'\' )</b></p>
<p>Add a CC\'d destination address</p>
</li>
<li><p><b>AddCustomHeader( $txt )</b></p>
<p> Добавить заголовок пользователя на email</p>
</li>
<li><p><b>AddEmbeddedImage( $path, $cid, $name = \'\', $encoding = \'base64\', $type = \'application/octent-stream\' )</b></p>
<p>Add an embedded image</p>
</li>
<li><p><b>AddReplyTo( $address, $name = \'\' )</b></p>
<p>Добавить ответ на адрес </p>
</li>
<li><p><b>AddStringAttachment( $string, $filename, $encoding = \'base64\', $type = \'application/octent-stream\' )</b></p>
<p>Add a file attachment</p>
</li>
<li><p><b>ClearAddresses()</b></p>
<p>Удалить все адреса</p>
</li>
<li><p><b>ClearAllRecipients()</b></p>
<p>Удалить всех адресатов</p>
</li>
<li><p><b>ClearAttachments()</b></p>
<p>Удалить все аттачменты</p>
</li>
<li><p><b>ClearBCCs()</b></p>
<p>Удалить все BCC адреса</p>
</li>
<li><p><b>ClearCCs()</b></p>
<p>Удалить все CC адреса</p>
</li>
<li><p><b>ClearCustomHeaders()</b></p>
<p>Удалить все заголовки пользователей</p>
</li>
<li><p><b>ClearReplyto()</b></p>
<p>Удалить ответ адресу</p>
</li>
<li><p><b>IsError()</b></p>
<p>Проверка на условия ошибки</p>
</li>
<li><p><b>bool IsHTML( $bool )</b></p>
<p>Выберите флажок html</p>
<p><i>Note</i> possibly this should be a get and set method</p>
</li>
<li><p><b>bool IsMail()</b></p>
<p>Проверить, используем ли мы mail</p>
</li>
<li><p><b>bool IsQmail()</b></p>
<p>Проверить, используем ли мы qmail</p>
</li>
<li><p><b>IsSendmail()</b></p>
<p>Проверить, используем ли мы sendmail</p>
</li>
<li><p><b>IsSMTP()</b></p>
<p>Проверить, используем ли мы smtp</p>
</li>
<li><p><b>Send()</b></p>
<p>Отправить приготовленные на данный момент письма</p>
</li>
<li><p><b>Выбрать язык( $lang_type, $lang_path = \'\' )</b></p>
<p>Выбрать текущий язык и <em>(опционально)</em> ветвь языка</p>
</li>
<li><p><b>SmtpClose()</b></p>
<p>Завершить smtp соединение</p>
</li>
</ul>
<h3>Поддержка</h3>
<p>Данный модуль не включает коммерческую поддержку. Тем не менее, существуют ресурсы, способные вам с эти помочь:</p>
<ul>
<li>Для последней версии этого модуля, ЧаВо, или зарегистрировать Bug Report или купить коммерческую поддержку, пожалуйста, загляните на страничку<ahref=\'http://techcom.dyndns.org\'>techcom.dyndns.org</a>.</li>
<li>Дополнительное обсуждение этой страницы можно также найти тут: <a href=\'http://forum.cmsmadesimple.org\'>CMS Made Simple Forums</a>.</li>
<li>Автора, calguy1000, часто можно застать вот тут:<a href=\'irc://irc.freenode.net/#cms\'>CMS IRC Channel</a>.</li>
<li>Наконец, возможно, вы достигнете своей цели, написав непосредственно автору.</li>  
</ul>
<p>As per the GPL, this software is provided as-is. Пожалуйста, прочитайте текст лицензии 
для полного отказа.</p>

<h3>Авторские права и лицензия</h3>
<p> Авторские права © 2005, Robert Campbell <a href=\'mailto:calguy1000@hotmail.com\'><calguy1000@hotmail.com></a>. All Rights Are Reserved.</p>
<p>This module has been released under the <a href=\'http://www.gnu.org/licenses/licenses.html#GPL\'>GNU Public License</a>. Вы должны согласиться с данной лицензией до того, как начнете использовать модуль.</p>';
$lang['utmz'] = '156861353.1249691644.74.46.utmccn=(referral)|utmcsr=localhost|utmcct=/msnew/install/index.php|utmcmd=referral';
$lang['utma'] = '156861353.1512485689.1236874721.1249691644.1250401334.75';
$lang['utmc'] = '156861353';
$lang['qca'] = 'P0-738804097-1250401341499';
$lang['utmb'] = '156861353.1.10.1250401334';
?>