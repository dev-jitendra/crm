<?php


namespace Espo\ORM\QueryComposer;

use Espo\ORM\Query\LockTable as LockTableQuery;

use LogicException;

class MysqlQueryComposer extends BaseQueryComposer
{
    public function composeLockTable(LockTableQuery $query): string
    {
        $params = $query->getRaw();

        $entityType = $this->sanitize($params['table']);

        $table = $this->toDb($entityType);

        $mode = $params['mode'];

        if (empty($table)) {
            throw new LogicException();
        }

        if (!in_array($mode, [LockTableQuery::MODE_SHARE, LockTableQuery::MODE_EXCLUSIVE])) {
            throw new LogicException();
        }

        $sql = "LOCK TABLES " . $this->quoteIdentifier($table) . " ";

        $modeMap = [
            LockTableQuery::MODE_SHARE => 'READ',
            LockTableQuery::MODE_EXCLUSIVE => 'WRITE',
        ];

        $sql .= $modeMap[$mode];

        if (str_contains($table, '_')) {
            
            $sql .= ", " .
                $this->quoteIdentifier($table) . " AS " .
                $this->quoteIdentifier(lcfirst($entityType)) . " " . $modeMap[$mode];
        }

        return $sql;
    }

    public function composeUnlockTables(): string
    {
        return "UNLOCK TABLES";
    }

    protected function limit(string $sql, ?int $offset = null, ?int $limit = null): string
    {
        if (!is_null($offset) && !is_null($limit)) {
            $offset = intval($offset);
            $limit = intval($limit);

            $sql .= " LIMIT $offset, $limit";

            return $sql;
        }

        if (!is_null($limit)) {
            $limit = intval($limit);

            $sql .= " LIMIT $limit";

            return $sql;
        }

        return $sql;
    }
}
