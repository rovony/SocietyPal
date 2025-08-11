@extends('layouts.app')

@section('content')

@livewire('maintenance.maintenance-detail', ['maintenanceId' => $maintenance->id])

@endsection