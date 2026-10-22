@extends('platform.layout')

@section('title', 'Website settings')

@section('content')
<div class="pa-page-head">
    <div><p class="pa-eyebrow">Public website</p><h1>Website settings</h1><p>Update the platform brand and support details used by the public marketing pages.</p></div>
    <a class="pa-button pa-button-light" href="{{ route('saas.home') }}" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> Preview website</a>
</div>
<section class="pa-panel">
    <div class="pa-panel-head"><h2>Brand &amp; contact information</h2></div>
    <form method="POST" action="{{ route('platform.website.update') }}" style="padding:22px">
        @csrf
        @method('PUT')
        <div class="pa-form-grid">
            <div class="pa-field">
                <label for="site_name">Website name</label>
                <input id="site_name" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? 'BarberPro') }}" maxlength="100" required>
                <div class="pa-help">Used in the public website navigation and page titles.</div>
            </div>
            <div class="pa-field">
                <label for="site_tagline">Brand tagline</label>
                <input id="site_tagline" name="site_tagline" value="{{ old('site_tagline', $settings['site_tagline'] ?? '') }}" maxlength="180">
            </div>
            <div class="pa-field">
                <label for="support_email">Support email</label>
                <input id="support_email" name="support_email" type="email" value="{{ old('support_email', $settings['support_email'] ?? '') }}" maxlength="190">
            </div>
            <div class="pa-field">
                <label for="support_phone">Support phone</label>
                <input id="support_phone" name="support_phone" type="tel" value="{{ old('support_phone', $settings['support_phone'] ?? '') }}" maxlength="40">
            </div>
            <div class="pa-field" style="grid-column:1/-1">
                <label for="announcement">Announcement bar</label>
                <textarea id="announcement" name="announcement" maxlength="240" placeholder="Leave blank to hide the announcement bar.">{{ old('announcement', $settings['announcement'] ?? '') }}</textarea>
                <div class="pa-help">Optional public message, shown above the website navigation.</div>
            </div>
        </div>
        <div class="pa-form-actions"><button type="submit" class="pa-button"><i class="bi bi-check2"></i> Save website settings</button></div>
    </form>
</section>
@endsection
