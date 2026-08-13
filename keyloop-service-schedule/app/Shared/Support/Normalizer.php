<?php

namespace App\Shared\Support;

final class Normalizer
{
    public static function email(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : mb_strtolower($value);
    }

    public static function phone(?string $value): ?string
    {
        $value = preg_replace('/\D+/', '', (string) $value) ?? '';

        return $value === '' ? null : $value;
    }

    public static function registration(string $value): string
    {
        return strtoupper(preg_replace('/[^a-zA-Z0-9]+/', '', $value) ?? '');
    }
}
