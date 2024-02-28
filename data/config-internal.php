<?php
return [
  'database' => [
    'host' => 'localhost',
    'port' => '',
    'charset' => NULL,
    'dbname' => 'northocrm',
    'user' => 'root',
    'password' => '',
    'platform' => 'Mysql'
  ],
  'smtpPassword' => NULL,
  'logger' => [
    'path' => 'data/logs/espo.log',
    'level' => 'WARNING',
    'rotation' => true,
    'maxFileNumber' => 30,
    'printTrace' => false
  ],
  'restrictedMode' => false,
  'webSocketMessager' => 'ZeroMQ',
  'clientSecurityHeadersDisabled' => false,
  'clientCspDisabled' => false,
  'clientCspScriptSourceList' => [
    0 => 'https://maps.googleapis.com'
  ],
  'adminUpgradeDisabled' => false,
  'isInstalled' => false,
  'microtimeInternal' => 1708777002.229169,
  'passwordSalt' => '1ee88b0b168ff2fc',
  'cryptKey' => '6e3dbe04f8580a0c7bae5310029c1b42',
  'hashSecretKey' => 'd185d9e32a0ea383bc8fa0fa3fd9aa28'
];
