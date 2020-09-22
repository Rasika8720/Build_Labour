@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-9 ml-2">
            <user-job-summary></user-job-summary>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/jobs/applied-to.js') }}"></script>
@endpush
