@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center chat-system">
            <user-component user-id="<?=$requested_id?>"></user-component>
            <chat></chat>
        </div>
    </div>
@endsection

@push('scripts')
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

    <script src="{{ asset('js/chat/chat.js') }}"></script>
@endpush
