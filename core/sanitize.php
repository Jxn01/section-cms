<?php
// ─── HTML Sanitizer ───
// Lightweight whitelist-based HTML sanitizer for WYSIWYG content.
// Strips everything except safe HTML tags and attributes.

function sanitizeHtml(string $html): string {
    if (trim($html) === '') return '';

    // Allowed tags and their allowed attributes
    $allowed = [
        'p'          => ['style'],
        'br'         => [],
        'strong'     => [],
        'b'          => [],
        'em'         => [],
        'i'          => [],
        'u'          => [],
        's'          => [],
        'a'          => ['href', 'target', 'rel', 'title'],
        'ul'         => [],
        'ol'         => [],
        'li'         => [],
        'h1'         => [],
        'h2'         => [],
        'h3'         => [],
        'h4'         => [],
        'h5'         => [],
        'h6'         => [],
        'blockquote' => [],
        'pre'        => [],
        'code'       => [],
        'img'        => ['src', 'alt', 'width', 'height', 'loading'],
        'table'      => [],
        'thead'      => [],
        'tbody'      => [],
        'tr'         => [],
        'th'         => ['colspan', 'rowspan'],
        'td'         => ['colspan', 'rowspan'],
        'span'       => ['style'],
        'div'        => ['style'],
        'sub'        => [],
        'sup'        => [],
        'hr'         => [],
        'figure'     => [],
        'figcaption' => [],
    ];

    // Build strip_tags allowlist
    $tagList = '<' . implode('><', array_keys($allowed)) . '>';
    $html = strip_tags($html, $tagList);

    // Sanitize attributes — remove anything not in the whitelist
    // Also strip javascript: URLs and event handlers
    $html = preg_replace_callback(
        '/<([a-z][a-z0-9]*)\b([^>]*)>/si',
        function ($match) use ($allowed) {
            $tag = strtolower($match[1]);
            $attrs = $match[2];

            if (!isset($allowed[$tag])) {
                return ''; // Tag not allowed
            }

            $allowedAttrs = $allowed[$tag];
            if (empty($allowedAttrs)) {
                // Self-closing tags
                if (in_array($tag, ['br', 'hr', 'img'])) {
                    return '<' . $tag . '>';
                }
                return '<' . $tag . '>';
            }

            // Parse and filter attributes
            $cleanAttrs = '';
            preg_match_all('/([a-z\-]+)\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|(\S+))/si', $attrs, $attrMatches, PREG_SET_ORDER);
            foreach ($attrMatches as $attr) {
                $attrName = strtolower($attr[1]);
                $attrValue = $attr[2] ?? $attr[3] ?? $attr[4] ?? '';

                // Only allow whitelisted attributes
                if (!in_array($attrName, $allowedAttrs, true)) continue;

                // Block javascript: URLs
                if (in_array($attrName, ['href', 'src'])) {
                    $testVal = strtolower(trim(preg_replace('/\s+/', '', $attrValue)));
                    if (preg_match('/^(javascript|data|vbscript):/i', $testVal)) continue;
                }

                // Block dangerous CSS in style
                if ($attrName === 'style') {
                    $attrValue = preg_replace('/expression\s*\(|javascript\s*:|url\s*\(/i', '', $attrValue);
                }

                $cleanAttrs .= ' ' . $attrName . '="' . htmlspecialchars($attrValue, ENT_QUOTES, 'UTF-8') . '"';
            }

            return '<' . $tag . $cleanAttrs . '>';
        },
        $html
    );

    // Remove any on* event handler attributes that might have slipped through
    $html = preg_replace('/\s+on\w+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);

    return $html;
}
