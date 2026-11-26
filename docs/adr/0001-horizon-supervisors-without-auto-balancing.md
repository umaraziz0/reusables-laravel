- Updated At: 2026-11-26 15:19:57
- Reviewed By: anthropic/claude-opus-5-5-low

# Horizon supervisors without auto balancing

Horizon's `auto` balancing keeps at least `minProcesses` workers per queue, so one supervisor with 8 queues never ran fewer than 8 workers, whatever `maxProcesses` said. Typical project hosts are small, and most of these queues are empty most of the time, so `config/horizon.php` uses `balance => false` instead. The light supervisor's workers take jobs from all of its queues in the order of `config/queue.php` `queues` (emails and notifications first), and the total process count scales between `minProcesses` and `maxProcesses`. Exports run on their own supervisor so a long export can't hold up emails.

## Consequences

- Queue order in `config/queue.php` is a priority: a flood on an earlier queue delays later ones until the supervisor scales up. Move a queue that needs isolation to its own supervisor rather than switching back to `auto`.
- One `retry_after` (330 seconds) covers the redis connection, because it must exceed the longest supervisor timeout (exports, 300 seconds). A job whose worker dies without failing it is retried after 330 seconds rather than 90. A separate connection for exports would avoid that, but every export job would then have to choose that connection.
