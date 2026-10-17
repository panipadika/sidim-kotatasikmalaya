@csrf
<style>
    .capaian-form { width: 100%; max-width: 860px; margin: 0 auto; background: var(--card-bg); border: 1px solid var(--border); border-radius: 16px; box-shadow: var(--shadow); overflow: hidden; }
    .form-header { padding: 24px 28px 20px; border-bottom: 1px solid #e6edf4; background: linear-gradient(110deg, #fffaf0 0%, #f4faff 100%); }
    .form-header h1 { margin: 0 0 6px; font-size: 1.5rem; letter-spacing: -.03em; } .form-header p { margin: 0; color: var(--text-soft); font-size: .86rem; line-height: 1.5; }
    .form-content { padding: 26px 28px 24px; } .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px 14px; } .field { display: flex; flex-direction: column; gap: 7px; } .field.full { grid-column: 1 / -1; }
    label { font-size: .82rem; font-weight: 800; color: var(--text-main); } label span { color: var(--text-soft); font-weight: 400; }
    input, select, textarea { width: 100%; border: 1px solid #d8e2ee; border-radius: 9px; padding: 11px 12px; font: inherit; font-size: .86rem; color: var(--text-main); background: #fbfdff; transition: border-color .15s ease, box-shadow .15s ease, background .15s ease; } input:focus, select:focus, textarea:focus { outline: 0; border-color: #38bdf8; box-shadow: 0 0 0 3px rgba(14, 165, 233, .14); background: #fff; } textarea { resize: vertical; min-height: 96px; line-height: 1.5; }
    small { color: #b4232d; font-size: .76rem; } .form-actions { display: flex; justify-content: flex-end; align-items: center; gap: 10px; margin-top: 24px; padding-top: 18px; border-top: 1px solid #e8eef4; } .form-actions a, .form-actions .cancel-btn { padding: 10px 13px; color: var(--text-soft); text-decoration: none; font-size: .84rem; font-weight: 700; background: transparent; border: 0; cursor: pointer; font-family: inherit; } .form-actions a:hover, .form-actions .cancel-btn:hover { color: var(--text-main); } .submit-btn { display: inline-flex; align-items: center; gap: 7px; border: 0; border-radius: 9px; padding: 11px 16px; background: #38bdf8; color: #064e70; font-size: .84rem; font-weight: 800; cursor: pointer; box-shadow: 0 7px 14px rgba(14, 165, 233, .16); } .submit-btn:hover { background: #7dd3fc; }
    @media (max-width: 650px) { .form-grid { grid-template-columns: 1fr; } .field.full { grid-column: auto; } .form-header, .form-content { padding-left: 16px; padding-right: 16px; } .form-actions { align-items: stretch; flex-direction: column-reverse; } .form-actions a, .form-actions .cancel-btn, .submit-btn { width: 100%; justify-content: center; text-align: center; } }
</style>
<div class="capaian-form">
    <div class="form-header"><h1>{{ isset($capaian) ? 'Edit Data Capaian' : 'Tambah Data Capaian' }}</h1><p>{{ isset($capaian) ? 'Perbarui nilai capaian indikator.' : 'Masukkan nilai capaian indikator secara manual.' }}</p></div>
    @php
        $selectedIndicatorId = old('indikator_id', $capaian->indikator_id ?? '');
        $existingTarget = isset($capaian)
            ? ($capaian->indikator?->targets?->where('jenis_target', 'RKPD')->firstWhere('tahun', $capaian->tahun)
                ?? $capaian->indikator?->targets?->firstWhere('tahun', $capaian->tahun))
            : null;
        $targetValue = old('target', $existingTarget?->nilai_target_range ?? ($existingTarget?->nilai_target !== null ? number_format((float) $existingTarget->nilai_target, 2, ',', '') : ''));
        $achievementValue = old('nilai', $capaian->nilai ?? null);
        $achievementDecimals = is_numeric($achievementValue) && abs((float) $achievementValue - round((float) $achievementValue, 2)) >= 0.0000001 ? 3 : 2;
    @endphp
    <div class="form-content"><div class="form-grid">
        <div class="field full"><label for="indikator_id">Indikator</label><select id="indikator_id" name="indikator_id" required><option value="">Pilih indikator</option>@foreach($indikators as $indikator)<option value="{{ $indikator->id }}" data-satuan="{{ $indikator->satuan ?? '' }}" @selected((string)$selectedIndicatorId === (string)$indikator->id)>{{ $indikator->nama_indikator }}</option>@endforeach</select>@error('indikator_id')<small>{{ $message }}</small>@enderror</div>
        <div class="field"><label for="satuan">Satuan</label><input id="satuan" type="text" value="{{ old('satuan', $capaian->indikator->satuan ?? '') }}" readonly></div>
        <div class="field"><label for="tahun">Tahun</label><input id="tahun" name="tahun" type="number" min="2005" max="2100" value="{{ old('tahun', $capaian->tahun ?? '') }}" placeholder="Contoh: 2026" required>@error('tahun')<small>{{ $message }}</small>@enderror</div>
        <div class="field"><label for="target">Target</label><input id="target" name="target" type="text" inputmode="decimal" value="{{ $targetValue }}" placeholder="Contoh: 22,08 atau 9,2-10,16">@error('target')<small>{{ $message }}</small>@enderror</div>
        <div class="field"><label for="nilai">Capaian</label><input id="nilai" name="nilai" type="text" inputmode="decimal" value="{{ is_numeric($achievementValue) ? number_format((float) $achievementValue, $achievementDecimals, ',', '') : $achievementValue }}" placeholder="Contoh: 22,08">@error('nilai')<small>{{ $message }}</small>@enderror</div>
        <div class="field full"><label for="sumber">Sumber <span>(opsional)</span></label><input id="sumber" name="sumber" value="{{ old('sumber', $capaian->sumber ?? '') }}" maxlength="255">@error('sumber')<small>{{ $message }}</small>@enderror</div>
        <div class="field full"><label for="keterangan">Keterangan <span>(opsional)</span></label><textarea id="keterangan" name="keterangan">{{ old('keterangan', $capaian->keterangan ?? '') }}</textarea>@error('keterangan')<small>{{ $message }}</small>@enderror</div>
    </div><div class="form-actions"><button type="button" class="cancel-btn" data-close-modal onclick="if(!this.closest('[data-modal]')){ window.location.href='{{ route('capaian.index') }}'; }">Batal</button><button class="submit-btn" type="submit"><i class="bi bi-check2"></i> {{ isset($capaian) ? 'Simpan perubahan' : 'Simpan capaian' }}</button></div></div>
</div>
<script>
    (() => {
        const indicatorSelect = document.getElementById('indikator_id');
        const unitInput = document.getElementById('satuan');
        if (!indicatorSelect || !unitInput) return;
        const syncUnit = () => {
            unitInput.value = indicatorSelect.selectedOptions[0]?.dataset.satuan || '-';
        };
        indicatorSelect.addEventListener('change', syncUnit);
        syncUnit();
    })();
</script>
