@php /** @var App\Invitation $invitation */ @endphp

<p>You invited to promote you concerts on TicketBeast!</p>
<p>
    Visit this link to create your account
    <a href="{{ url("/invitations/{$invitation->code}") }}">{{ url("/invitations/{$invitation->code}") }}</a>
</p>

