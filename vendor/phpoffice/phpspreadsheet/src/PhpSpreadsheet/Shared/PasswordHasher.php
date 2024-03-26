<?php

namespace PhpOffice\PhpSpreadsheet\Shared;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\Protection;

class PasswordHasher
{
    
    private static function getAlgorithm(string $algorithmName): string
    {
        if (!$algorithmName) {
            return '';
        }

        
        $mapping = [
            Protection::ALGORITHM_MD2 => 'md2',
            Protection::ALGORITHM_MD4 => 'md4',
            Protection::ALGORITHM_MD5 => 'md5',
            Protection::ALGORITHM_SHA_1 => 'sha1',
            Protection::ALGORITHM_SHA_256 => 'sha256',
            Protection::ALGORITHM_SHA_384 => 'sha384',
            Protection::ALGORITHM_SHA_512 => 'sha512',
            Protection::ALGORITHM_RIPEMD_128 => 'ripemd128',
            Protection::ALGORITHM_RIPEMD_160 => 'ripemd160',
            Protection::ALGORITHM_WHIRLPOOL => 'whirlpool',
        ];

        if (array_key_exists($algorithmName, $mapping)) {
            return $mapping[$algorithmName];
        }

        throw new Exception('Unsupported password algorithm: ' . $algorithmName);
    }

    
    private static function defaultHashPassword(string $pPassword): string
    {
        $password = 0x0000;
        $charPos = 1; 

        
        $chars = preg_split('
        foreach ($chars as $char) {
            $value = ord($char) << $charPos++; 
            $rotated_bits = $value >> 15; 
            $value &= 0x7fff; 
            $password ^= ($value | $rotated_bits);
        }

        $password ^= strlen($pPassword);
        $password ^= 0xCE4B;

        return strtoupper(dechex($password));
    }

    
    public static function hashPassword(string $password, string $algorithm = '', string $salt = '', int $spinCount = 10000): string
    {
        $phpAlgorithm = self::getAlgorithm($algorithm);
        if (!$phpAlgorithm) {
            return self::defaultHashPassword($password);
        }

        $saltValue = base64_decode($salt);
        $encodedPassword = mb_convert_encoding($password, 'UCS-2LE', 'UTF-8');

        $hashValue = hash($phpAlgorithm, $saltValue . $encodedPassword, true);
        for ($i = 0; $i < $spinCount; ++$i) {
            $hashValue = hash($phpAlgorithm, $hashValue . pack('L', $i), true);
        }

        return base64_encode($hashValue);
    }
}
