@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <load-job company-id="{{ $company_id }}"></load-job>
        <div class="col-md-3">
            <create-job :creating="true" company-id="{{ $company_id }}" :preview-btn="true"></create-job>
        </div>
        <div class="col-md-9 ml-2">
            <!-- <transition appear appear-active-class="appear-slide-enter-active"> -->
                <new-job-details></new-job-details>
            <!-- </transition> -->
            <!-- <transition appear appear-active-class="appear-slide-enter-active"> -->
                <new-job-requirements></new-job-requirements>
            <!-- </transition> -->
            <!-- <transition appear appear-active-class="appear-slide-enter-active"> -->
                <new-job-responsibilities></new-job-responsibilities>
            <!-- </transition> -->
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/jobs/post.js') }}"></script>
@endpush
