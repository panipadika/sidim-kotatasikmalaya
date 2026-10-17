@extends('layouts.admin')
@section('title', 'Edit Capaian')
@section('content')
<form method="POST" action="{{ route('capaian.update', $capaian) }}">@method('PUT') @include('capaian._form', ['capaian' => $capaian])</form>
@endsection
