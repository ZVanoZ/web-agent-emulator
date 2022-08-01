CREATE TABLE IF NOT EXISTS monolog$errors
(
    id INTEGER
        PRIMARY KEY AUTOINCREMENT,
    time INTEGER UNSIGNED
        NOT NULL,
    trace_id VARCHAR(255)
        NOT NULL,
    err_code VARCHAR(10),
    err_file VARCHAR(1024),
    err_line INT,
    err_level INT,
    err_message LONGTEXT,
    context LONGTEXT
)