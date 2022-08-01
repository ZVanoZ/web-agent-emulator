<?php

namespace ZVanoZ\BaseApiServer\Monolog;

use Monolog\Level;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;
use PDO;
use PDOStatement;

abstract class PdoHandler
    extends AbstractProcessingHandler
{
    protected bool $isInitialized = false;
    protected PDO $pdo;
    protected PDOStatement $statementErrors;
    protected PDOStatement $statementJornal;

    public function __construct(
        PDO              $pdo,
        int|string|Level $level = Level::Debug,
        bool             $bubble = true
    )
    {
        $this->pdo = $pdo;
        parent::__construct($level, $bubble);
    }

    protected function initialize(): void{
        if($this->isInitialized){
            return;
        }
        $this->initDb();
        $this->createStatement();
        $this->isInitialized = true;
    }
    abstract function initDb(): void;
    abstract function createStatement(): void;

    protected function write(
        LogRecord $record
    ): void
    {
        $this->initialize();
        if(!array_key_exists('className', $record->context)
        ){
            return;
        }
        $context = Context::createFromArray($record->context);
        $className = get_class($context);
        switch ($className){
            case ContextError::class:
                $this->writeError($record, $context);
                break;
            case ContextJournal::class:
                $this->writeJournal($record, $context);
                break;
            default:
                //$this->writeError($record);
        }
    }

    protected function writeError(
        LogRecord $record,
        Context $context
    )
    {
        $context = $record->context;
        $data = [
            'channel' => $record->channel,
            'trace_id' => $context['traceId'],
            'level' => $record->level->value,
            'message' => $record->formatted,
            'time' => $record->datetime->format('U'),
        ];
        $this->statementErrors->execute($data);

    }
    protected function writeJournal(
        LogRecord $record,
        ContextJournal $context
    )
    {
        $data = [
            'time' =>  $record->datetime->format('U'),
            'trace_id' => $context->traceId,
            'message' => $record->message,
            'context' => $context->toArray()
        ];
        $this->statementErrors->execute($data);
    }

}