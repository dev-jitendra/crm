<?php


ob_start();

$result = [
    'success' => true,
    'errors' => [],
];

$phpRequiredList = $installer->getSystemRequirementList('php', true);

foreach ($phpRequiredList as $name => $details) {
    if (!$details['acceptable']) {

        switch ($details['type']) {
            case 'version':
                $result['success'] = false;
                $result['errors']['phpVersion'] = $details['required'];

                break;

            default:
                $result['success'] = false;
                $result['errors']['phpRequires'][] = $name;

                break;
        }
    }
}

$allPostData = $postData->getAll();

if (
    $result['success'] &&
    !empty($allPostData['dbName']) &&
    !empty($allPostData['hostName']) &&
    !empty($allPostData['dbUserName'])
) {
    $connect = false;

    $dbName = trim($allPostData['dbName']);

    if (!str_contains($allPostData['hostName'], ':')) {
        $allPostData['hostName'] .= ":";
    }

    [$hostName, $port] = explode(':', trim($allPostData['hostName']));

    $dbUserName = trim($allPostData['dbUserName']);
    $dbUserPass = trim($allPostData['dbUserPass']);

    if (!$port) {
        $port = null;
    }

    $platform = $allPostData['dbPlatform'] ?? 'Mysql';

    $databaseParams = [
        'platform' => $platform,
        'host' => $hostName,
        'port' => $port,
        'user' => $dbUserName,
        'password' => $dbUserPass,
        'dbname' => $dbName,
    ];

    $isConnected = true;

    try {
        $installer->checkDatabaseConnection($databaseParams, true);
    }
    catch (\Exception $e) {
        $isConnected = false;
        $result['success'] = false;
        $result['errors']['dbConnect']['errorCode'] = $e->getCode();
        $result['errors']['dbConnect']['errorMsg'] = $e->getMessage();
    }

    if ($isConnected) {
        $databaseRequiredList = $installer
            ->getSystemRequirementList('database', true, ['databaseParams' => $databaseParams]);

        foreach ($databaseRequiredList as $name => $details) {
            if (!$details['acceptable']) {
                switch ($details['type']) {
                    case 'version':
                        $result['success'] = false;
                        $result['errors'][$name] = $details['required'];

                        break;

                    default:
                        $result['success'] = false;
                        $result['errors'][$name][] = $name;

                        break;
                }
            }
        }
    }

}

ob_clean();
echo json_encode($result);
