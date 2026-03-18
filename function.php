<?php
/**
 * functions.php — Helper functions for Serendib Explorer
 * Reusable utilities for input validation, sanitization, and responses.
 */

/**
 * Sanitize a plain text input field.
 * Removes extra whitespace and strips HTML tags.
 */
function sanitize_text(string $value): string {
    return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate an email address.
 * Returns true if valid, false otherwise.
 */
function validate_email(string $email): bool {
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validate password strength.
 * Must be at least 6 characters.
 */
function validate_password(string $password): bool {
    return strlen($password) >= 6;
}

/**
 * Send a JSON response and exit.
 * 
 * @param bool   $success  Whether the operation succeeded.
 * @param string $message  Message to return to the client.
 * @param array  $extra    Any additional data to include in the response.
 */
function json_response(bool $success, string $message, array $extra = []): void {
    echo json_encode(array_merge(
        ["success" => $success, "message" => $message],
        $extra
    ));
    exit;
}

/**
 * Check if the current PHP session has a logged-in user.
 * Returns the user array from $_SESSION, or null if not logged in.
 */
function get_logged_in_user(): ?array {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return $_SESSION['user'] ?? null;
}

/**
 * Require the user to be logged in.
 * If not, sends a 401 JSON response and exits.
 * Use this at the top of any protected API endpoint.
 */
function require_auth(): array {
    $user = get_logged_in_user();
    if (!$user) {
        http_response_code(401);
        json_response(false, "You must be logged in to perform this action.");
    }
    return $user;
}
?>