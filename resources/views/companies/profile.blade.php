@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <load-company company-id="{{ $company_id }}"></load-company>
        <div class="col-md-3">
            <company-profile company-id="{{ $company_id }}" viewer_type="{{ $viewer_type }}"></company-profile>
        </div>
        <div class="col-md-6">
            <company-browse viewer_type="{{ $viewer_type }}"></company-browse>
            <company-posts company-id="{{ $company_id }}" v-if="false"></company-posts>
            <company-jobs company-id="{{ $company_id }}" viewer_type="{{ $viewer_type }}"></company-jobs>
        </div>
        <div class="col-md-3">
            @if ($viewer_type != 'viewer')
                <company-most-recent-applicants company-id="{{ $company_id }}"></company-most-recent-applicants>
            @endif
            <ads></ads>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/companies/profile.js') }}"></script>
@endpush
