<?php

namespace ZVanoZ\BaseApiServer\Action\Json\Journal;

use ZVanoZ\BaseApiServer\{
    AppInterface,
    Response\JsonResponse
};
use PDO;
use PDOStatement;

class GetItemAction
    extends AbstractAction
{
    function createStatement(PDO $db): PDOStatement
    {
        $sql = <<<SQL
SELECT * 
FROM monolog\$journal t
WHERE t.id = :id
SQL;

        $result = $this->db->prepare($sql);
        return $result;
    }

    function executeStatement(PDOStatement $statement)
    {
        $statement->execute($this->bindParams);
        $data = $statement->fetch(PDO::FETCH_ASSOC);
        if($data === false){
            $data = null;
        }
        return $data;
    }
}