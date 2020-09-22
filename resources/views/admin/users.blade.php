@extends('layouts.admin')

@section('content')
    <photo-modal></photo-modal>
    
    <delete-modal></delete-modal>
    
    <datatable  title="Users"
                fetch-url="{{ route('users.table') }}"
                :columns="['full_name', 'id', 'email', 'role', 'address', 'sector', 'tier', 'willing to relocate','created_at']"
                modal-name="User">
    </datatable>
@endsection