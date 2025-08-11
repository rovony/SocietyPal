@extends('layouts.app')

@section('content')

@livewire('forms.show-apartment-management', ['apartmentId' => $id])


@endsection
