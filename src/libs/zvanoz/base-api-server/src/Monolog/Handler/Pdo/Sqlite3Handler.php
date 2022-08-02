<?php

namespace ZVanoZ\BaseApiServer\Monolog\Handler\Pdo;

use ZVanoZ\BaseApiServer\Monolog\PdoHandler;

class Sqlite3Handler
    extends PdoHandler
{
    function initDb(): void
    {
        $ddl = file_get_contents(__DIR__ . '/Sqlite3Handler/create_table_errors.sql');
        $this->pdo->exec($ddl);

        $ddl = file_get_contents(__DIR__ . '/Sqlite3Handler/create_table_journal.sql');
        $this->pdo->exec($ddl);
    }

    function createStatement(): void
    {
        $dml = file_get_contents(__DIR__ . '/Sqlite3Handler/insert_into_errors.sql');
        $this->statementErrors = $this->pdo->prepare($dml);

        $dml = file_get_contents(__DIR__ . '/Sqlite3Handler/insert_into_journal.sql');
        $this->statementJornal = $this->pdo->prepare($dml);

    }
}