<?php

$this->Preference->set('mailer', 'smtp');
$this->Preference->set('host', 'localhost');
$this->Preference->set('port', 25 );
$this->Preference->set('from', 'root@localhost');
$this->Preference->set('fromuser', 'CMS Administrator');
$this->Preference->set('sendmail', '/usr/sbin/sendmail');
$this->Preference->set('timeout', 1000);
$this->Preference->set('smtpauth', 0);
$this->Preference->set('username', '');
$this->Preference->set('password', '');

?>