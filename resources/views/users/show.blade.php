@extends('layouts.app')

@section('content')

@livewire('user.user-detail', ['userId' => $user->id])

@endsection