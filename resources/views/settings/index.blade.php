@extends('layouts.admin')

@section('title', 'Pengaturan Akun Admin')

@section('content')
<style>
    .settings-page {
        width: 100%;
        min-width: 0;
        max-width: 1080px;
        margin: 0 auto;
        padding-bottom: 24px;
    }

    .page-heading {
        padding: 18px 22px;
        margin-bottom: 16px;
        background: linear-gradient(110deg, #ffffff 0%, #f4faff 100%);
        border: 1px solid var(--border);
        border-radius: 14px;
        box-shadow: var(--shadow);
    }

    .heading-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: .72rem;
        font-weight: 700;
        color: #0369a1;
        background: #e0f2fe;
        border: 1px solid #bae6fd;
        padding: 3px 9px;
        border-radius: 6px;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .page-heading h1 {
        margin: 0 0 4px;
        font-size: 1.35rem;
        font-weight: 800;
        letter-spacing: -.02em;
        color: var(--text-main);
    }

    .page-heading p {
        margin: 0;
        color: var(--text-soft);
        font-size: .84rem;
        line-height: 1.4;
    }

    .settings-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
        gap: 16px;
        align-items: start;
    }

    .settings-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 14px;
        box-shadow: var(--shadow);
        overflow: hidden;
        transition: transform .15s ease, box-shadow .15s ease;
    }

    .settings-card:hover {
        box-shadow: 0 10px 28px rgba(15, 39, 71, 0.08);
    }

    .settings-card.full-width {
        grid-column: 1 / -1;
    }

    .card-header-custom {
        padding: 16px 20px;
        border-bottom: 1px solid #edf1f5;
        background: linear-gradient(110deg, #ffffff 0%, #f8fafc 100%);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .card-header-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .card-header-icon.blue {
        background: #e0f2fe;
        color: #0284c7;
    }

    .card-header-icon.amber {
        background: #fef3c7;
        color: #d97706;
    }

    .card-header-icon.purple {
        background: #f3e8ff;
        color: #7e22ce;
    }

    .card-header-title {
        min-width: 0;
    }

    .card-header-title h2 {
        margin: 0 0 2px;
        font-size: 1.05rem;
        font-weight: 800;
        letter-spacing: -.02em;
        color: var(--text-main);
    }

    .card-header-title p {
        margin: 0;
        font-size: .78rem;
        color: var(--text-soft);
    }

    .card-body-custom {
        padding: 20px;
    }

    .form-group {
        margin-bottom: 16px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-label {
        font-size: .8rem;
        font-weight: 700;
        color: var(--text-main);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
    }

    .input-wrapper .input-icon {
        position: absolute;
        left: 12px;
        color: #94a3b8;
        font-size: .92rem;
        pointer-events: none;
        transition: color .15s ease;
    }

    .input-wrapper input {
        width: 100%;
        height: 40px;
        border: 1px solid #d8e2ee;
        border-radius: 8px;
        padding: 0 12px 0 36px;
        color: var(--text-main);
        background: #fbfdff;
        font: inherit;
        font-size: .85rem;
        transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease;
    }

    .input-wrapper.has-toggle input {
        padding-right: 38px;
    }

    .input-wrapper input:focus {
        outline: 0;
        border-color: #38bdf8;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, .14);
        background: #ffffff;
    }

    .input-wrapper input:focus ~ .input-icon {
        color: #0284c7;
    }

    .toggle-pass-btn {
        position: absolute;
        right: 10px;
        background: transparent;
        border: 0;
        color: #94a3b8;
        font-size: .95rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px;
        border-radius: 6px;
        transition: color .15s ease, background-color .15s ease;
    }

    .toggle-pass-btn:hover {
        color: #334155;
        background-color: #f1f5f9;
    }

    .field-hint {
        font-size: .74rem;
        color: var(--text-soft);
        line-height: 1.35;
        margin-top: 2px;
    }

    .field-error {
        font-size: .76rem;
        color: #dc2626;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 4px;
        margin-top: 3px;
    }

    .card-footer-custom {
        padding: 14px 20px 18px;
        border-top: 1px solid #edf1f5;
        background: #fafcff;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
    }

    .btn-submit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        height: 38px;
        padding: 0 18px;
        border-radius: 8px;
        font-size: .84rem;
        font-weight: 700;
        border: 0;
        cursor: pointer;
        transition: all .15s ease;
        box-sizing: border-box;
    }

    .btn-primary-custom {
        background: #0284c7;
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(2, 132, 199, 0.25);
    }

    .btn-primary-custom:hover {
        background: #0369a1;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(2, 132, 199, 0.35);
    }

    .btn-amber-custom {
        background: #d97706;
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(217, 119, 6, 0.25);
    }

    .btn-amber-custom:hover {
        background: #b45309;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(217, 119, 6, 0.35);
    }

    .btn-purple-custom {
        background: #7e22ce;
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(126, 34, 206, 0.25);
    }

    .btn-purple-custom:hover {
        background: #6b21a8;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(126, 34, 206, 0.35);
    }

    .btn-outline-secondary {
        background: #ffffff;
        color: #475569;
        border: 1px solid #cbd5e1;
    }

    .btn-outline-secondary:hover {
        background: #f1f5f9;
        color: #1e293b;
    }

    .admin-info-badge {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 10px;
        margin-bottom: 16px;
    }

    .admin-info-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #075c3a 0%, #16a34a 100%);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        font-weight: 800;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(7, 92, 58, 0.25);
    }

    .admin-info-text {
        min-width: 0;
    }

    .admin-info-text .name {
        font-weight: 800;
        color: #14532d;
        font-size: .88rem;
    }

    .admin-info-text .role {
        font-size: .74rem;
        color: #166534;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* URL Khusus Card Styles */
    .url-preview-card {
        padding: 14px 18px;
        background: #faf5ff;
        border: 1px solid #e9d5ff;
        border-radius: 10px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
    }

    .url-preview-card .url-content {
        display: flex;
        flex-direction: column;
        gap: 3px;
        min-width: 0;
    }

    .url-preview-card .url-label {
        font-size: .72rem;
        font-weight: 800;
        color: #7e22ce;
        text-transform: uppercase;
        letter-spacing: .04em;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .url-preview-card .url-display {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: .92rem;
        font-weight: 700;
        color: #4c1d95;
        word-break: break-all;
    }

    .btn-copy-url {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #ffffff;
        color: #7e22ce;
        border: 1px solid #d8b4fe;
        border-radius: 8px;
        padding: 8px 14px;
        font-size: .8rem;
        font-weight: 700;
        cursor: pointer;
        transition: all .15s ease;
        white-space: nowrap;
    }

    .btn-copy-url:hover {
        background: #f3e8ff;
        border-color: #c084fc;
        transform: translateY(-1px);
    }

    .url-input-group {
        display: flex;
        align-items: stretch;
        width: 100%;
        border: 1px solid #d8e2ee;
        border-radius: 8px;
        background: #fbfdff;
        overflow: hidden;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .url-input-group:focus-within {
        border-color: #a855f7;
        box-shadow: 0 0 0 3px rgba(168, 85, 247, .15);
        background: #fff;
    }

    .url-prefix {
        padding: 0 14px;
        background: #f1f5f9;
        color: #64748b;
        font-size: .85rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        border-right: 1px solid #e2e8f0;
        user-select: none;
        white-space: nowrap;
    }

    .url-input-group input {
        flex: 1;
        height: 40px;
        border: 0;
        outline: 0;
        padding: 0 14px;
        font: inherit;
        font-size: .88rem;
        font-weight: 700;
        color: var(--text-main);
        background: transparent;
    }

    .url-notice-box {
        margin-top: 8px;
        padding: 10px 14px;
        background: #fffbeb;
        border: 1px solid #fef3c7;
        border-radius: 8px;
        font-size: .76rem;
        color: #92400e;
        line-height: 1.45;
        display: flex;
        gap: 8px;
        align-items: flex-start;
    }

    .url-notice-box i {
        color: #d97706;
        font-size: .95rem;
        flex-shrink: 0;
        margin-top: 1px;
    }

    @media (max-width: 768px) {
        .settings-grid {
            grid-template-columns: 1fr;
        }
        .btn-submit {
            width: 100%;
        }
        .card-footer-custom {
            flex-direction: column-reverse;
        }
        .url-preview-card {
            flex-direction: column;
            align-items: stretch;
        }
        .btn-copy-url {
            justify-content: center;
        }
    }
</style>

<div class="settings-page">
    <div class="page-heading">
        <div class="heading-badge"><i class="bi bi-shield-lock-fill"></i> Keamanan & Akun</div>
        <h1>Pengaturan Akun Admin</h1>
        <p>Kelola nama tampilan, username login admin, URL khusus akses login, dan perbarui kata sandi untuk mengamankan akses ke dashboard.</p>
    </div>

    <div class="settings-grid">
        <!-- Kartu 1: Profil & Username -->
        <div class="settings-card">
            <div class="card-header-custom">
                <div class="card-header-icon blue">
                    <i class="bi bi-person-gear"></i>
                </div>
                <div class="card-header-title">
                    <h2>Username & Profil</h2>
                    <p>Ubah nama tampilan dan username login admin</p>
                </div>
            </div>

            <form action="{{ route('settings.profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card-body-custom">
                    <div class="admin-info-badge">
                        <div class="admin-info-avatar">
                            {{ strtoupper(substr($admin->name, 0, 1)) }}
                        </div>
                        <div class="admin-info-text">
                            <div class="name">{{ $admin->name }}</div>
                            <div class="role"><i class="bi bi-shield-check"></i> Administrator Sistem (Username: {{ $admin->username }})</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="admin-name">Nama Admin</label>
                        <div class="input-wrapper">
                            <i class="bi bi-person input-icon"></i>
                            <input type="text" id="admin-name" name="name" value="{{ old('name', $admin->name) }}" placeholder="Masukkan nama admin" required>
                        </div>
                        @error('name')
                            <div class="field-error"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="admin-username">Username Login</label>
                        <div class="input-wrapper">
                            <i class="bi bi-at input-icon"></i>
                            <input type="text" id="admin-username" name="username" value="{{ old('username', $admin->username) }}" placeholder="Masukkan username baru" required autocomplete="username">
                        </div>
                        <div class="field-hint">Gunakan username ini saat masuk melalui URL khusus admin.</div>
                        @error('username')
                            <div class="field-error"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="card-footer-custom">
                    <button type="submit" class="btn-submit btn-primary-custom">
                        <i class="bi bi-check2-circle"></i> Simpan Username
                    </button>
                </div>
            </form>
        </div>

        <!-- Kartu 2: Ubah Password -->
        <div class="settings-card">
            <div class="card-header-custom">
                <div class="card-header-icon amber">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <div class="card-header-title">
                    <h2>Ubah Password</h2>
                    <p>Perbarui kata sandi akun admin secara berkala</p>
                </div>
            </div>

            <form action="{{ route('settings.password.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card-body-custom">
                    <div class="form-group">
                        <label class="form-label" for="current_password">Password Saat Ini</label>
                        <div class="input-wrapper has-toggle">
                            <i class="bi bi-key input-icon"></i>
                            <input type="password" id="current_password" name="current_password" placeholder="Masukkan password saat ini" required autocomplete="current-password">
                            <button type="button" class="toggle-pass-btn" data-toggle-target="current_password" title="Lihat password" aria-label="Lihat password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <div class="field-error"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="new_password">Password Baru</label>
                        <div class="input-wrapper has-toggle">
                            <i class="bi bi-lock input-icon"></i>
                            <input type="password" id="new_password" name="password" placeholder="Masukkan password baru (min. 8 karakter)" required autocomplete="new-password">
                            <button type="button" class="toggle-pass-btn" data-toggle-target="new_password" title="Lihat password" aria-label="Lihat password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="field-hint">Kata sandi baru minimal 8 karakter.</div>
                        @error('password')
                            <div class="field-error"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Konfirmasi Password Baru</label>
                        <div class="input-wrapper has-toggle">
                            <i class="bi bi-shield-check input-icon"></i>
                            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password baru" required autocomplete="new-password">
                            <button type="button" class="toggle-pass-btn" data-toggle-target="password_confirmation" title="Lihat password" aria-label="Lihat password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-footer-custom">
                    <button type="submit" class="btn-submit btn-amber-custom">
                        <i class="bi bi-key-fill"></i> Perbarui Password
                    </button>
                </div>
            </form>
        </div>

        <!-- Kartu 3: Ubah URL Khusus Login Admin -->
        <div class="settings-card full-width">
            <div class="card-header-custom">
                <div class="card-header-icon purple">
                    <i class="bi bi-link-45deg"></i>
                </div>
                <div class="card-header-title">
                    <h2>URL Khusus Login Admin</h2>
                    <p>Ubah alamat URL rahasia untuk masuk ke portal admin (default: <code>/admin-dashboard</code>)</p>
                </div>
            </div>

            <div class="card-body-custom">
                <div class="url-preview-card">
                    <div class="url-content">
                        <div class="url-label">
                            <i class="bi bi-shield-check"></i> URL Login Admin Aktif Saat Ini
                        </div>
                        <div class="url-display" id="full-login-url-text">{{ $fullLoginUrl }}</div>
                    </div>
                    <button type="button" class="btn-copy-url" id="copy-url-btn" data-url="{{ $fullLoginUrl }}">
                        <i class="bi bi-clipboard"></i> <span id="copy-btn-text">Salin URL</span>
                    </button>
                </div>

                <form action="{{ route('settings.login-url.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label class="form-label" for="login_path">Path / Slug URL Login Baru</label>
                        <div class="url-input-group">
                            <span class="url-prefix">{{ url('/') }}/</span>
                            <input type="text" id="login_path" name="login_path" value="{{ old('login_path', $currentLoginPath) }}" placeholder="masukkan-url-rahasia" required autocomplete="off">
                        </div>
                        <div class="field-hint">Hanya gunakan huruf (a-z), angka (0-9), strip (-), atau garis bawah (_). Minimal 3 karakter.</div>
                        @error('login_path')
                            <div class="field-error"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</div>
                        @enderror

                        <div class="url-notice-box">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <div>
                                <strong>Penting:</strong> Setelah URL diubah, URL login lama serta URL umum seperti <code>/admin</code> atau <code>/login</code> akan otomatis menghasilkan <strong>404 (Tidak Ditemukan)</strong>. Pastikan Anda menyalin atau mem-bookmark URL baru ini agar tidak lupa saat ingin login kembali.
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 18px;">
                        <button type="submit" class="btn-submit btn-purple-custom">
                            <i class="bi bi-shield-check"></i> Simpan URL Login Baru
                        </button>
                    </div>
                </form>
            </div>

            @if ($currentLoginPath !== 'admin-dashboard')
                <div class="card-footer-custom" style="justify-content: space-between;">
                    <div style="font-size: .8rem; color: var(--text-soft);">
                        URL saat ini bukan default. Anda dapat mengembalikannya ke <code>/admin-dashboard</code> kapan saja.
                    </div>
                    <form action="{{ route('settings.login-url.reset') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengembalikan URL login admin ke default (/admin-dashboard)?');">
                        @csrf
                        <button type="submit" class="btn-submit btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i> Kembalikan ke /admin-dashboard
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    // Toggle show/hide password
    document.querySelectorAll('[data-toggle-target]').forEach((button) => {
        button.addEventListener('click', () => {
            const targetId = button.getAttribute('data-toggle-target');
            const input = document.getElementById(targetId);
            if (!input) return;

            const icon = button.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                if (icon) {
                    icon.className = 'bi bi-eye-slash';
                }
                button.title = 'Sembunyikan password';
            } else {
                input.type = 'password';
                if (icon) {
                    icon.className = 'bi bi-eye';
                }
                button.title = 'Lihat password';
            }
        });
    });

    // Copy full login URL to clipboard
    const copyBtn = document.getElementById('copy-url-btn');
    if (copyBtn) {
        copyBtn.addEventListener('click', () => {
            const url = copyBtn.getAttribute('data-url');
            if (!url) return;

            navigator.clipboard.writeText(url).then(() => {
                const textSpan = document.getElementById('copy-btn-text');
                const icon = copyBtn.querySelector('i');
                if (textSpan) textSpan.textContent = 'Tersalin!';
                if (icon) icon.className = 'bi bi-check-lg';

                setTimeout(() => {
                    if (textSpan) textSpan.textContent = 'Salin URL';
                    if (icon) icon.className = 'bi bi-clipboard';
                }, 2000);
            }).catch(() => {
                // Fallback for browsers with restricted clipboard permission
                const tempInput = document.createElement('input');
                tempInput.value = url;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);

                const textSpan = document.getElementById('copy-btn-text');
                if (textSpan) textSpan.textContent = 'Tersalin!';
                setTimeout(() => {
                    if (textSpan) textSpan.textContent = 'Salin URL';
                }, 2000);
            });
        });
    }
</script>
@endsection
