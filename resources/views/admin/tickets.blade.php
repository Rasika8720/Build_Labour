@extends('layouts.admin')

@section('content')
    <datatable  title="Tickets"
                fetch-url="{{ route('tickets.table') }}"
                :columns="['id', 'ticket',  'description']"
                modal-name="Ticket">
    </datatable>
@endsection
