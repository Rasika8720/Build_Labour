@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">

        <load-user user-id="{{ $user_id }}"></load-user>
        <div class="col-md-3">
            <user-profile
            internal-role="{{$internal_role}}"></user-profile>
            <about-me></about-me>
            <ideal-role></ideal-role>
        </div>
        <div class="col-md-6">
            <employment></employment>
            <industry-skills></industry-skills>
            <education></education>
            <tickets></tickets>
        </div>
        <div class="col-md-3">
            <ads></ads>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/users/profile.js') }}"></script>
@endpush
