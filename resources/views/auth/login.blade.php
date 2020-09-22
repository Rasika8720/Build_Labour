@extends('layouts.auth')

@section('content')
    <transition appear appear-active-class="show-enter-active">
        <login></login>
    </transition>
@endsection

@push('scripts')
    <script src="{{ asset('js/auth/login.js') }}"></script>
@endpush