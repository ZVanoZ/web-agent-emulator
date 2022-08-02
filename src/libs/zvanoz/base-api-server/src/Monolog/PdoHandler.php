<?php

namespace ZVanoZ\BaseApiServer\Monolog;

use Monolog\Level;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;
use PDO;
use PDOStatement;
use ZVanoZ\BaseApiServer\Monolog\Context\ErrorContext;
use ZVanoZ\BaseApiServer\Monolog\Context\JournalContext;

abstract class PdoHandler
    extends AbstractHandler
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

    protected function initialize(): void
    {
        if ($this->isInitialized) {
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
        if (!array_key_exists('className', $record->context)
        ) {
            return;
        }
        $context = Context::createFromArray($record->context);
        $className = get_class($context);
        switch ($className) {
            case ErrorContext::class:
                $this->writeError($record, $context);
                break;
            case JournalContext::class:
                $this->writeJournal($record, $context);
                break;
            default:
                //$this->writeError($record);
        }
    }

    public function writeError(
        LogRecord $record,
        ErrorContext   $context
    ): void
    {
        $data = [
            'time' => $record->datetime->format('U'),
            'trace_id' => $context->traceId,
            'err_code' => $context->errCode,
            'err_file' => $context->errFile,
            'err_line' => $context->errLine,
            'err_level' => null,
            'err_message' => $context->errMessage,
            'context' => $context->__toString(),
        ];
        $this->statementErrors->execute($data);
    }

    public function writeJournal(
        LogRecord      $record,
        JournalContext $context
    ): void
    {
        $data = [
            'time' => $record->datetime->format('U'),
            'trace_id' => $context->traceId,
            'message' => $record->message,
            'context' => $context,
        ];
        $this->statementJornal->execute($data);
    }

}