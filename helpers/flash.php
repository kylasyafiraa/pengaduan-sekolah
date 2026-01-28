<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function flash_set(string $key, string $message, string $type = 'info'): void
{
    $_SESSION['flash'][$key] = ['message' => $message, 'type' => $type];
}

function flash_get(string $key): ?array
{
    if (!isset($_SESSION['flash'][$key])) {
        return null;
    }

    $value = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);
    return is_array($value) ? $value : null;
}
