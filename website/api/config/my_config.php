<?php

$config = array(
//APP SETTINGS
'App.name' => 'Liveteacher.com',
  'App.registerEmail' => 'registration@liveteacher.com',
  'App.feedbackEmail' => 'feedback@debuggable.com',
  'App.loginCookieLife' => '3600',
//SMTP SETTINGS
'App.smtpPort' => '25',
'App.mailTimeout' => '30',
'App.mailHost' => 'smtp.etmail.cz',
'App.fileDir' => 'files',
'App.defaultFileDir' => 'files/default',
);
date_default_timezone_set('UTC');
