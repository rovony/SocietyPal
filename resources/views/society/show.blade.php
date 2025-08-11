@extends('layouts.app')

@section('content')

@livewire('Society.SocietyDetail', ['slug' => $slug])

@endsection
