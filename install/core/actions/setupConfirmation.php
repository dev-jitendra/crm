<?php


$phpRequirementList = $installer->getSystemRequirementList('php');
$smarty->assign('phpRequirementList', $phpRequirementList);

$installData = $_SESSION['install'];
$hostData = explode(':', $installData['host-name']);

$dbConfig = [
    'host' => $hostData[0] ?? '',
    'port' => $hostData[1] ?? '',
    'dbname' => $installData['db-name'],
    'user' => $installData['db-user-name'],
    'password' => $installData['db-user-password'],
    'platform' => $installData['db-platform'] ?? null,
];

$mysqlRequirementList = $installer->getSystemRequirementList('database', false, ['databaseParams' => $dbConfig]);
$smarty->assign('mysqlRequirementList', $mysqlRequirementList);

$permissionRequirementList = $installer->getSystemRequirementList('permission');
$smarty->assign('permissionRequirementList', $permissionRequirementList);
