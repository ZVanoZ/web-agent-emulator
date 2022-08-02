CREATE TABLE IF NOT EXISTS monolog$journal
(
    id       INTEGER
        PRIMARY KEY AUTOINCREMENT,
    time     INTEGER UNSIGNED
        NOT NULL,
    trace_id VARCHAR(255)
        NOT NULL,
    message LONGTEXT,
    context LONGTEXT
)