<?php




$map = [
    ['Laminas\\Mail\\Protocol\\Smtp', 'Zend\\Mail\\Protocol\\Smtp'],
    ['Laminas\\Mail\\Protocol\\Imap', 'Zend\\Mail\\Protocol\\Imap'],
    ['Laminas\\Mail\\Message', 'Zend\\Mail\\Message'],
];

foreach ($map as $item) {
    $className = $item[0];
    $alias = $item[1];

    if (!class_exists($className)) {
        continue;
    }

    class_alias($className, $alias);
}
