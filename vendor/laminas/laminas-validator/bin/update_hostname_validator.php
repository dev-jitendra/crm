<?php

declare(strict_types=1);

use Laminas\Validator\Hostname;

require __DIR__ . '/../vendor/autoload.php';

const IANA_URL                        = 'https:
const LAMINAS_HOSTNAME_VALIDATOR_FILE = __DIR__ . '/../src/Hostname.php';

if (! file_exists(LAMINAS_HOSTNAME_VALIDATOR_FILE) || ! is_readable(LAMINAS_HOSTNAME_VALIDATOR_FILE)) {
    printf("Error: cannot read file '%s'%s", LAMINAS_HOSTNAME_VALIDATOR_FILE, PHP_EOL);
    exit(1);
}

if (! is_writable(LAMINAS_HOSTNAME_VALIDATOR_FILE)) {
    printf("Error: cannot update file '%s'%s", LAMINAS_HOSTNAME_VALIDATOR_FILE, PHP_EOL);
    exit(1);
}


$newFileContent = [];    
$insertDone     = false; 
$insertFinish   = false; 
$checkOnly      = isset($argv[1]) ? $argv[1] === '--check-only' : false;
$response       = getOfficialTLDs();

$currentFileContent = file(LAMINAS_HOSTNAME_VALIDATOR_FILE);

foreach ($currentFileContent as $line) {
    if ($insertDone === $insertFinish) {
        
        $newFileContent[] = $line;
    }

    if ($insertFinish) {
        continue;
    }

    if ($insertDone) {
        
        if (preg_match('/^\s+\];\s*$/', $line)) {
            $newFileContent[] = $line;
            $insertFinish     = true;
        }

        continue;
    }

    
    if (preg_match('/^\s+protected\s+\$validTlds\s+=\s+\[\s*$/', $line)) {
        $newFileContent = array_merge($newFileContent, getNewValidTlds($response));
        $insertDone     = true;
    }
}

if (! $insertDone) {
    printf('Error: cannot find line with "protected $validTlds"%s', PHP_EOL);
    exit(1);
}

if (! $insertFinish) {
    printf('Error: cannot find end of $validTlds declaration%s', PHP_EOL);
    exit(1);
}

if ($currentFileContent === $newFileContent) {
    printf('Nothing to do. Validator has no TLD changes.%s', PHP_EOL);
    exit(0);
}

if ($checkOnly) {
    printf(
        'TLDs must be updated, please run `php bin/update_hostname_validator.php` and push your changes%s',
        PHP_EOL
    );
    exit(1);
}

if (false === @file_put_contents(LAMINAS_HOSTNAME_VALIDATOR_FILE, $newFileContent)) {
    printf('Error: cannot write info file "%s"%s', LAMINAS_HOSTNAME_VALIDATOR_FILE, PHP_EOL);
    exit(1);
}

printf('Validator TLD file updated.%s', PHP_EOL);
exit(0);


function getOfficialTLDs(): string
{
    try {
        return file_get_contents(IANA_URL);
    } catch (Throwable $e) {
        printf(
            'Downloading the IANA TLD list failed: %s',
            $e->getMessage(),
        );

        exit(1);
    }
}


function getNewValidTlds(string $string): array
{
    $decodePunycode = getPunycodeDecoder();

    
    $newValidTlds = [];
    foreach (preg_grep('/^[^#]/', preg_split("#\r?\n#", $string)) as $line) {
        $newValidTlds [] = sprintf(
            "%s'%s',\n",
            str_repeat(' ', 8),
            $decodePunycode(strtolower($line))
        );
    }

    return $newValidTlds;
}


function getPunycodeDecoder()
{
    if (function_exists('idn_to_utf8')) {
        return function ($domain) {
            return idn_to_utf8($domain, 0, INTL_IDNA_VARIANT_UTS46);
        };
    }

    $hostnameValidator = new Hostname();
    $reflection        = new ReflectionClass($hostnameValidator::class);
    $decodePunyCode    = $reflection->getMethod('decodePunycode');

    return function ($encode) use ($hostnameValidator, $decodePunyCode) {
        if (strpos($encode, 'xn--') === 0) {
            return $decodePunyCode->invokeArgs($hostnameValidator, [substr($encode, 4)]);
        }
        return $encode;
    };
}
