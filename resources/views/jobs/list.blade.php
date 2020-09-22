@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-3">
            <create-job :creating="false"></create-job>
        </div>
        <div class="col-md-9 ml-2">
            <job-summary company-id="{{ $company_id }}"></job-summary>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/jobs/list.js') }}"></script>
@endpush