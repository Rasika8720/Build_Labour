@extends('layouts.admin')

@section('content')
    <datatable title="Companies"
                fetch-url="{{ route('companies.table') }}"
                :columns="['id', 'name', 'created_at']"
                modal-name="Company">
    </datatable>
@endsection