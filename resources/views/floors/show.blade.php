@extends('layouts.app')

@section('content')

@livewire('forms.show-floor-management', ['floorId' => $id])

@endsection

