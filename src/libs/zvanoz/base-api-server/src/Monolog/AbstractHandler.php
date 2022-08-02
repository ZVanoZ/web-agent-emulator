<?php

namespace ZVanoZ\BaseApiServer\Monolog;

use Monolog\Level;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;
use PDO;
use PDOStatement;
use ZVanoZ\BaseApiServer\Monolog\Context\ErrorContext;
use ZVanoZ\BaseApiServer\Monolog\Context\JournalContext;

abstract class AbstractHandler
    extends AbstractProcessingHandler
{
    protected bool $isInitialized = false;

    protected function initialize(): void{
        if($this->isInitialized){
            return;
        }
        // @NOTE: add your code here
        $this->isInitialized = true;
    }

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

    abstract function writeError(
        LogRecord $record,
        ErrorContext $context
    ):void;

    abstract function writeJournal(
        LogRecord $record,
        JournalContext $context
    ):void;
}