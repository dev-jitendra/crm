<?php


$cronMessage = $installer->getCronMessage();

$smarty->assign('cronTitle', $cronMessage['message']);
$smarty->assign('cronHelp', $cronMessage['command']);

$installer->setSuccess();


session_unset();
