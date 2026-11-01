@extends('layouts.admin')
@section('title', 'Tambah Indikator')
@section('content')
<style>
    .indicator-form { width: 100%; max-width: none; min-height: calc(100vh - 76px); margin: 0; background: var(--card-bg); border: 1px solid var(--border); border-radius: 16px; box-shadow: var(--shadow); overflow: hidden; }
    .form-header { padding: 24px 30px 20px; border-bottom: 1px solid #e6edf4; background: linear-gradient(110deg, #fffaf0 0%, #f0f9f4 100%); }
    .form-header h1 { margin: 0 0 6px; font-size: 1.55rem; letter-spacing: -.03em; }
    .form-header p { margin: 0; color: var(--text-soft); font-size: .88rem; line-height: 1.5; }
    .indicator-form form { padding: 28px 30px 26px; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px 16px; }
    .field { display: flex; flex-direction: column; gap: 6px; }
    .field.full { grid-column: 1 / -1; }
    label { font-size: .82rem; font-weight: 700; color: var(--text-main); }
    label span { color: var(--text-soft); font-weight: 400; }
    input, select, textarea { width: 100%; border: 1px solid #d8e2ee; border-radius: 9px; padding: 11px 12px; font: inherit; font-size: .86rem; color: var(--text-main); background: #fbfdff; transition: border-color .2s, box-shadow .2s; }
    input:focus, select:focus, textarea:focus { outline: 0; border-color: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, .15); background: #fff; }
    textarea { resize: vertical; min-height: 108px; line-height: 1.5; }
    small { color: #b4232d; font-size: .76rem; }
    .form-actions { display: flex; justify-content: flex-end; align-items: center; gap: 10px; margin-top: 26px; padding-top: 18px; border-top: 1px solid #e8eef4; }
    .form-actions a { padding: 10px 13px; color: var(--text-soft); text-decoration: none; font-size: .84rem; font-weight: 700; }
    .form-actions a:hover { color: var(--text-main); }
    .submit-btn { border: 0; border-radius: 9px; padding: 11px 16px; background: #10b981; color: #ffffff; font-size: .84rem; font-weight: 800; cursor: pointer; box-shadow: 0 7px 14px rgba(16, 185, 129, .2); }
    .submit-btn:hover { background: #059669; }
    @media (max-width: 650px) { .form-grid { grid-template-columns: 1fr; } .field.full { grid-column: auto; } .form-header, .indicator-form form { padding-left: 16px; padding-right: 16px; } .form-actions { align-items: stretch; flex-direction: column-reverse; } .form-actions a, .submit-btn { width: 100%; text-align: center; } }
</style>
<div class="indicator-form"><div class="form-header"><h1>Tambah Indikator</h1><p>Masukkan informasi indikator dan hubungan induknya.</p></div><form method="POST" action="{{ route('indikator.store') }}">@include('indikator._form')</form></div>
@endsection
