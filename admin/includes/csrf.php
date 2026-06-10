<?php
// ─── CSRF Protection ───
// Generates and validates CSRF tokens for admin forms.

/**
 * Get or generate the CSRF token for the current session.
 */
function csrfToken(): string {
    if (empty($_SESSION['admin_csrf_token'])) {
        $_SESSION['admin_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['admin_csrf_token'];
}

/**
 * Output a hidden CSRF input field for forms.
 */
function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Verify the CSRF token from a POST request.
 * Terminates with 403 if invalid.
 */
function csrfVerify(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals(csrfToken(), $token)) {
        http_response_code(403);
        die('Érvénytelen biztonsági token. Kérjük frissítse az oldalt és próbálja újra.');
    }
}
