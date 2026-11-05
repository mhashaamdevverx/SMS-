@php
    $activeLocale = app()->getLocale();
    $activeDir = config("languages.supported.{$activeLocale}.dir", 'ltr');
    $activeFont = config("languages.supported.{$activeLocale}.font");
@endphp

@if($activeDir === 'rtl')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
@endif

@if(!empty($activeFont))
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family={{ str_replace(' ', '+', $activeFont) }}:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body, input, textarea, select, button {
            font-family: '{{ $activeFont }}', system-ui, -apple-system, sans-serif !important;
        }
    </style>
@endif

<style>
    /* Scoped Language Switcher Styles (zero global element selectors) */
    .lang-switcher-wrap {
        position: relative;
        display: inline-block;
        vertical-align: middle;
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-size: 13px;
        z-index: 1050;
        box-sizing: border-box;
    }

    .lang-switcher-wrap *,
    .lang-switcher-wrap *::before,
    .lang-switcher-wrap *::after {
        box-sizing: border-box;
    }

    .top-nav-actions .lang-switcher-wrap {
        margin-inline-end: 10px;
    }

    .lang-switcher-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 38px;
        padding: 0 12px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 9999px;
        color: #1e293b;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
        line-height: 1;
        list-style: none;
        user-select: none;
        transition: all 0.2s ease;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);
    }

    .lang-switcher-btn::-webkit-details-marker {
        display: none !important;
    }

    .lang-switcher-btn::marker {
        display: none !important;
        content: "" !important;
    }

    .lang-switcher-btn:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #4f46e5;
    }

    .lang-switcher-btn:focus-visible {
        outline: 2px solid #6366f1;
        outline-offset: 2px;
    }

    .lang-switcher-icon {
        font-size: 16px;
        color: #4f46e5;
        display: inline-flex;
    }

    .lang-switcher-current {
        font-size: 13px;
        font-weight: 600;
        white-space: nowrap;
    }

    .lang-switcher-arrow {
        font-size: 11px;
        color: #94a3b8;
        display: inline-flex;
        transition: transform 0.2s ease;
    }

    .lang-switcher-wrap[open] .lang-switcher-arrow {
        transform: rotate(180deg);
    }

    .lang-switcher-menu {
        position: absolute;
        top: calc(100% + 6px);
        inset-inline-end: 0;
        min-width: 155px;
        max-height: 320px;
        overflow-y: auto;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 8px 10px -6px rgba(15, 23, 42, 0.08);
        padding: 5px;
        margin: 0;
        list-style: none !important;
        z-index: 1200;
    }

    .lang-switcher-item {
        list-style: none !important;
        margin: 0;
        padding: 0;
    }

    .lang-switcher-item::before,
    .lang-switcher-item::marker {
        display: none !important;
        content: "" !important;
    }

    .lang-switcher-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 12px;
        border-radius: 8px;
        color: #334155 !important;
        text-decoration: none !important;
        font-size: 13px;
        font-weight: 500;
        line-height: 1.2;
        transition: background 0.15s ease, color 0.15s ease;
    }

    .lang-switcher-link:hover {
        background: #f1f5f9;
        color: #4f46e5 !important;
        text-decoration: none !important;
    }

    .lang-switcher-link.lang-switcher-active {
        background: #eef2ff;
        color: #4f46e5 !important;
        font-weight: 700;
    }

    .lang-switcher-code {
        font-size: 10px;
        font-weight: 700;
        opacity: 0.65;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin-inline-start: 8px;
    }

    /* Dark Mode Theme Support */
    [data-theme="dark"] .lang-switcher-btn {
        background: #1e293b;
        border-color: #334155;
        color: #f1f5f9;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }

    [data-theme="dark"] .lang-switcher-btn:hover {
        background: #334155;
        border-color: #475569;
        color: #818cf8;
    }

    [data-theme="dark"] .lang-switcher-icon {
        color: #818cf8;
    }

    [data-theme="dark"] .lang-switcher-menu {
        background: #1e293b;
        border-color: #334155;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.45);
    }

    [data-theme="dark"] .lang-switcher-link {
        color: #cbd5e1 !important;
    }

    [data-theme="dark"] .lang-switcher-link:hover {
        background: #334155;
        color: #ffffff !important;
    }

    [data-theme="dark"] .lang-switcher-link.lang-switcher-active {
        background: rgba(99, 102, 241, 0.25);
        color: #a5b4fc !important;
    }
</style>
