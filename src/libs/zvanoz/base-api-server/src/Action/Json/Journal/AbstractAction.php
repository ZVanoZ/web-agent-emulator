<?php

namespace ZVanoZ\BaseApiServer\Action\Json\Journal;

use ZVanoZ\BaseApiServer\{
    AppInterface,
    Response\JsonResponse
};
use PDO;
use PDOStatement;

abstract class AbstractAction
    extends \ZVanoZ\BaseApiServer\Action\Json\Http200Action
{
    protected PDO $db;
    protected array $bindParams = [];
    protected array $whereParams = [];
    public function __construct(
        PDO    $db,
        ?array $bindParams = null
    )
    {
        $this->db = $db;
        if(is_array($bindParams)){
            $this->bindParams = $bindParams;
        }
    }

    protected function normalizeParams():void
    {

    }
    abstract function createStatement(PDO $db):PDOStatement;
    abstract function executeStatement(PDOStatement $statement);

    public function execute(
        AppInterface $app
    ): JsonResponse
    {
        $this->normalizeParams();
        $stmt = $this->createStatement($this->db);
        $data = $this->executeStatement($stmt);

        $result = parent::execute($app);
        $result->setItems([
            'result' => $data
        ]);

        return $result;
    }
}