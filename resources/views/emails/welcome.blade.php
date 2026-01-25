<x-mail::message>
# Welcome, {{ $user->name }}!

Thank you for registering with {{ config('app.name') }}.

Your account has been successfully created and you're ready to start your programming puzzle journey.

<x-mail::button :url="config('app.url')">
Get Started
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
