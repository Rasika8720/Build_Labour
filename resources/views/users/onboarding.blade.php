@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <!-- <div class="col-md-6"> -->
            <!-- <user-profile></user-profile>
            <about-me></about-me>
            <ideal-role></ideal-role>
            <employment></employment>
            <industry-skills></industry-skills>
            <education></education>
            <tickets></tickets> -->
            <load-user user-id="{{ $user_id }}"></load-user>
            <onboarding></onboarding>
        <!-- </div> -->
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/users/onboarding.js') }}"></script>
@endpush
