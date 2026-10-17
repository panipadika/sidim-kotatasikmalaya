@extends('layouts.admin')
@section('title', 'Edit Data Target')
@section('content')
<form method="POST" action="{{ route('target.update', $target) }}">@method('PUT') @include('target._form', ['target' => $target])</form>
@endsection
