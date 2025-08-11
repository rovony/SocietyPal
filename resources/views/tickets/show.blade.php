@extends('layouts.app')

@section('content')

@livewire('ticket.ticket-detail', ['ticketId' => $ticket->id])

@endsection
