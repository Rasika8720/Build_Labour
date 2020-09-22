@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <load-job></load-job>
        <div class="col-md-3">
            <company-summary role="{{ $role }}" already_applied="{{ $already_applied }}" :is-preview-mode="{{ $isPreviewMode }}"></company-summary>

        </div>
        <div class="col-md-6">
            <job-details></job-details>
            <job-requirements></job-requirements>
            <job-responsibilities></job-responsibilities>
        </div>
        <div class="col-md-3">
            <ads></ads>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/jobs/view.js') }}"></script>
@endpush
