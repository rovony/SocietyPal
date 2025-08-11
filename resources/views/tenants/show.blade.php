@extends('layouts.app')

@section('content')

@livewire('tenants.tenant-detail', ['tenantId' => $tenant->id])

@endsection
