@extends('layouts.admin')
@section('title', 'Tambah Data Target')
@section('content')
<form method="POST" action="{{ route('target.store') }}">@include('target._form', ['target' => $target ?? null])</form>
@endsection
