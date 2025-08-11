@extends('layouts.app')

@section('content')
 
@livewire('visitors-management.visitor-approval', ['visitor' => $visitor])

@endsection
