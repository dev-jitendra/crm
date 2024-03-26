<?php


$config = $installer->getConfig();
$metadata = $installer->getMetadata();

$fields = [
    'dateFormat' => [
        'default' => $config->get('dateFormat'),
        'options' => $metadata->get(['app', 'dateTime', 'dateFormatList']) ?? [],
    ],
    'timeFormat' => [
        'default'=> $config->get('timeFormat'),
        'options' => $metadata->get(['app', 'dateTime', 'timeFormatList']) ?? [],
    ],
    'timeZone' => [
        'default'=> $config->get('timeZone', 'UTC'),
    ],
    'weekStart' => [
        'default'=> $config->get('weekStart', 0),
    ],
    'defaultCurrency' => [
        'default' => $config->get('defaultCurrency', 'USD'),
    ],
    'thousandSeparator' => [
        'default' => $config->get('thousandSeparator', ','),
    ],
    'decimalMark' => [
        'default' => $config->get('decimalMark', '.'),
    ],
    'language' => [
        'default' => (!empty($_SESSION['install']['user-lang'])) ?
            $_SESSION['install']['user-lang'] :
            $config->get('language', 'en_US'),
    ],
];

foreach ($fields as $fieldName => $field) {
    if (isset($_SESSION['install'][$fieldName])) {
        $fields[$fieldName]['value'] = $_SESSION['install'][$fieldName];
    } else {
        $fields[$fieldName]['value'] = isset($field['default']) ? $field['default'] : '';
    }
}

$smarty->assign('fields', $fields);
