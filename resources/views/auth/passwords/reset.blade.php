@extends('layouts.auth')

@section('content')
    <transition appear appear-active-class="show-enter-active">
        <reset-password></reset-password>
    </transition>
@endsection

@push('scripts')
    <script src="{{ asset('js/auth/reset_password.js') }}"></script>
@endpush