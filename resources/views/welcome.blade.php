<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Makro Kota Tasikmalaya</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1f6b45;
            --primary-dark: #073b2a;
            --text: #16352a;
            --muted: #648074;
            --white: #ffffff;
        }

        html {
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE / Edge */
        }

        html::-webkit-scrollbar,
        body::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
            width: 0;
            height: 0;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background:
                linear-gradient(rgba(255, 255, 255, .28), rgba(255, 255, 255, .28)),
                url('{{ asset('latar1.png') }}') center / cover no-repeat fixed;
            color: var(--text);
            overflow-x: hidden;
        }

        .page {
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px 20px;
            position: relative;
            overflow-x: hidden;
            animation: page-enter .7s ease both;
        }

        .page::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: #ffffff;
        }

        .login-layout {
            width: min(100%, 1060px);
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(330px, 400px);
            align-items: center;
            gap: 48px;
            position: relative;
            z-index: 1;
        }

        .brand-panel {
            max-width: 520px;
            transform: translateY(-8px);
            animation: logo-enter .75s cubic-bezier(.22, 1, .36, 1) .1s both;
        }

        .brand {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 12px;
            margin-bottom: 14px;
        }

        .brand-mark {
            width: 68px;
            height: 68px;
            border-radius: 16px;
            background: #fffdf1;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(244, 211, 94, .7);
            box-shadow: 0 8px 18px rgba(7, 59, 42, 0.08);
        }

        .brand-mark img {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }

        .brand-panel h2 {
            margin: 0 0 8px;
            color: var(--primary-dark);
            max-width: 600px;
            font-size: clamp(1.75rem, 3vw, 2.35rem);
            line-height: 1.12;
            letter-spacing: -.03em;
        }

        .brand-panel p {
            max-width: 400px;
            margin: 0;
            color: var(--muted);
            font-size: .88rem;
            line-height: 1.55;
        }

        .login-box {
            width: 100%;
            max-width: none;
            background: #ffffff;
            border: 1px solid #e2eee6;
            border-radius: 16px;
            box-shadow: 0 16px 45px rgba(7, 59, 42, .08), 0 3px 10px rgba(7, 59, 42, .03);
            padding: 22px 26px 20px;
            animation: box-enter .65s cubic-bezier(.22, 1, .36, 1) .18s both;
        }

        .login-heading {
            margin-bottom: 14px;
        }

        .login-heading h1 {
            color: var(--primary-dark);
            font-size: 1.35rem;
            text-align: left;
            margin: 0 0 3px;
            line-height: 1.2;
            letter-spacing: -.02em;
        }

        .subtitle {
            text-align: left;
            color: var(--muted);
            margin: 0 0 14px;
            font-size: 0.8rem;
            line-height: 1.35;
        }

        .action-stack {
            display: grid;
            gap: 8px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            text-align: center;
            padding: 9px 14px;
            border-radius: 8px;
            font-size: .88rem;
            font-weight: 700;
            text-decoration: none;
            height: 40px;
            box-sizing: border-box;
            border: none;
            cursor: pointer;
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            box-shadow: 0 8px 16px rgba(7, 59, 42, 0.14);
        }

        .btn-secondary {
            background: #f8fbf9;
            border: 1px solid #d4e5da;
            color: var(--text);
        }

        .btn-secondary:hover {
            background: #edf5f0;
            border-color: #bcdbc8;
        }

        .btn-register {
            background: #fff8ec;
            border: 1px solid #f4d35e;
            color: var(--primary-dark);
        }

        .btn-register:hover {
            background: #fff2d6;
            border-color: #e5be38;
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 6px 0;
            color: var(--muted);
            font-size: .8rem;
            font-weight: 600;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #d4e5da;
        }

        .divider span {
            padding: 0 10px;
        }

        .page-loader {
            position: fixed;
            inset: 0;
            z-index: 20;
            display: grid;
            place-items: center;
            background: rgba(248, 250, 252, .96);
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity .28s ease, visibility .28s ease;
        }

        .page-loader.is-active {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .loader-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        .loader-logo {
            display: grid;
            place-items: center;
            width: 86px;
            height: 86px;
        }

        .loader-logo img {
            width: 86px;
            height: 86px;
            object-fit: contain;
        }

        .loading-dots {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .loading-dots span {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--primary);
            animation: dot-loading 1s ease-in-out infinite;
        }

        .loading-dots span:nth-child(2) { animation-delay: .15s; }
        .loading-dots span:nth-child(3) { animation-delay: .3s; }

        @keyframes dot-loading {
            0%, 60%, 100% { transform: translateY(0); opacity: .35; }
            30% { transform: translateY(-6px); opacity: 1; }
        }

        @keyframes page-enter {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes logo-enter {
            from { opacity: 0; transform: translateY(-8px) scale(.96); }
            to { opacity: 1; transform: translateY(-8px) scale(1); }
        }

        @keyframes box-enter {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (prefers-reduced-motion: reduce) {
            .page, .brand-panel, .login-box { animation: none; }
        }

        @media (max-width: 900px) {
            .page {
                padding: 20px 16px;
            }

            .login-layout {
                grid-template-columns: 1fr;
                gap: 16px;
                max-width: 420px;
                margin: 0 auto;
            }

            .brand-panel {
                max-width: none;
                text-align: center;
                transform: none;
                margin: 0 auto;
            }

            .brand {
                justify-content: center;
                margin-bottom: 10px;
            }

            .brand-panel h2 {
                text-align: center;
                font-size: clamp(1.25rem, 3.5vw, 1.65rem);
                margin-bottom: 4px;
                line-height: 1.2;
            }

            .brand-panel p {
                text-align: center;
                margin: 0 auto;
                font-size: .84rem;
                max-width: 380px;
            }
        }

        @media (max-width: 600px) {
            body {
                background-attachment: scroll;
            }

            .page {
                padding: 16px 14px 20px;
                min-height: 100vh;
                min-height: 100dvh;
                align-items: flex-start;
            }

            .login-layout {
                gap: 12px;
                max-width: 100%;
            }

            .brand-panel {
                transform: none;
                padding: 0 4px;
            }

            .brand {
                margin-bottom: 8px;
            }

            .brand-mark {
                width: 52px;
                height: 52px;
                border-radius: 12px;
                box-shadow: 0 4px 10px rgba(7, 59, 42, 0.06);
            }

            .brand-mark img {
                width: 36px;
                height: 36px;
            }

            .brand-panel h2 {
                font-size: clamp(1.1rem, 4.5vw, 1.3rem);
                line-height: 1.2;
                letter-spacing: -.02em;
                margin-bottom: 4px;
            }

            .brand-panel h2 br {
                display: none;
            }

            .brand-panel p {
                font-size: 0.78rem;
                line-height: 1.4;
                max-width: 320px;
            }

            .login-box {
                padding: 16px 16px 14px;
                border-radius: 14px;
                box-shadow: 0 8px 24px rgba(7, 59, 42, .06);
            }

            .login-heading {
                margin-bottom: 10px;
            }

            .login-heading h1 {
                font-size: 1.2rem;
            }

            .subtitle {
                font-size: 0.75rem;
                margin-bottom: 12px;
                line-height: 1.35;
            }

            .action-stack {
                gap: 6px;
            }

            .btn {
                padding: 8px 12px;
                font-size: 0.84rem;
                height: 38px;
                border-radius: 8px;
            }
        }

        @media (max-width: 380px) {
            .page {
                padding: 12px 10px 16px;
            }

            .login-box {
                padding: 14px 12px 12px;
            }

            .brand-panel h2 {
                font-size: 1.05rem;
            }

            .login-heading h1 {
                font-size: 1.12rem;
            }
        }
    </style>
</head>
<body>
    <div class="page-loader" data-page-loader aria-hidden="true">
        <div class="loader-content">
            <div class="loader-logo">
                <img src="{{ asset('logo.png') }}" alt="">
            </div>
            <div class="loading-dots" aria-label="Memuat halaman">
                <span></span><span></span><span></span>
            </div>
        </div>
    </div>

    <div class="page">
        <div class="login-layout">
            <section class="brand-panel">
                <div class="brand">
                    <div class="brand-mark">
                        <img src="{{ asset('logo.png') }}" alt="Logo Bapperida">
                    </div>
                </div>
                <h2>Dashboard Data Indikator<br>Pembangunan Kota Tasikmalaya</h2>
                <p>Portal informasi indikator pembangunan Kota Tasikmalaya untuk pengelolaan data yang terarah dan terpercaya.</p>
            </section>

            <section class="login-box">
                <div class="login-heading">
                    <h1>Selamat Datang</h1>
                    <div class="subtitle">Silakan masuk atau daftar untuk mengakses dashboard data makro Kota Tasikmalaya</div>
                </div>

                <div class="action-stack">
                    @if(auth('web')->check())
                        <a href="{{ route('public.dashboard') }}" class="btn btn-primary">Buka Dashboard Pengguna</a>
                        <form action="{{ route('public.logout') }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" class="btn btn-secondary">Keluar Akun</button>
                        </form>
                    @elseif(auth('admin')->check())
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">Buka Dashboard Admin</a>
                        <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" class="btn btn-secondary">Keluar Admin</button>
                        </form>
                    @else
                        <a href="{{ route('public.login') }}" class="btn btn-primary">Masuk</a>
                        <a href="{{ route('public.register') }}" class="btn btn-register">Daftar Akun Baru</a>
                    @endif
                </div>
            </section>
        </div>
    </div>

    <script>
        const pageLoader = document.querySelector('[data-page-loader]');

        document.querySelectorAll('a[href]').forEach(function (link) {
            link.addEventListener('click', function () {
                if (!pageLoader) return;
                if (link.target === '_blank' || link.hasAttribute('download')) return;
                if (link.origin !== window.location.origin) return;
                if (link.pathname === window.location.pathname && link.search === window.location.search) return;
                pageLoader.classList.add('is-active');
                pageLoader.setAttribute('aria-hidden', 'false');
            });
        });
    </script>
</body>
</html>
