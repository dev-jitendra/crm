<?php


ob_start();
$result = array('success' => true, 'errorMsg' => '');

$installer->rebuild();

ob_clean();
echo json_encode($result);
