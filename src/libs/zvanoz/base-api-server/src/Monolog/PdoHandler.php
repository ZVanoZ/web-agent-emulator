<?php

namespace ZVanoZ\BaseApiServer\Monolog;

if (!class_exists('PDO')) {
    throw new Exception('class "PDO" is not exists');
}

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;
use PDO;
use PDOStatement;

class PdoHandler
    extends AbstractProcessingHandler
{
    private bool $initialized = false;
    private PDO $pdo;
    private PDOStatement $statement;

    public function __construct(
        PDO              $pdo,
        int|string|Level $level = Level::Debug,
        bool             $bubble = true
    )
    {
        $this->pdo = $pdo;
        parent::__construct($level, $bubble);
    }

    protected function write(
        LogRecord $record
    ): void
    {
        if (!$this->initialized) {
            $this->initialize();
        }
        $data = [
            'channel' => $record->channel,
            'level' => $record->level,
            'message' => $record->formatted,
            'time' => $record->datetime->format('U'),
        ];
        $this->statement->execute($data);
    }

    private function initialize()
    {
        $this->pdo->exec(
            <<<DDL
CREATE TABLE IF NOT EXISTS monolog 
(
    channel VARCHAR(255), 
    level INTEGER, 
    message LONGTEXT, 
    time INTEGER UNSIGNED
)
DDL
        );
        $this->statement = $this->pdo->prepare(
            <<<DDL
INSERT INTO monolog (
     channel, 
     level, 
     message, 
     time
) VALUES (
    :channel, 
    :level, 
    :message, 
    :time
)
DDL
        );

        $this->initialized = true;
    }
}