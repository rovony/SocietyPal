@extends('layouts.app')

@section('content')

@livewire('owner.owner-detail', ['userId' => $owner->id])

@endsection