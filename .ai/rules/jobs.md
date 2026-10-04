---
paths:
  - 'app/Jobs/**'
---

# Jobs

## Use Horizon queue names

Assign every job a queue Horizon supervisors actually consume. Use a name from the supervisor `queue` list in `config/horizon.php`, resolved through the matching `queues` entry in `config/queue.php`. Do not use `default`; that queue is not worked.
