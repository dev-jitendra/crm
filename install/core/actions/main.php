<?php


$config = $installer->getConfig();

$fields = [
    'user-lang' => [
        'default' => $config->get('language', 'en_US'),
    ],
    'theme' => [
        'default' => $config->get('theme'),
    ],
];

foreach ($fields as $fieldName => $field) {
    if (isset($_SESSION['install'][$fieldName])) {
        $fields[$fieldName]['value'] = $_SESSION['install'][$fieldName];
    } else {
        $fields[$fieldName]['value'] = (isset($field['default']))? $field['default'] : '';
    }
}

$language = $installer->createLanguage($_SESSION['install']['user-lang'] ?? 'en_US');

$themes = [];
foreach ($installer->getThemeList() as $item) {
    $themes[$item] = $language->translate($item, 'themes', 'Global');
}

$smarty->assign('themeLabel', $language->translate('theme', 'fields', 'Settings'));
$smarty->assign('fields', $fields);
$smarty->assign("themes", $themes);
