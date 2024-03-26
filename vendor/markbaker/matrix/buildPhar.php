<?php

# required: PHP 5.3+ and zlib extension


if (ini_get('phar.readonly')) {
    echo "php.ini: set the 'phar.readonly' option to 0 to enable phar creation\n";
    exit(1);
}


$pharName = 'Matrix.phar';


$sourceDir = __DIR__ . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;


$metaData = array(
    'Author'      => 'Mark Baker <mark@lange.demon.co.uk>',
    'Description' => 'PHP Class for working with Matrix numbers',
    'Copyright'   => 'Mark Baker (c) 2013-' . date('Y'),
    'Timestamp'   => time(),
    'Version'     => '0.1.0',
    'Date'        => date('Y-m-d')
);


if (file_exists($pharName)) {
    echo "Removed: {$pharName}\n";
    unlink($pharName);
}

echo "Building phar file...\n";


$phar = new Phar($pharName, null, 'Matrix');
$phar->buildFromDirectory($sourceDir);
$phar->setStub(
<<<'EOT'
<?php
    spl_autoload_register(function ($className) {
        include 'phar:
    });

    try {
        Phar::mapPhar();
    } catch (PharException $e) {
        error_log($e->getMessage());
        exit(1);
    }

    include 'phar:

    __HALT_COMPILER();
EOT
);
$phar->setMetadata($metaData);
$phar->compressFiles(Phar::GZ);

echo "Complete.\n";

exit();
