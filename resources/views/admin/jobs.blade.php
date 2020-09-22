@extends('layouts.admin')

@section('content')
    <datatable  title="Jobs"
                fetch-url="{{ route('jobs.table') }}"
                :columns="['id','company', 'title', 'location', 'exp_level',
                            'contract_type', 'salary', 'reports_to']"
                modal-name="Job">
    </datatable>
@endsection
