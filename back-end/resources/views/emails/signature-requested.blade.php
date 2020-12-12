@component('mail::message')
# Document waiting for signature

Open the link below to access the document that needs to be signed by you

@component('mail::button', ['url' => $url, 'color' => 'success'])
Open document
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
