@extends('layouts.admin')

@section('content')
    <datatable  title="Job Roles"
                fetch-url="{{ route('job-roles.table') }}"
                :columns="['id', 'role name']"
                modal-name="JobRole">
    </datatable>
@endsection
