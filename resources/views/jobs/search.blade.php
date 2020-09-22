@extends('layouts.app')

@section('content')
    <job-search></job-search>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="profile-item-2">
                <job-posts></job-posts>
            </div>
        </div>
        <div class="col-md-4">
            <job-saved></job-saved>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/jobs/search.js') }}"></script>
@endpush
