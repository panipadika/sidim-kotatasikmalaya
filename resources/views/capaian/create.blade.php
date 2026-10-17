@extends('layouts.admin')
@section('title', 'Tambah Capaian')
@section('content')
<form method="POST" action="{{ route('capaian.store') }}">@include('capaian._form')</form>
@endsection
