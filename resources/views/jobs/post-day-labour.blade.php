@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <load-job company-id="{{ $company_id }}"></load-job>
        <div class="col-md-3">
            <create-job :creating="true" company-id="{{ $company_id }}" :preview-btn="false"></create-job>
        </div>
        <div class="col-md-9 ml-2">
            <new-day-labour-details></new-day-labour-details>
            <new-day-labour-trades></new-day-labour-trades>
            <new-day-labour-requirements></new-day-labour-requirements>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/jobs/post-day-labour.js') }}"></script>
@endpush
