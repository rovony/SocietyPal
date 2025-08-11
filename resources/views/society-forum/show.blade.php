@extends('layouts.app')

@section('content')

@livewire('forum.forum-detail', ['forumId' => $forum->id])

@endsection
