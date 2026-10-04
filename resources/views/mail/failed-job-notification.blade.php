<x-mail::message>
# Queue Job Failed

A queue job has failed in **{{ \Illuminate\Support\Facades\Config::string('app.env') }}**.

<x-mail::panel>
**Job Details**

- **Environment:** {{ \Illuminate\Support\Facades\Config::string('app.env') }}
- **Job Name:** {{ $jobName }}
- **Job UUID:** {{ $uuid ?? 'N/A' }}
- **Connection:** {{ $connectionName }}
- **Queue:** {{ $failedQueue }}
- **Failed At:** {{ $failedAt }}
@if($attempts)
- **Attempts:** {{ $attempts }}
@endif
</x-mail::panel>

<x-mail::panel>
**Exception**

- **Class:** `{{ $exceptionClass }}`
- **Message:** {{ $exceptionMessage }}
</x-mail::panel>

<x-mail::panel>
**Stack Trace**

```
{{ $stackTrace }}
```
</x-mail::panel>

<x-mail::button :url="\Illuminate\Support\Facades\Config::string('app.url')">
View Application
</x-mail::button>

Thanks,<br>
{{ \Illuminate\Support\Facades\Config::string('app.name') }}
</x-mail::message>
