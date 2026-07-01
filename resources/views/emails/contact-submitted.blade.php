<x-mail::message>
# New Contact Message Received

You have received a new contact message from the website contact form.

**Details:**
* **Name:** {{ $data['name'] }}
* **Email:** [{{ $data['email'] }}](mailto:{{ $data['email'] }})
* **Phone Number:** {{ $data['phone'] }}
* **Subject:** {{ $data['subject'] }}

**Message:**
{{ $data['message'] }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
