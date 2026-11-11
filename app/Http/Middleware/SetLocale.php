<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    private const SUPPORTED_LOCALES = ['en', 'ur', 'ar', 'hi', 'bn', 'zh', 'es', 'fr', 'de', 'it'];

    private const EXTRA_LANGUAGES = [
        'de' => [
            'name' => 'German',
            'native' => 'Deutsch',
            'dir' => 'ltr',
            'font' => null,
        ],
        'it' => [
            'name' => 'Italian',
            'native' => 'Italiano',
            'dir' => 'ltr',
            'font' => null,
        ],
    ];

    private const COUNTRY_LOCALE_MAP = [
        'PK' => 'ur',
        'SA' => 'ar',
        'AE' => 'ar',
        'EG' => 'ar',
        'JO' => 'ar',
        'KW' => 'ar',
        'QA' => 'ar',
        'OM' => 'ar',
        'BH' => 'ar',
        'IQ' => 'ar',
        'MA' => 'ar',
        'DZ' => 'ar',
        'TN' => 'ar',
        'IN' => 'hi',
        'BD' => 'bn',
        'CN' => 'zh',
        'TW' => 'zh',
        'HK' => 'zh',
        'SG' => 'zh',
        'ES' => 'es',
        'MX' => 'es',
        'AR' => 'es',
        'CO' => 'es',
        'CL' => 'es',
        'PE' => 'es',
        'FR' => 'fr',
        'BE' => 'fr',
        'DE' => 'de',
        'AT' => 'de',
        'CH' => 'de',
        'IT' => 'it',
    ];

    /**
     * Resolve and apply the active locale using manual choice, browser header, country header, or default.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->ensureSupportedLanguagesConfig();

        $supported = array_keys(config('languages.supported', []));
        if (empty($supported)) {
            $supported = self::SUPPORTED_LOCALES;
        }

        $default = config('languages.default', 'en');
        if (!in_array($default, $supported, true)) {
            $default = 'en';
        }

        [$locale, $isManual] = $this->resolveLocale($request, $supported, $default);

        App::setLocale($locale);

        if ($request->hasSession()) {
            if ($isManual) {
                $request->session()->put('locale', $locale);
                $request->session()->put('locale_manual', true);
                $request->session()->forget('locale_detected');
            } elseif (!$request->is('lang/*')) {
                $request->session()->forget(['locale', 'locale_manual']);
                $request->session()->put('locale_detected', $locale);
            }
        }

        $response = $next($request);

        if ($request->is('lang/*') && $response->getStatusCode() < 400 && $request->hasSession()) {
            $switched = $request->session()->get('locale');
            if (is_string($switched) && in_array($switched, $supported, true)) {
                $request->session()->put('locale_manual', true);
                $request->session()->forget('locale_detected');
                App::setLocale($switched);
                $response->withCookie(cookie('locale_choice', $switched, 525600));
            }
        }

        return $response;
    }

    /**
     * Ensure runtime config includes all 10 supported locales in the dropdown order.
     */
    private function ensureSupportedLanguagesConfig(): void
    {
        $configured = config('languages.supported', []);
        foreach (self::EXTRA_LANGUAGES as $code => $meta) {
            if (!isset($configured[$code])) {
                $configured[$code] = $meta;
            }
        }

        config(['languages.supported' => $configured]);
    }

    /**
     * Determine locale by priority: manual choice -> Accept-Language -> country header -> default.
     *
     * @return array{0: string, 1: bool}
     */
    private function resolveLocale(Request $request, array $supported, string $default): array
    {
        if ($request->hasSession()) {
            $manualSession = $request->session()->get('locale_manual');
            if (is_string($manualSession) && in_array($manualSession, $supported, true)) {
                return [$manualSession, true];
            }

            $sessionLocale = $request->session()->get('locale');
            if (is_string($sessionLocale) && in_array($sessionLocale, $supported, true)) {
                return [$sessionLocale, true];
            }
        }

        $cookieLocale = $request->cookie('locale_choice');
        if (is_string($cookieLocale) && in_array($cookieLocale, $supported, true)) {
            return [$cookieLocale, true];
        }

        $preferred = $this->parseAcceptLanguage($request->header('Accept-Language'), $supported);
        if ($preferred !== null) {
            return [$preferred, false];
        }

        foreach (['CF-IPCountry', 'X-Country-Code', 'CloudFront-Viewer-Country', 'X-AppEngine-Country'] as $header) {
            $country = strtoupper(trim((string) $request->header($header, '')));
            if ($country !== '' && isset(self::COUNTRY_LOCALE_MAP[$country])) {
                $mapped = self::COUNTRY_LOCALE_MAP[$country];
                if (in_array($mapped, $supported, true)) {
                    return [$mapped, false];
                }
            }
        }

        return [$default, false];
    }

    /**
     * Parse the Accept-Language header deterministically by q-value descending (stable on ties).
     */
    private function parseAcceptLanguage(?string $header, array $supported): ?string
    {
        $header = trim((string) $header);
        if ($header === '') {
            return null;
        }

        $candidates = [];
        foreach (explode(',', $header) as $index => $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }

            $segments = explode(';', $part);
            $tag = strtolower(trim($segments[0]));
            if ($tag === '' || $tag === '*') {
                continue;
            }

            $q = 1.0;
            foreach (array_slice($segments, 1) as $param) {
                $param = trim($param);
                if (preg_match('/^q\s*=\s*([0-9]+(?:\.[0-9]+)?)$/i', $param, $matches)) {
                    $q = (float) $matches[1];
                    break;
                }
            }

            if ($q <= 0.0) {
                continue;
            }

            $primary = explode('-', str_replace('_', '-', $tag))[0];
            $candidates[] = [
                'primary' => $primary,
                'q' => min($q, 1.0),
                'index' => $index,
            ];
        }

        usort($candidates, static function (array $a, array $b): int {
            if ($a['q'] === $b['q']) {
                return $a['index'] <=> $b['index'];
            }

            return $b['q'] <=> $a['q'];
        });

        foreach ($candidates as $candidate) {
            if (in_array($candidate['primary'], $supported, true)) {
                return $candidate['primary'];
            }
        }

        return null;
    }
}

