<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Makro Kota Tasikmalaya')</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #075c3a;
            --sidebar-bg-strong: #0b633f;
            --sidebar-panel: rgba(255, 255, 255, 0.04);
            --sidebar-text: #ffffff;
            --sidebar-muted: rgba(255, 255, 255, 0.72);
            --sidebar-label: rgba(255, 255, 255, 0.7);
            --sidebar-active: #249b5d;
            --sidebar-active-text: #ffffff;
            --sidebar-hover: rgba(255, 255, 255, 0.06);
            --sidebar-border: rgba(255, 255, 255, 0.12);
            --sidebar-footer-bg: #0c563d;
            --accent: #ffd23f;
            --content-bg: #ebf2ee;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-soft: #5d7288;
            --border: #dfe8e3;
            --shadow: 0 18px 40px rgba(15, 39, 71, 0.08);
        }

        .sidebar-public {
            background: #ffffff !important;
            color: #1f2937 !important;
            border-right: 1px solid rgba(148, 163, 184, 0.18) !important;
            box-shadow: 8px 0 24px rgba(15, 39, 71, 0.06);
        }

        .sidebar-public .sidebar-header,
        .sidebar-public .sidebar-footer,
        .sidebar-public .nav-group {
            background: transparent !important;
            border-color: rgba(148, 163, 184, 0.18) !important;
        }

        .sidebar-public .nav-label {
            color: #64748b !important;
        }

        .sidebar-public .nav-item {
            color: #1f2937 !important;
            background: transparent !important;
            border-color: transparent !important;
        }

        .sidebar-public .nav-item i {
            color: #2f7a5f !important;
        }

        .sidebar-public .nav-item:hover {
            background: #edf7f1 !important;
            color: #0f172a !important;
            border-color: rgba(13, 148, 76, 0.15) !important;
        }

        .sidebar-public .nav-item.active {
            background: #edf7f1 !important;
            color: #0f6b4d !important;
            border-color: rgba(13, 148, 76, 0.15) !important;
            box-shadow: inset 3px 0 0 #f4c95d, 0 4px 10px rgba(15, 39, 71, 0.04);
        }

        .sidebar-public .nav-item.active i {
            color: #0f6b4d !important;
        }

        .sidebar-public .logout-btn {
            background: linear-gradient(135deg, #1f6b45 0%, #073b2a 100%) !important;
            color: #ffffff !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }

        .sidebar-public .brand-name {
            color: #0f172a !important;
        }

        .sidebar-public .brand-sub {
            color: #64748b !important;
        }

        * {
            box-sizing: border-box;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        *::-webkit-scrollbar {
            display: none;
            width: 0;
            height: 0;
        }

        html, body {
            margin: 0;
            height: 100%;
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: var(--content-bg);
            color: var(--text-main);
        }

        html {
            font-size: 100%;
        }

        body {
            overflow: hidden;
        }

        .toast-stack {
            position: fixed;
            top: 16px;
            left: 50%;
            z-index: 1200;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            transform: translateX(-50%);
            pointer-events: none;
        }

        .app-toast {
            width: max-content;
            max-width: min(480px, calc(100vw - 32px));
            padding: 10px 14px;
            border: 1px solid rgba(15, 39, 71, 0.08);
            border-radius: 10px;
            box-shadow: 0 10px 24px rgba(15, 39, 71, 0.14);
            font-size: 0.82rem;
            font-weight: 700;
            line-height: 1.4;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px);
            transition: opacity 0.28s ease, transform 0.28s ease, visibility 0.28s ease;
            pointer-events: auto;
        }

        .app-toast.is-visible {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .app-toast.is-hidden {
            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px);
        }

        .app-toast--success {
            background: #e6f7ed;
            color: #176b3a;
            border-color: rgba(23, 107, 58, 0.14);
        }

        .app-toast--error {
            background: #fff1f1;
            color: #a3202a;
            border-color: rgba(163, 32, 42, 0.15);
        }

        .app-toast--warning {
            background: #fff8e6;
            color: #8a5a00;
            border-color: rgba(138, 90, 0, 0.12);
        }

        .app-toast--info {
            background: #edf5ff;
            color: #1d4f91;
            border-color: rgba(29, 79, 145, 0.12);
        }

        .admin-shell {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 240px;
            flex: 0 0 240px;
            height: 100vh;
            overflow: hidden;
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            position: relative;
            display: flex;
            flex-direction: column;
            border-right: 1px solid var(--sidebar-border);
            box-shadow: 8px 0 24px rgba(15, 39, 71, 0.08);
            z-index: 20;
            transition: transform 0.25s ease;
        }

        .sidebar-header {
            padding: 10px 14px;
            background: transparent;
            border-bottom: 1px solid var(--sidebar-border);
            position: sticky;
            top: 0;
            z-index: 4;
            display: flex;
            align-items: center;
            min-height: 56px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
            margin-top: 0;
        }

        .brand-logo-img {
            width: 32px;
            height: 32px;
            object-fit: contain;
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.18));
            margin-top: 0;
        }

        .brand-name {
            font-weight: 800;
            font-size: .92rem;
            line-height: 1.15;
            white-space: nowrap;
            color: #ffffff;
            letter-spacing: 0.01em;
            margin-top: 0;
        }

        .brand-sub {
            font-size: 0.68rem;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 1px;
            letter-spacing: 0.01em;
            white-space: nowrap;
            font-weight: 500;
        }

        .brand > div:last-child {
            min-width: 0;
        }

        .sidebar-body {
            padding: 8px 10px 10px;
            flex: 1;
            overflow-y: auto;
        }

        .nav-group {
            margin-bottom: 8px;
            background: transparent;
            border: none;
            border-radius: 0;
            padding: 0;
            box-shadow: none;
        }

        .nav-group:last-child {
            margin-bottom: 0;
        }

        .nav-label {
            font-size: 0.65rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.7);
            padding: 6px 8px 3px;
            font-weight: 700;
            line-height: 1.2;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            border: 1px solid transparent;
            background: transparent;
            color: var(--sidebar-text);
            text-decoration: none;
            min-height: 34px;
            padding: 6px 10px;
            margin-bottom: 2px;
            border-radius: 7px;
            font-weight: 600;
            font-size: 0.82rem;
            transition: background-color 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
            box-shadow: none;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.06);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.08);
            transform: none;
        }

        .nav-item i {
            flex: 0 0 16px;
            width: 16px;
            text-align: center;
            font-size: 0.88rem;
            color: rgba(255,255,255,0.8);
        }

        .nav-item span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .nav-item.active {
            background: var(--sidebar-active);
            color: var(--sidebar-active-text);
            border-color: transparent;
            box-shadow: inset 3px 0 0 var(--accent);
            border-radius: 7px;
        }

        .nav-item.active i {
            color: #ffffff;
        }

        .sidebar-footer {
            padding: 8px 10px 10px;
            border-top: 1px solid var(--sidebar-border);
            background: var(--sidebar-footer-bg);
        }

        .logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: #0a4f39;
            color: #ffffff;
            padding: 8px 10px;
            border-radius: 7px;
            font-weight: 700;
            font-size: 0.78rem;
            cursor: pointer;
            transition: filter 0.2s ease, transform 0.2s ease;
        }

        .logout-btn:hover {
            filter: brightness(1.08);
            transform: translateY(-1px);
        }

        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .topbar {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--border);
            padding: 8px 14px;
            min-height: 54px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .menu-toggle {
            display: none;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: var(--card-bg);
            color: var(--text-main);
            box-shadow: 0 4px 12px rgba(15,39,71,0.08);
            font-size: 1.1rem;
            cursor: pointer;
        }

        .page-title {
            font-size: 0.95rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .topbar-badge {
            padding: 5px 10px;
            border-radius: 999px;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #047857;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .content {
            padding: 10px 14px 12px;
            flex: 1;
            display: flex;
            align-items: stretch;
            height: calc(100vh - 54px);
            min-height: 0;
            overflow: auto;
        }

        .card {
            background: rgba(255,255,255,0.82);
            border: 1px solid var(--border);
            border-radius: 18px;
            box-shadow: var(--shadow);
            padding: 22px 26px;
            width: 100%;
            min-height: 100%;
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
        }

        .content h2 {
            margin: 0;
            font-size: clamp(2rem, 3vw, 3.5rem);
            color: var(--text-main);
            line-height: 1.05;
            font-weight: 800;
            letter-spacing: -0.04em;
        }

        .subtitle {
            margin: 0;
            color: var(--text-soft);
            font-size: 1rem;
        }

        .welcome-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 16px;
            background: #fff8e8;
            color: #8b6d10;
            padding: 8px 12px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.76rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .page-transition {
            position: fixed;
            inset: 0;
            z-index: 999;
            display: grid;
            place-items: center;
            background: rgba(248, 250, 252, .96);
            pointer-events: none;
            opacity: 0;
            visibility: hidden;
            transition: opacity .15s ease, visibility .15s ease;
        }

        .page-transition.is-active {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .page-transition-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        .page-transition img {
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
            background: var(--sidebar-active);
            animation: dot-loading 1s ease-in-out infinite;
        }

        .loading-dots span:nth-child(2) { animation-delay: .15s; }
        .loading-dots span:nth-child(3) { animation-delay: .3s; }

        @keyframes dot-loading {
            0%, 60%, 100% { transform: translateY(0); opacity: .35; }
            30% { transform: translateY(-6px); opacity: 1; }
        }

        .content {
            animation: content-enter .15s ease both;
        }

        @keyframes content-enter {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (prefers-reduced-motion: reduce) {
            .page-transition, .content { transition: none; animation: none; }
            .page-transition img, .loading-dots span { animation: none; }
        }

        /* Mobile Navigation for Public User */
        .user-mobile-topbar {
            display: none;
        }

        .user-bottom-nav {
            display: none;
        }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(2px);
            z-index: 22;
            opacity: 0;
            transition: opacity 0.25s ease;
        }

        .sidebar-backdrop.is-visible {
            opacity: 1;
        }

        @media (max-width: 991px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                transform: translateX(-100%);
                box-shadow: 18px 0 50px rgba(15, 39, 71, 0.18);
                z-index: 25;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .menu-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .main {
                width: 100%;
            }

            .user-mobile-topbar {
                display: flex;
                align-items: center;
                justify-content: flex-start;
                gap: 12px;
                padding: 8px 14px;
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-bottom: 1px solid var(--border);
                position: sticky;
                top: 0;
                z-index: 15;
                height: 52px;
                box-shadow: 0 2px 8px rgba(15, 39, 71, 0.04);
            }

            .mobile-nav-toggle {
                border: 0;
                background: #f1f5f9;
                color: #0f172a;
                width: 38px;
                height: 38px;
                border-radius: 9px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 1.25rem;
                cursor: pointer;
                transition: background-color 0.15s ease;
            }

            .mobile-nav-toggle:hover {
                background: #e2e8f0;
            }

            .user-mobile-brand {
                display: flex;
                align-items: center;
                gap: 8px;
                font-weight: 800;
                font-size: 0.86rem;
                color: var(--text-main);
                letter-spacing: -0.01em;
            }

            .mobile-brand-logo {
                width: 26px;
                height: 26px;
                object-fit: contain;
            }

            .sidebar-backdrop.active {
                display: block;
            }

            .content.user-content {
                height: calc(100vh - 52px - 58px);
                height: calc(100dvh - 52px - 58px);
                padding: 10px 12px 14px;
            }

            .user-bottom-nav {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                height: 58px;
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(12px);
                border-top: 1px solid var(--border);
                box-shadow: 0 -4px 16px rgba(15, 39, 71, 0.08);
                z-index: 15;
            }

            .bottom-nav-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 2px;
                color: #64748b;
                text-decoration: none;
                font-size: 0.68rem;
                font-weight: 700;
                transition: color 0.15s ease;
            }

            .bottom-nav-item i {
                font-size: 1.15rem;
                transition: transform 0.15s ease, color 0.15s ease;
            }

            .bottom-nav-item.active {
                color: #0f6b4d;
            }

            .bottom-nav-item.active i {
                color: #0f6b4d;
                transform: translateY(-1px);
            }
        }

        /* Custom Form Select Component (Replaces native OS select with Green Theme dropdown) */
        @keyframes dropFade {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .form-custom-select {
            position: relative;
            width: 100%;
        }
        .form-custom-select-trigger {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            width: 100%;
            min-height: 42px;
            border: 1px solid #d8e2ee;
            border-radius: 9px;
            padding: 10px 12px;
            background: #fbfdff;
            color: var(--text-main);
            font: inherit;
            font-size: .86rem;
            cursor: pointer;
            text-align: left;
            transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
        }
        .form-custom-select-trigger:hover {
            background: #ffffff;
            border-color: #cbd5e1;
        }
        .form-custom-select-trigger:focus,
        .form-custom-select.is-open .form-custom-select-trigger {
            outline: 0;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, .15);
            background: #fff;
        }
        .form-custom-select-trigger.is-invalid {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, .15) !important;
        }
        .form-custom-select-label {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .form-custom-select-arrow {
            font-size: .72rem;
            color: #64748b;
            transition: transform .2s ease;
            flex-shrink: 0;
        }
        .form-custom-select.is-open .form-custom-select-arrow {
            transform: rotate(180deg);
            color: #10b981;
        }
        .form-custom-select-menu {
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            max-height: 240px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 9px;
            box-shadow: 0 12px 30px rgba(15, 39, 71, 0.14), 0 2px 6px rgba(0, 0, 0, 0.04);
            z-index: 1060;
            display: none;
            flex-direction: column;
            overflow: hidden;
        }
        .form-custom-select.is-open .form-custom-select-menu {
            display: flex;
            animation: dropFade .15s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .form-custom-select-menu.opens-up {
            top: auto;
            bottom: calc(100% + 4px);
            box-shadow: 0 -12px 30px rgba(15, 39, 71, 0.14), 0 -2px 6px rgba(0, 0, 0, 0.04);
        }
        .form-custom-select-search {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-bottom: 1px solid #f1f5f9;
            background: #f8fafc;
            transition: background .15s ease, border-color .15s ease;
        }
        .form-custom-select-search:focus-within {
            background: #ffffff;
            border-bottom-color: #10b981;
        }
        .form-custom-select-search i {
            color: #94a3b8;
            font-size: .78rem;
            flex-shrink: 0;
            transition: color .15s ease;
        }
        .form-custom-select-search:focus-within i {
            color: #10b981;
        }
        .form-custom-select-search input,
        .form-grid .form-custom-select-search input,
        .field .form-custom-select-search input {
            flex: 1 1 auto !important;
            width: 100% !important;
            height: 28px !important;
            min-height: unset !important;
            border: 0 !important;
            outline: 0 !important;
            background: transparent !important;
            padding: 0 !important;
            margin: 0 !important;
            box-shadow: none !important;
            color: var(--text-main) !important;
            font: inherit !important;
            font-size: .82rem !important;
            white-space: normal !important;
        }
        .form-custom-select-search input:focus,
        .form-grid .form-custom-select-search input:focus,
        .field .form-custom-select-search input:focus {
            outline: 0 !important;
            border: 0 !important;
            box-shadow: none !important;
            background: transparent !important;
        }
        .form-custom-select-options {
            overflow-y: auto;
            max-height: 195px;
            padding: 4px;
        }
        .form-custom-select-option {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 8px 10px;
            border: 0;
            border-radius: 6px;
            background: transparent;
            font: inherit;
            font-size: .83rem;
            font-weight: 500;
            color: var(--text-main);
            cursor: pointer;
            text-align: left;
            transition: background-color .12s ease, color .12s ease;
        }
        .form-custom-select-option:hover {
            background-color: #ecfdf5;
            color: #065f46;
        }
        .form-custom-select-option.is-selected {
            background-color: #d1fae5;
            color: #065f46;
            font-weight: 700;
        }
        .form-custom-select-option .check-icon {
            color: #10b981;
            font-size: .9rem;
            flex-shrink: 0;
        }
    </style>
</head>
<body>
    @php
        $globalFlashMessages = [];
        foreach (['success', 'error', 'warning', 'info', 'status'] as $flashKey) {
            $flashValue = session($flashKey);
            if ($flashValue !== null && $flashValue !== '') {
                $globalFlashMessages[] = [
                    'type' => $flashKey === 'status' ? 'info' : $flashKey,
                    'message' => $flashValue,
                    'role' => $flashKey === 'error' ? 'alert' : 'status',
                ];
            }
        }

        if (isset($errors) && $errors->any()) {
            foreach ($errors->all() as $error) {
                $globalFlashMessages[] = [
                    'type' => 'error',
                    'message' => $error,
                    'role' => 'alert',
                ];
            }
        }
    @endphp

    @if (!empty($globalFlashMessages))
        <div class="toast-stack" aria-live="polite" aria-atomic="true">
            @foreach ($globalFlashMessages as $flash)
                <div class="app-toast app-toast--{{ $flash['type'] }}" data-global-toast role="{{ $flash['role'] }}">{{ $flash['message'] }}</div>
            @endforeach
        </div>
    @endif

    <div class="page-transition" data-page-transition aria-hidden="true">
        <div class="page-transition-content">
            <img src="{{ asset('logo.png') }}" alt="">
            <div class="loading-dots" aria-label="Memuat halaman">
                <span></span><span></span><span></span>
            </div>
        </div>
    </div>
    @php
        $currentUser = auth()->user();
        $isPublicUser = request()->routeIs('public.*') || ($currentUser instanceof \App\Models\User);
        $isDashboardActive = $isPublicUser ? request()->routeIs('public.dashboard') : request()->routeIs('dashboard');
        $isDetailActive = $isPublicUser
            ? (request()->routeIs('public.detail.indikator') || request()->routeIs('public.indikator.show'))
            : (request()->routeIs('detail.indikator') || request()->routeIs('indikator.show'));
    @endphp

    <div class="admin-shell">
        <aside class="sidebar {{ $isPublicUser ? 'sidebar-public' : '' }}" id="sidebar">
            <div class="sidebar-header">
                <div class="brand">
                    <img src="{{ asset('logo.png') }}" alt="Logo" class="brand-logo-img">
                    <div>
                        <div class="brand-name">Bapperida</div>
                        <div class="brand-sub">Kota Tasikmalaya</div>
                    </div>
                </div>
            </div>

            <div class="sidebar-body">
                <div class="nav-group">
                    <div class="nav-label">Menu Utama</div>
                    <a href="{{ $isPublicUser ? route('public.dashboard') : route('dashboard') }}" class="nav-item {{ $isDashboardActive && !$isDetailActive ? 'active' : '' }}">
                        <i class="bi bi-house-door-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </div>

                @if($isPublicUser)
                    <div class="nav-group">
                        <div class="nav-label">Detail</div>
                        <a href="{{ route('public.detail.indikator') }}" class="nav-item {{ $isDetailActive ? 'active' : '' }}">
                            <i class="bi bi-file-earmark-text-fill"></i>
                            <span>Detail Indikator</span>
                        </a>
                        <div class="nav-label" style="padding-top: 8px;">Analisis</div>
                        <a href="{{ route('public.analisis') }}" class="nav-item {{ request()->routeIs('public.analisis') ? 'active' : '' }}">
                            <i class="bi bi-pie-chart-fill"></i>
                            <span>Analisis Indikator</span>
                        </a>
                    </div>
                @else
                    <div class="nav-group">
                        <div class="nav-label">Detail</div>
                        <a href="{{ route('detail.indikator') }}" class="nav-item {{ $isDetailActive ? 'active' : '' }}">
                            <i class="bi bi-file-earmark-text-fill"></i>
                            <span>Detail Indikator</span>
                        </a>
                    </div>

                    <div class="nav-group">
                        <div class="nav-label">Data</div>
                        <a href="{{ route('indikator.index') }}" class="nav-item {{ request()->routeIs('indikator.index') || request()->routeIs('indikator.create') || request()->routeIs('indikator.edit') ? 'active' : '' }}">
                            <i class="bi bi-list-ul"></i>
                            <span>Data Indikator</span>
                        </a>
                        <a href="{{ route('capaian.index') }}" class="nav-item {{ request()->routeIs('capaian.*') && !request()->routeIs('capaian.import*') ? 'active' : '' }}">
                            <i class="bi bi-graph-up-arrow"></i>
                            <span>Data Capaian</span>
                        </a>
                        <a href="{{ route('capaian.import') }}" class="nav-item {{ request()->routeIs('capaian.import*') ? 'active' : '' }}">
                            <i class="bi bi-file-earmark-spreadsheet"></i>
                            <span>Import Excel</span>
                        </a>
                        <a href="{{ route('target.index') }}" class="nav-item {{ request()->routeIs('target.*') ? 'active' : '' }}">
                            <i class="bi bi-bullseye"></i>
                            <span>Data Target</span>
                        </a>
                    </div>

                    <div class="nav-group">
                        <div class="nav-label">Analisis</div>
                        <a href="{{ route('analisis.index') }}" class="nav-item {{ request()->routeIs('analisis.*') ? 'active' : '' }}">
                            <i class="bi bi-pie-chart-fill"></i>
                            <span>Analisis Indikator</span>
                        </a>
                    </div>

                    <div class="nav-group">
                        <div class="nav-label">Pengguna</div>
                        <a href="{{ route('users.index') }}" class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                            <i class="bi bi-people-fill"></i>
                            <span>Data User</span>
                        </a>
                    </div>

                    <div class="nav-group">
                        <div class="nav-label">Pengaturan</div>
                        <a href="{{ route('settings.index') }}" class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                            <i class="bi bi-gear-fill"></i>
                            <span>Pengaturan Akun</span>
                        </a>
                    </div>
                @endif
            </div>

            <div class="sidebar-footer">
                <form action="{{ $isPublicUser ? route('public.logout') : route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        <main class="main">
            @if ($isPublicUser)
                <header class="user-mobile-topbar" id="userMobileTopbar">
                    <button type="button" class="mobile-nav-toggle" id="menuToggle" aria-label="Buka Menu">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="user-mobile-brand">
                        <img src="{{ asset('logo.png') }}" alt="Logo" class="mobile-brand-logo">
                        <span>Bapperida Kota Tasikmalaya</span>
                    </div>
                </header>
                <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
            @endif

            <div class="content {{ $isPublicUser ? 'user-content' : '' }}">
                @yield('content')
            </div>

            @if ($isPublicUser)
                <nav class="user-bottom-nav">
                    <a href="{{ route('public.dashboard') }}" class="bottom-nav-item {{ $isDashboardActive && !$isDetailActive ? 'active' : '' }}">
                        <i class="bi bi-house-door-fill"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('public.detail.indikator') }}" class="bottom-nav-item {{ $isDetailActive ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text-fill"></i>
                        <span>Detail</span>
                    </a>
                    <a href="{{ route('public.analisis') }}" class="bottom-nav-item {{ request()->routeIs('public.analisis') ? 'active' : '' }}">
                        <i class="bi bi-pie-chart-fill"></i>
                        <span>Analisis</span>
                    </a>
                </nav>
            @endif
        </main>
    </div>

    <script>
        const pageTransition = document.querySelector('[data-page-transition]');
        const sidebar = document.getElementById('sidebar');
        const menuToggle = document.getElementById('menuToggle');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        const content = document.querySelector('.content');
        const scrollRestoreKey = 'admin-content-scroll';

        document.addEventListener('submit', function (event) {
            const form = event.target;
            if (!(form instanceof HTMLFormElement) || form.method.toLowerCase() !== 'post') return;
            if (new URL(form.action, window.location.href).pathname.endsWith('/logout')) return;

            sessionStorage.setItem(scrollRestoreKey, JSON.stringify({
                top: content?.scrollTop ?? 0,
            }));
        }, true);

        const savedScroll = sessionStorage.getItem(scrollRestoreKey);
        if (savedScroll) {
            sessionStorage.removeItem(scrollRestoreKey);
            try {
                const position = JSON.parse(savedScroll);
                if (document.querySelector('[data-global-toast]') && content) {
                    const restoreScroll = () => requestAnimationFrame(() => requestAnimationFrame(() => {
                        content.scrollTop = position.top;
                    }));

                    if (document.readyState === 'complete') {
                        restoreScroll();
                    } else {
                        window.addEventListener('load', restoreScroll, { once: true });
                    }
                }
            } catch {
                sessionStorage.removeItem(scrollRestoreKey);
            }
        }

        document.querySelectorAll('[data-global-toast]').forEach((toast) => {
            requestAnimationFrame(() => toast.classList.add('is-visible'));
            window.setTimeout(() => {
                toast.classList.add('is-hidden');
                window.setTimeout(() => toast.remove(), 280);
            }, 3500);
        });

        const hidePageTransition = () => {
            if (window.pageTransitionTimer) {
                clearTimeout(window.pageTransitionTimer);
                window.pageTransitionTimer = null;
            }
            if (!pageTransition) return;
            pageTransition.classList.remove('is-active');
            pageTransition.setAttribute('aria-hidden', 'true');
        };

        hidePageTransition();
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', hidePageTransition, { once: true });
        }
        window.addEventListener('pageshow', hidePageTransition);
        window.addEventListener('load', hidePageTransition, { once: true });

        document.addEventListener('click', function (event) {
            const link = event.target.closest('a[href]');
            if (!link || !pageTransition || event.defaultPrevented) return;
            if (link.target === '_blank' || link.hasAttribute('download') || link.hasAttribute('data-no-page-transition') || link.origin !== window.location.origin) return;
            if (link.pathname === window.location.pathname && link.search === window.location.search) return;

            if (window.pageTransitionTimer) clearTimeout(window.pageTransitionTimer);
            window.pageTransitionTimer = setTimeout(() => {
                pageTransition.classList.add('is-active');
                pageTransition.setAttribute('aria-hidden', 'false');
            }, 120);
        });

        if (menuToggle && sidebar) {
            menuToggle.addEventListener('click', function () {
                const isOpen = sidebar.classList.toggle('open');
                if (sidebarBackdrop) {
                    sidebarBackdrop.classList.toggle('active', isOpen);
                    requestAnimationFrame(() => sidebarBackdrop.classList.toggle('is-visible', isOpen));
                }
            });
        }

        if (sidebarBackdrop && sidebar) {
            sidebarBackdrop.addEventListener('click', function () {
                sidebar.classList.remove('open');
                sidebarBackdrop.classList.remove('is-visible');
                setTimeout(() => sidebarBackdrop.classList.remove('active'), 250);
            });
        }

        // Setup Custom Form Select for any select inside .form-grid or .field
        function setupCustomFormSelect(select) {
            if (!select || select.dataset.customized === 'true') return;
            if (select.closest('.filters') || select.classList.contains('per-page-select') || select.classList.contains('no-custom-select')) return;
            select.dataset.customized = 'true';

            // Hide native select visually
            select.style.position = 'absolute';
            select.style.opacity = '0';
            select.style.width = '1px';
            select.style.height = '1px';
            select.style.pointerEvents = 'none';
            select.style.zIndex = '-1';
            select.tabIndex = -1;

            const wrapper = document.createElement('div');
            wrapper.className = 'form-custom-select';

            const trigger = document.createElement('button');
            trigger.type = 'button';
            trigger.className = 'form-custom-select-trigger';
            trigger.setAttribute('aria-haspopup', 'listbox');
            trigger.setAttribute('aria-expanded', 'false');

            const labelSpan = document.createElement('span');
            labelSpan.className = 'form-custom-select-label';

            const arrowIcon = document.createElement('i');
            arrowIcon.className = 'bi bi-chevron-down form-custom-select-arrow';

            trigger.appendChild(labelSpan);
            trigger.appendChild(arrowIcon);
            wrapper.appendChild(trigger);

            const menu = document.createElement('div');
            menu.className = 'form-custom-select-menu';
            menu.setAttribute('role', 'listbox');

            const optionsCount = select.options.length;
            const enableSearch = optionsCount > 6 || select.dataset.search === 'true';

            let searchInput = null;
            if (enableSearch) {
                const searchBox = document.createElement('div');
                searchBox.className = 'form-custom-select-search';
                searchBox.innerHTML = '<i class="bi bi-search"></i><input type="text" placeholder="Cari..." autocomplete="off">';
                searchInput = searchBox.querySelector('input');
                menu.appendChild(searchBox);
            }

            const optionsContainer = document.createElement('div');
            optionsContainer.className = 'form-custom-select-options';

            const optionButtons = [];

            const updateFromSelect = () => {
                const selectedOpt = select.selectedOptions[0] || select.options[0];
                labelSpan.textContent = selectedOpt ? selectedOpt.textContent.trim() : 'Pilih...';
                optionButtons.forEach(btn => {
                    const isMatch = btn.getAttribute('data-value') === (selectedOpt ? selectedOpt.value : '');
                    btn.classList.toggle('is-selected', isMatch);
                    let checkIcon = btn.querySelector('.check-icon');
                    if (isMatch) {
                        if (!checkIcon) {
                            checkIcon = document.createElement('i');
                            checkIcon.className = 'bi bi-check2 check-icon';
                            btn.appendChild(checkIcon);
                        }
                    } else if (checkIcon) {
                        checkIcon.remove();
                    }
                });
            };

            Array.from(select.options).forEach(opt => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'form-custom-select-option';
                btn.setAttribute('data-value', opt.value);
                btn.setAttribute('data-label', opt.textContent.trim());

                Array.from(opt.attributes).forEach(attr => {
                    if (attr.name.startsWith('data-')) {
                        btn.setAttribute(attr.name, attr.value);
                    }
                });

                const span = document.createElement('span');
                span.textContent = opt.textContent.trim();
                btn.appendChild(span);

                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    select.value = opt.value;
                    trigger.classList.remove('is-invalid');
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                    updateFromSelect();
                    toggle(false);
                    trigger.focus();
                });

                optionsContainer.appendChild(btn);
                optionButtons.push(btn);
            });

            menu.appendChild(optionsContainer);
            wrapper.appendChild(menu);

            const toggle = (open) => {
                const isOpen = open ?? !wrapper.classList.contains('is-open');
                if (isOpen) {
                    document.querySelectorAll('.form-custom-select.is-open, .custom-select-container.is-open').forEach(el => {
                        if (el !== wrapper) el.classList.remove('is-open');
                    });
                    const rect = trigger.getBoundingClientRect();
                    const spaceBelow = window.innerHeight - rect.bottom;
                    if (spaceBelow < 250 && rect.top > 250) {
                        menu.classList.add('opens-up');
                    } else {
                        menu.classList.remove('opens-up');
                    }
                }
                wrapper.classList.toggle('is-open', isOpen);
                trigger.setAttribute('aria-expanded', String(isOpen));
                if (isOpen && searchInput) {
                    searchInput.value = '';
                    optionButtons.forEach(b => b.style.display = 'flex');
                    setTimeout(() => searchInput.focus(), 50);
                }
            };

            trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                toggle();
            });

            if (searchInput) {
                searchInput.addEventListener('input', (e) => {
                    const q = e.target.value.toLowerCase().trim();
                    optionButtons.forEach(btn => {
                        const text = (btn.getAttribute('data-label') || '').toLowerCase();
                        btn.style.display = text.includes(q) ? 'flex' : 'none';
                    });
                });
                searchInput.addEventListener('click', (e) => e.stopPropagation());
            }

            document.addEventListener('click', (e) => {
                if (!wrapper.contains(e.target)) toggle(false);
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && wrapper.classList.contains('is-open')) {
                    toggle(false);
                    trigger.focus();
                }
            });

            select.addEventListener('change', updateFromSelect);

            select.addEventListener('invalid', (e) => {
                e.preventDefault();
                trigger.focus();
                trigger.classList.add('is-invalid');
            });

            const form = select.closest('form');
            if (form) {
                form.addEventListener('reset', () => setTimeout(() => updateFromSelect(), 10));
            }

            const fieldLabel = select.id ? document.querySelector('label[for="' + select.id + '"]') : select.closest('.field')?.querySelector('label');
            if (fieldLabel) {
                fieldLabel.addEventListener('click', (e) => {
                    e.preventDefault();
                    trigger.focus();
                    toggle();
                });
            }

            select.parentNode.insertBefore(wrapper, select.nextSibling);
            updateFromSelect();
        }

        function initAllFormCustomSelects(root = document) {
            root.querySelectorAll('.form-grid select, .field select, select.custom-form-select').forEach(setupCustomFormSelect);
        }

        initAllFormCustomSelects();
        document.addEventListener('DOMContentLoaded', () => initAllFormCustomSelects());
    </script>
</body>
</html>