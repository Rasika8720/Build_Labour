@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">

        <load-user user-id="{{ $user_id }}"></load-user>
        
        <div class="col-md-6 offset-md-3">
            <upload-json></upload-json>
        </div>
        
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/users/profile.js') }}"></script>
@endpush
