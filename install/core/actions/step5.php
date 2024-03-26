<?php


$config = $installer->getConfig();

$fields = array(
    'smtpServer' => array(
        'default' => $config->get('smtpServer'),
    ),
    'smtpPort' => array(
        'default' => $config->get('smtpPort', 25),
    ),
    'smtpAuth' => array(
        'default' => false,
    ),
    'smtpSecurity' => array(
        'default' => $config->get('smtpSecurity'),
    ),
    'smtpUsername' => array(
        'default' => $config->get('smtpUsername'),
    ),
    'smtpPassword' => array(
        'default' => $config->get('smtpPassword'),
    ),
    'outboundEmailFromName' => array(
        'default' => $config->get('outboundEmailFromName'),
    ),
    'outboundEmailFromAddress' => array(
        'default' => $config->get('outboundEmailFromAddress'),
    ),
    'outboundEmailIsShared' => array(
        'default' => $config->get('outboundEmailIsShared', false),
    ),
);

foreach ($fields as $fieldName => $field) {
    if (isset($_SESSION['install'][$fieldName])) {
        $fields[$fieldName]['value'] = $_SESSION['install'][$fieldName];
    } else {
        $fields[$fieldName]['value'] = isset($field['default']) ? $field['default'] : '';
    }
}

$smarty->assign('fields', $fields);
