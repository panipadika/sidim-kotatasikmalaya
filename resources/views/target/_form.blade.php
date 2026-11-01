@csrf
@php
    $target = $target ?? null;
@endphp
<style>
    .target-form { width: 100%; max-width: 860px; margin: 0 auto; background: var(--card-bg); border: 1px solid var(--border); border-radius: 16px; box-shadow: var(--shadow); overflow: hidden; }
    .form-header { padding: 24px 28px 20px; border-bottom: 1px solid #e6edf4; background: linear-gradient(110deg, #fffaf0 0%, #f0f9f4 100%); }
    .form-header h1 { margin: 0 0 6px; font-size: 1.5rem; letter-spacing: -.03em; }
    .form-header p { margin: 0; color: var(--text-soft); font-size: .86rem; line-height: 1.5; }
    .form-content { padding: 26px 28px 24px; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px 14px; }
    .field { display: flex; flex-direction: column; gap: 7px; }
    .field.full { grid-column: 1 / -1; }
    label { font-size: .82rem; font-weight: 800; color: var(--text-main); }
    label span { color: var(--text-soft); font-weight: 400; }
    input, select, textarea { width: 100%; border: 1px solid #d8e2ee; border-radius: 9px; padding: 11px 12px; font: inherit; font-size: .86rem; color: var(--text-main); background: #fbfdff; transition: border-color .15s ease, box-shadow .15s ease, background .15s ease; }
    input:focus, select:focus, textarea:focus { outline: 0; border-color: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, .15); background: #fff; }
    textarea { resize: vertical; min-height: 96px; line-height: 1.5; }
    small { color: #b4232d; font-size: .76rem; }
    .form-actions { display: flex; justify-content: flex-end; align-items: center; gap: 10px; margin-top: 24px; padding-top: 18px; border-top: 1px solid #e8eef4; }
    .form-actions a, .form-actions .cancel-btn { padding: 10px 13px; color: var(--text-soft); text-decoration: none; font-size: .84rem; font-weight: 700; background: transparent; border: 0; cursor: pointer; font-family: inherit; }
    .form-actions a:hover, .form-actions .cancel-btn:hover { color: var(--text-main); }
    .submit-btn { display: inline-flex; align-items: center; justify-content: center; gap: 7px; border: 0; border-radius: 9px; padding: 11px 16px; background: #10b981; color: #ffffff; font-size: .84rem; font-weight: 800; cursor: pointer; box-shadow: 0 7px 14px rgba(16, 185, 129, .2); }
    .submit-btn:hover { background: #059669; }
    @media (max-width: 650px) {
        .form-grid { grid-template-columns: 1fr; }
        .field.full { grid-column: auto; }
        .form-header, .form-content { padding-left: 16px; padding-right: 16px; }
        .form-actions { align-items: stretch; flex-direction: column-reverse; }
        .form-actions a, .form-actions .cancel-btn, .submit-btn { width: 100%; justify-content: center; text-align: center; }
    }
</style>
<div class="target-form">
    <div class="form-header">
        <h1>{{ isset($target) ? 'Edit Data Target' : 'Tambah Data Target' }}</h1>
        <p>{{ isset($target) ? 'Perbarui target indikator pembangunan.' : 'Masukkan target indikator pembangunan secara manual.' }}</p>
    </div>

    <div class="form-content">
        <div class="form-grid">
            <div class="field full">
                <label for="indikator_id">Indikator</label>
                <select id="indikator_id" name="indikator_id" required autofocus>
                    <option value="">Pilih indikator</option>
                    @foreach($indikators as $indikator)
                        <option value="{{ $indikator->id }}" data-satuan="{{ $indikator->satuan ?? '' }}" @selected((string) old('indikator_id', $target->indikator_id ?? '') === (string) $indikator->id)>
                            {{ $indikator->nama_indikator }}
                        </option>
                    @endforeach
                </select>
                @error('indikator_id')<small>{{ $message }}</small>@enderror
            </div>

            <div class="field">
                <label for="satuan">Satuan</label>
                <input id="satuan" type="text" value="{{ old('satuan', $target?->indikator?->satuan ?? '') }}" readonly>
            </div>

            <div class="field">
                <label for="tahun">Tahun</label>
                <input id="tahun" name="tahun" type="number" min="2005" max="2100" value="{{ old('tahun', $target->tahun ?? '') }}" placeholder="Contoh: 2025" required>
                @error('tahun')<small>{{ $message }}</small>@enderror
            </div>

            <div class="field">
                <label for="jenis_target">Jenis target</label>
                @php
                    $defaultTypes = ['RPJMD', 'RKPD', 'RPJPD'];
                    $currentJenis = old('jenis_target', $target->jenis_target ?? '');
                    $isCustom = !empty($currentJenis) && !in_array($currentJenis, $defaultTypes, true);
                    $selectedDropdownVal = $isCustom ? 'Lainnya' : $currentJenis;
                @endphp
                <select id="jenis_target" name="jenis_target" required>
                    <option value="">Pilih jenis target</option>
                    @foreach($defaultTypes as $jenis)
                        <option value="{{ $jenis }}" @selected($selectedDropdownVal === $jenis)>{{ $jenis }}</option>
                    @endforeach
                    <option value="Lainnya" @selected($selectedDropdownVal === 'Lainnya')>Lainnya</option>
                </select>
                @error('jenis_target')<small>{{ $message }}</small>@enderror
            </div>

            <div class="field jenis-target-custom-field" style="{{ ($selectedDropdownVal === 'Lainnya') ? 'display: flex;' : 'display: none;' }}">
                <label for="jenis_target_custom">Nama Jenis Target Baru</label>
                <input type="text" id="jenis_target_custom" name="jenis_target_custom" value="{{ old('jenis_target_custom', $isCustom ? $currentJenis : '') }}" placeholder="Contoh: Renstra, SPM, dll." maxlength="50">
                @error('jenis_target_custom')<small>{{ $message }}</small>@enderror
            </div>

            <div class="field">
                <label for="periode">Periode</label>
                <input id="periode" name="periode" value="{{ old('periode', $target->periode ?? '') }}" placeholder="Contoh: Tahunan / 2025-2029" required maxlength="100">
                @error('periode')<small>{{ $message }}</small>@enderror
            </div>

            <div class="field">
                <label for="nilai_target">Nilai target</label>
                <input id="nilai_target" name="nilai_target" type="text" inputmode="decimal" value="{{ old('nilai_target_range', old('nilai_target', $target?->nilai_target_range ?? ($target?->nilai_target !== null ? number_format((float) $target->nilai_target, 2, ',', '') : ''))) }}" placeholder="Contoh: 22,08 atau 9,2-10,16">
                @error('nilai_target')<small>{{ $message }}</small>@enderror
            </div>

            <div class="field full">
                <label for="sumber">Sumber <span>(opsional)</span></label>
                <input id="sumber" name="sumber" value="{{ old('sumber', $target->sumber ?? '') }}" maxlength="255">
                @error('sumber')<small>{{ $message }}</small>@enderror
            </div>

            <div class="field full">
                <label for="keterangan">Keterangan <span>(opsional)</span></label>
                <textarea id="keterangan" name="keterangan">{{ old('keterangan', $target->keterangan ?? '') }}</textarea>
                @error('keterangan')<small>{{ $message }}</small>@enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="button" class="cancel-btn" data-close-modal onclick="if(!this.closest('[data-modal]')){ window.location.href='{{ route('target.index') }}'; }">Batal</button>
            <button class="submit-btn" type="submit">{{ isset($target) ? 'Simpan perubahan' : 'Simpan target' }}</button>
        </div>
    </div>
</div>

<script>
    (() => {
        const scripts = document.getElementsByTagName('script');
        const currentScript = scripts[scripts.length - 1];
        const container = currentScript.closest('form') || currentScript.closest('.target-form');
        if (!container) return;

        const indicatorSelect = container.querySelector('select[name="indikator_id"]');
        const unitInput = container.querySelector('#satuan, input[name="satuan"]');

        if (indicatorSelect && unitInput) {
            const syncUnit = () => {
                const selected = indicatorSelect.selectedOptions[0];
                unitInput.value = selected?.dataset.satuan || '';
            };

            indicatorSelect.addEventListener('change', syncUnit);
            syncUnit();
        }

        const jenisSelect = container.querySelector('select[name="jenis_target"]');
        const customField = container.querySelector('.jenis-target-custom-field');
        const customInput = container.querySelector('#jenis_target_custom');

        if (jenisSelect && customField && customInput) {
            const syncCustomField = (shouldFocus = false) => {
                const isCustom = jenisSelect.value === 'Lainnya';
                customField.style.display = isCustom ? 'flex' : 'none';
                customInput.required = isCustom;
                if (isCustom && shouldFocus) {
                    setTimeout(() => customInput.focus(), 50);
                }
            };
            jenisSelect.addEventListener('change', () => syncCustomField(true));
            syncCustomField(false);
        }
    })();
</script>
