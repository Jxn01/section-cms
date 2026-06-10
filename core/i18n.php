<?php
// ═══════════════════════════════════════════════════════════════
// Internationalization (i18n)
// ═══════════════════════════════════════════════════════════════
// A tiny, dependency-free translation layer shared by the public
// website and the admin panel.
//
// Page and section *content* is authored in the admin panel and
// stored in the database, so it can be written in any language.
// These dictionaries only cover the fixed UI strings that the
// templates render directly (buttons, labels, messages, etc.).
//
// Dictionaries live in config/lang/<locale>.php and return a flat
// associative array of key => string. English ('en') is the
// fallback: any key missing from another locale falls back to it.
// ═══════════════════════════════════════════════════════════════

class I18n
{
    /** Locales the project ships translations for. */
    public const SUPPORTED = ['en', 'hu'];

    private const DEFAULT = 'en';

    private static string $locale = self::DEFAULT;
    private static array $messages = [];
    private static array $fallback = [];

    /**
     * Load a locale's dictionary (with the English fallback).
     * Safe to call more than once (e.g. to switch language).
     */
    public static function init(string $locale = self::DEFAULT): void
    {
        $locale     = self::normalize($locale);
        $langDir    = dirname(__DIR__) . '/config/lang/';
        self::$fallback = require $langDir . self::DEFAULT . '.php';
        self::$messages = $locale === self::DEFAULT
            ? self::$fallback
            : require $langDir . $locale . '.php';
        self::$locale = $locale;
    }

    /** Coerce an arbitrary value to a supported locale code. */
    public static function normalize(string $locale): string
    {
        $locale = strtolower(substr(trim($locale), 0, 2));
        return in_array($locale, self::SUPPORTED, true) ? $locale : self::DEFAULT;
    }

    /** Current locale code, e.g. "en" or "hu". */
    public static function locale(): string
    {
        return self::$locale;
    }

    /** Value for the <html lang="..."> attribute and hreflang. */
    public static function htmlLang(): string
    {
        return self::$locale;
    }

    /** Value for the og:locale Open Graph meta tag. */
    public static function ogLocale(): string
    {
        return ['en' => 'en_US', 'hu' => 'hu_HU'][self::$locale] ?? 'en_US';
    }

    /**
     * Translate a key. Unknown keys fall back to English, then to
     * the key itself (so missing strings are visible, not blank).
     * Placeholders like {name} are replaced from $vars.
     */
    public static function t(string $key, array $vars = []): string
    {
        $msg = self::$messages[$key] ?? self::$fallback[$key] ?? $key;

        if ($vars) {
            $replacements = [];
            foreach ($vars as $name => $value) {
                $replacements['{' . $name . '}'] = (string) $value;
            }
            $msg = strtr($msg, $replacements);
        }

        return $msg;
    }

    /**
     * Format a date/time string for the current locale.
     *
     * @param string $datetime A value strtotime() understands.
     * @param bool   $withTime Append the time (HH:MM) as well.
     */
    public static function formatDate(string $datetime, bool $withTime = false): string
    {
        $ts = strtotime($datetime);
        if ($ts === false) {
            return $datetime;
        }
        $formats = [
            'en' => $withTime ? 'M j, Y H:i' : 'M j, Y',
            'hu' => $withTime ? 'Y.m.d H:i'  : 'Y. m. d.',
        ];
        $format = $formats[self::$locale] ?? $formats['en'];
        return date($format, $ts);
    }
}

/** Shorthand: return a translated string. */
function t(string $key, array $vars = []): string
{
    return I18n::t($key, $vars);
}

/** Shorthand: echo a translated string, HTML-escaped. */
function te(string $key, array $vars = []): void
{
    echo htmlspecialchars(I18n::t($key, $vars), ENT_QUOTES, 'UTF-8');
}
