INSERT INTO monolog$journal (
    time,
    trace_id,
    message,
    context
) VALUES (
    :time,
    :trace_id,
    :message,
    :context
)