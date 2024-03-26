<?php

namespace Doctrine\DBAL\Id;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\LockMode;
use Doctrine\Deprecations\Deprecation;
use Throwable;

use function array_change_key_case;
use function assert;
use function is_int;

use const CASE_LOWER;


class TableGenerator
{
    private Connection $conn;

    
    private $generatorTableName;

    
    private array $sequences = [];

    
    public function __construct(Connection $conn, $generatorTableName = 'sequences')
    {
        Deprecation::trigger(
            'doctrine/dbal',
            'https:
            'The TableGenerator class is is deprecated.',
        );

        if ($conn->getDriver() instanceof Driver\PDO\SQLite\Driver) {
            throw new Exception('Cannot use TableGenerator with SQLite.');
        }

        $this->conn = DriverManager::getConnection(
            $conn->getParams(),
            $conn->getConfiguration(),
            $conn->getEventManager(),
        );

        $this->generatorTableName = $generatorTableName;
    }

    
    public function nextValue($sequence)
    {
        if (isset($this->sequences[$sequence])) {
            $value = $this->sequences[$sequence]['value'];
            $this->sequences[$sequence]['value']++;
            if ($this->sequences[$sequence]['value'] >= $this->sequences[$sequence]['max']) {
                unset($this->sequences[$sequence]);
            }

            return $value;
        }

        $this->conn->beginTransaction();

        try {
            $platform = $this->conn->getDatabasePlatform();
            $sql      = 'SELECT sequence_value, sequence_increment_by'
                . ' FROM ' . $platform->appendLockHint($this->generatorTableName, LockMode::PESSIMISTIC_WRITE)
                . ' WHERE sequence_name = ? ' . $platform->getWriteLockSQL();
            $row      = $this->conn->fetchAssociative($sql, [$sequence]);

            if ($row !== false) {
                $row = array_change_key_case($row, CASE_LOWER);

                $value = $row['sequence_value'];
                $value++;

                assert(is_int($value));

                if ($row['sequence_increment_by'] > 1) {
                    $this->sequences[$sequence] = [
                        'value' => $value,
                        'max' => $row['sequence_value'] + $row['sequence_increment_by'],
                    ];
                }

                $sql  = 'UPDATE ' . $this->generatorTableName . ' ' .
                       'SET sequence_value = sequence_value + sequence_increment_by ' .
                       'WHERE sequence_name = ? AND sequence_value = ?';
                $rows = $this->conn->executeStatement($sql, [$sequence, $row['sequence_value']]);

                if ($rows !== 1) {
                    throw new Exception('Race-condition detected while updating sequence. Aborting generation');
                }
            } else {
                $this->conn->insert(
                    $this->generatorTableName,
                    ['sequence_name' => $sequence, 'sequence_value' => 1, 'sequence_increment_by' => 1],
                );
                $value = 1;
            }

            $this->conn->commit();
        } catch (Throwable $e) {
            $this->conn->rollBack();

            throw new Exception(
                'Error occurred while generating ID with TableGenerator, aborted generation: ' . $e->getMessage(),
                0,
                $e,
            );
        }

        return $value;
    }
}
