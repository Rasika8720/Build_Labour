@extends('layouts.auth')

@section('content')
    <transition appear appear-active-class="show-enter-active">
        <request-link></request-link>
    </transition>
@endsection

@push('scripts')
    <script src="{{ asset('js/auth/request_link.js') }}"></script>
@endpush