INSERT INTO monolog$errors (
    time,
    trace_id,
    err_code,
    err_file,
    err_line,
    err_level,
    err_message,
    context
) VALUES (
     :time,
     :trace_id,
     :err_code,
     :err_file,
     :err_line,
     :err_level,
     :err_message,
     :context
)