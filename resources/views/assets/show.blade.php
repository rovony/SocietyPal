@extends('layouts.app')

@section('content')

@livewire('assets.asset-detail', ['assetId' => $asset->id])

@endsection