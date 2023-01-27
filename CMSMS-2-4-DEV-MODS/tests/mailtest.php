#!/usr/bin/env php
<?php

$to  = 'TBA';
$subject = 'PHP mail-function test';
$message = 'It worked!';
$headers = 'From: TBA@TBA' . "\r\n" . 'X-Mailer: PHP/' . phpversion();
$res = mail($to, $subject, $message, $headers);
echo 'mail-function result = '.$res;
echo "\n";
