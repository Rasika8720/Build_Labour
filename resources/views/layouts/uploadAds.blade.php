@extends('layouts.app')

@section('content2')
    
    <upload-ads :ad_id="{{ $ad_id }}" :upload="{{  $uploadId }}"></upload-ads>

@endsection

@push('scripts')
    <script src="{{ asset('js/users/profile.js') }}"></script>
@endpush
