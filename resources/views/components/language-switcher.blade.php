@php
    $supportedLanguages = config('languages.supported', []);
    $currentLocale = app()->getLocale();
    $currentLang = $supportedLanguages[$currentLocale] ?? ['native' => 'English', 'name' => 'English'];
@endphp

<details class="lang-switcher-wrap">
    <summary class="lang-switcher-btn" title="{{ __('Language') }}" aria-label="{{ __('Language') }}">
        <i class="bi bi-translate lang-switcher-icon" aria-hidden="true"></i>
        <span class="lang-switcher-current">{{ $currentLang['native'] }}</span>
        <i class="bi bi-chevron-down lang-switcher-arrow" aria-hidden="true"></i>
    </summary>
    <ul class="lang-switcher-menu">
        @foreach($supportedLanguages as $code => $data)
            <li class="lang-switcher-item">
                <a class="lang-switcher-link {{ $code === $currentLocale ? 'lang-switcher-active' : '' }}" href="{{ route('lang.switch', $code) }}">
                    <span>{{ $data['native'] }}</span>
                    <small class="lang-switcher-code">{{ strtoupper($code) }}</small>
                </a>
            </li>
        @endforeach
    </ul>
</details>

<script>
    if (!window.__langSwitcherListenerAttached) {
        window.__langSwitcherListenerAttached = true;
        document.addEventListener('click', function (e) {
            document.querySelectorAll('.lang-switcher-wrap[open]').forEach(function (el) {
                if (!el.contains(e.target)) {
                    el.removeAttribute('open');
                }
            });
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.lang-switcher-wrap[open]').forEach(function (el) {
                    el.removeAttribute('open');
                });
            }
        });
    }
</script>
