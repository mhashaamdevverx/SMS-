@php
    $defaultSupported = [
        'en' => ['name' => 'English', 'native' => 'English', 'dir' => 'ltr', 'font' => null],
        'ur' => ['name' => 'Urdu', 'native' => 'اردو', 'dir' => 'rtl', 'font' => 'Noto Nastaliq Urdu'],
        'ar' => ['name' => 'Arabic', 'native' => 'العربية', 'dir' => 'rtl', 'font' => 'Noto Sans Arabic'],
        'hi' => ['name' => 'Hindi', 'native' => 'हिन्दी', 'dir' => 'ltr', 'font' => 'Noto Sans Devanagari'],
        'bn' => ['name' => 'Bengali', 'native' => 'বাংলা', 'dir' => 'ltr', 'font' => 'Noto Sans Bengali'],
        'zh' => ['name' => 'Chinese', 'native' => '中文', 'dir' => 'ltr', 'font' => 'Noto Sans SC'],
        'es' => ['name' => 'Spanish', 'native' => 'Español', 'dir' => 'ltr', 'font' => null],
        'fr' => ['name' => 'French', 'native' => 'Français', 'dir' => 'ltr', 'font' => null],
        'de' => ['name' => 'German', 'native' => 'Deutsch', 'dir' => 'ltr', 'font' => null],
        'it' => ['name' => 'Italian', 'native' => 'Italiano', 'dir' => 'ltr', 'font' => null],
    ];
    $configuredLanguages = config('languages.supported', []);
    $supportedLanguages = [];
    foreach ($defaultSupported as $code => $meta) {
        $supportedLanguages[$code] = $configuredLanguages[$code] ?? $meta;
    }
    $currentLocale = app()->getLocale();
    $currentLang = $supportedLanguages[$currentLocale] ?? $defaultSupported['en'];
@endphp

<details class="lang-switcher-wrap">
    <summary class="lang-switcher-btn" title="{{ __('Language') }}" aria-label="{{ __('Language') }}" aria-haspopup="true" aria-expanded="false">
        <i class="bi bi-translate lang-switcher-icon" aria-hidden="true"></i>
        <span class="lang-switcher-current">{{ $currentLang['native'] }}</span>
        <i class="bi bi-chevron-down lang-switcher-arrow" aria-hidden="true"></i>
    </summary>
    <ul class="lang-switcher-menu" role="menu">
        @foreach($supportedLanguages as $code => $data)
            <li class="lang-switcher-item" role="none">
                <a class="lang-switcher-link {{ $code === $currentLocale ? 'lang-switcher-active' : '' }}" href="{{ route('lang.switch', $code) }}" role="menuitem" @if($code === $currentLocale) aria-current="true" @endif>
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
        var syncSwitcherState = function (el) {
            var btn = el.querySelector('.lang-switcher-btn');
            if (btn) {
                btn.setAttribute('aria-expanded', el.hasAttribute('open') ? 'true' : 'false');
            }
        };
        document.addEventListener('toggle', function (e) {
            var target = e.target;
            if (!target || !target.classList || !target.classList.contains('lang-switcher-wrap')) {
                return;
            }
            if (target.hasAttribute('open')) {
                document.querySelectorAll('.lang-switcher-wrap[open]').forEach(function (other) {
                    if (other !== target) {
                        other.removeAttribute('open');
                        syncSwitcherState(other);
                    }
                });
            }
            syncSwitcherState(target);
        }, true);
        document.addEventListener('click', function (e) {
            document.querySelectorAll('.lang-switcher-wrap[open]').forEach(function (el) {
                if (!el.contains(e.target)) {
                    el.removeAttribute('open');
                    syncSwitcherState(el);
                }
            });
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.lang-switcher-wrap[open]').forEach(function (el) {
                    el.removeAttribute('open');
                    syncSwitcherState(el);
                });
            }
        });
    }
</script>
