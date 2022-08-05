<?php

namespace ZVanoZ\BaseApiServer\Action\Json\Journal;

use ZVanoZ\BaseApiServer\{AppInterface, ModuleError, Response\JsonResponse};
use PDO;
use PDOStatement;

class GetListAction
    extends AbstractAction
{
    function normalizeParams(): void
    {
        foreach ($this->bindParams as $name => $value) {
            if (in_array($name, ['id', 'trace_id', 'time'])) {
                $this->whereParams[] = "$name = :$name";
            } else if ($name === 'timeFrom') {
                $this->whereParams[] = "time >= :$name";
            } else if ($name === 'timeTo') {
                $this->whereParams[] = "timeTo <= :$name";
            } else {
                throw (new ModuleError('Invalid param'))
                    ->addContext('paramName', $name)
                    ->addContext('paramValue', $value);
            }
        }
    }

    function createStatement(PDO $db): PDOStatement
    {
        $sql = <<<SQL
SELECT t.id, t.trace_id, t.time, t.message  
from monolog\$journal t 
SQL;
        $where = implode(' AND ', $this->whereParams);
        if(!empty($where)){
            $sql .= ' WHERE ' . $where;
        }

        $result = $this->db->prepare($sql);
        return $result;
    }

    function executeStatement(PDOStatement $statement)
    {
        $statement->execute($this->bindParams);
        $data = $statement->fetchAll();
        return $data;
    }


}