<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function redirect(string $path): void
{
    $location = $path;
    if (preg_match('#^https?://#', $path) === 1) {
        $location = $path;
    } else {
        $location = url($path);
    }

    header('Location: ' . $location);
    exit;
}

function is_logged_in(): bool
{
    return isset($_SESSION['user']);
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_login(): void
{
    if (!is_logged_in()) {
        redirect('/login.php');
    }
}

function require_role(string $role): void
{
    require_login();
    $user = current_user();
    if (!$user || ($user['role'] ?? '') !== $role) {
        redirect('/index.php');
    }
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
