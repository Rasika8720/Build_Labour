@extends('layouts.admin')

@section('content')
    <export></export>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin/exports.js') }}"></script>
@endpush