@extends('layouts.app')

@section('content')

@livewire('forms.show-tower-management', ['towerId' => $id])

@endsection

