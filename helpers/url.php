<?php

declare(strict_types=1);

function base_url(): string
{
    $config = $GLOBALS['APP_CONFIG'] ?? [];
    $baseUrl = (string)($config['base_url'] ?? '');
    $baseUrl = rtrim($baseUrl, '/');
    return $baseUrl === '' ? '' : $baseUrl;
}

function url(string $path = ''): string
{
    $path = '/' . ltrim($path, '/');
    return base_url() . ($path === '/' ? '/' : $path);
}
