{{-- Injected shop branding: theme color + CSS variable bridge for full admin panel --}}
@php
    $shopThemeModel = $currentShop ?? null;
    $shopTheme = $shopThemeModel ? $shopThemeModel->themeColor() : '#2D5BFF';
    $shopThemeRgb = $shopThemeModel ? $shopThemeModel->themeRgbString() : '45, 91, 255';
    $shopThemeDark = $shopThemeModel ? $shopThemeModel->themeDark(0.55) : '#1A2B8F';
    $shopLogo = $shopThemeModel ? $shopThemeModel->logoUrl() : asset('assets/img/logo.webp');
    $shopName = $shopThemeModel->name ?? 'Infotech';
    $themeStyle = $shopThemeModel
        ? $shopThemeModel->themeStyleAttributes()
        : "--shop-theme: {$shopTheme}; --shop-theme-rgb: {$shopThemeRgb}; --shop-theme-dark: {$shopThemeDark}; --bc-primary: {$shopTheme}; --bs-primary: {$shopTheme}; --bs-primary-rgb: {$shopThemeRgb}; --al-blue: {$shopTheme}; --al-grad: linear-gradient(135deg, {$shopTheme} 0%, {$shopThemeDark} 100%);";
@endphp
<style id="shop-theme-vars">
    :root {
        {!! str_replace('; ', ";\n        ", $themeStyle) !!};
    }

    /* Full-panel primary overrides driven by shop theme */
    .text-primary,
    a.text-primary,
    .btn-link,
    .link-primary,
    .bc-brand-text strong {
        color: var(--shop-theme) !important;
    }

    .bg-primary,
    .badge.bg-primary,
    .btn-check:checked + .btn-outline-primary,
    .page-item.active .page-link,
    .nav-pills .nav-link.active,
    .form-check-input:checked,
    .app-brand .layout-menu-toggle {
        background-color: var(--shop-theme) !important;
        border-color: var(--shop-theme) !important;
        color: #fff !important;
    }

    .border-primary {
        border-color: var(--shop-theme) !important;
    }

    .bg-label-primary,
    .badge.bg-label-primary {
        background-color: var(--shop-theme-soft) !important;
        color: var(--shop-theme) !important;
    }

    .progress-bar.bg-primary,
    .progress .progress-bar {
        background-color: var(--shop-theme) !important;
    }

    .btn-primary,
    .btn-primary:hover,
    .btn-primary:focus,
    .btn-primary:active,
    .dt-buttons .btn-primary,
    .swal2-confirm {
        background: var(--bc-grad-primary) !important;
        background-color: var(--shop-theme) !important;
        border-color: var(--shop-theme) !important;
        color: #fff !important;
        box-shadow: 0 8px 18px rgba(var(--shop-theme-rgb), 0.28) !important;
    }

    .btn-outline-primary {
        border-color: var(--shop-theme) !important;
        color: var(--shop-theme) !important;
    }

    .btn-outline-primary:hover {
        background: var(--shop-theme-soft) !important;
        color: var(--shop-theme-dark) !important;
    }

    .bg-menu-theme .menu-inner > .menu-item.active > .menu-link,
    html:not(.layout-menu-collapsed) .bg-menu-theme .menu-inner > .menu-item.active > .menu-link,
    .layout-wrapper:not(.layout-horizontal) .bg-menu-theme .menu-inner > .menu-item.active > .menu-link {
        color: #fff !important;
        background: var(--bc-grad-primary) !important;
        background-color: var(--shop-theme) !important;
        box-shadow: 0 8px 20px rgba(var(--shop-theme-rgb), 0.28) !important;
    }

    .bg-menu-theme .menu-inner > .menu-item.active:before,
    .layout-wrapper:not(.layout-horizontal) .bg-menu-theme .menu-inner > .menu-item.active:before {
        background: var(--shop-theme) !important;
    }

    .menu-inner > .menu-item:hover > .menu-link {
        background: var(--shop-theme-soft) !important;
        color: var(--shop-theme) !important;
    }

    .form-control:focus,
    .form-select:focus,
    .dt-bootstrap5 .form-control:focus,
    .select2-container--default.select2-container--focus .select2-selection--multiple,
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        border-color: var(--shop-theme) !important;
        box-shadow: 0 0 0 0.2rem rgba(var(--shop-theme-rgb), 0.2) !important;
    }

    .form-check-input:focus {
        border-color: var(--shop-theme);
        box-shadow: 0 0 0 0.2rem rgba(var(--shop-theme-rgb), 0.2);
    }

    .nav-tabs .nav-link.active,
    .nav-tabs .nav-item.show .nav-link {
        color: var(--shop-theme) !important;
        border-bottom-color: var(--shop-theme) !important;
    }

    .pagination .page-item.active .page-link,
    .dataTables_wrapper .page-item.active .page-link {
        background-color: var(--shop-theme) !important;
        border-color: var(--shop-theme) !important;
    }

    .fc .fc-button-primary,
    .fc .fc-button-primary:not(:disabled).fc-button-active,
    .fc .fc-button-primary:hover {
        background: var(--bc-grad-primary) !important;
        border-color: var(--shop-theme) !important;
    }

    .modal .modal-header.bg-primary,
    .modal-header.bg-primary {
        background: var(--bc-grad-primary) !important;
        background-color: var(--shop-theme) !important;
    }

    .saas-plan-usage {
        background: var(--shop-theme-soft) !important;
        color: var(--shop-theme-dark) !important;
    }

    .bc-brand-logo {
        object-fit: contain;
        max-height: 48px;
        width: auto;
    }

    /* Login / auth surfaces */
    body.al-page {
        --al-blue: var(--shop-theme);
        --al-navy: var(--shop-theme-dark);
        --al-soft: var(--shop-theme-soft);
        --al-grad: var(--bc-grad-primary);
    }

    .al-spinner {
        border-top-color: var(--shop-theme) !important;
    }

    .al-btn-primary,
    .al-card .btn-primary,
    button.al-submit {
        background: var(--bc-grad-primary) !important;
        border-color: var(--shop-theme) !important;
    }

    .al-right {
        background: var(--bc-grad-primary) !important;
    }
</style>
