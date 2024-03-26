<?php


ob_start();
$result = array('success' => false, 'errorMsg' => '');

if (!empty($_SESSION['install'])) {
    $paramList = [
        'dateFormat',
        'timeFormat',
        'timeZone',
        'weekStart',
        'defaultCurrency',
        'thousandSeparator',
        'decimalMark',
        'language',
    ];

    $preferences = [];
    foreach ($paramList as $paramName) {
        if (array_key_exists($paramName, $_SESSION['install'])) {
            $preferences[$paramName] = $_SESSION['install'][$paramName];
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
