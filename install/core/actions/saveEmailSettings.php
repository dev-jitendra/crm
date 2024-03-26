<?php


ob_start();
$result = array('success' => false, 'errorMsg' => '');

if (!empty($_SESSION['install'])) {

    $paramList = [
        'smtpServer',
        'smtpPort',
        'smtpAuth',
        'smtpSecurity',
        'smtpUsername',
        'smtpPassword',
        'outboundEmailFromName',
        'outboundEmailFromAddress',
        'outboundEmailIsShared',
    ];

    $preferences = [];
    foreach ($paramList as $paramName) {

        switch ($paramName) {
            case 'smtpAuth':
                $preferences['smtpAuth'] = (empty($_SESSION['install']['smtpAuth']) || $_SESSION['install']['smtpAuth'] == 'false' || !$_SESSION['install']['smtpAuth']) ? false : true;
                break;

            case 'outboundEmailIsShared':
                $preferences['outboundEmailIsShared'] = (empty($_SESSION['install']['smtpAuth']) || $_SESSION['install']['outboundEmailIsShared'] == 'false' || !$_SESSION['install']['smtpAuth']) ? false : true;
                break;

            default:
                if (array_key_exists($paramName, $_SESSION['install'])) {
                    $preferences[$paramName] = $_SESSION['install'][$paramName];
                }
                break;
        }

    }

    $res = $installer->savePreferences($preferences);
    if (!empty($res)) {
        $result['success'] = true;
    }
    else {
        $result['success'] = false;
        $result['errorMsg'] = 'Cannot save preferences';
    }
}
else {
    $result['success'] = false;
    $result['errorMsg'] = 'Cannot save preferences';
}

ob_clean();
echo json_encode($result);
