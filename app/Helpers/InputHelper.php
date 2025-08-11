<?php

namespace App\Helpers;

class InputHelper
{
    public static function filterEmptyValues(array $input): array
    {
        return array_filter($input, fn($value) => !self::isExplicitlyEmpty($value));
    }

    /**
     * Cek apakah nilai dianggap kosong secara eksplisit.
     *
     * @param mixed $value
     * @return bool
     */
    private static function isExplicitlyEmpty($value): bool
    {
        // Jangan filter 0, "0", false, tapi filter null dan ""
        return $value === null || (is_string($value) && trim($value) === '');
    }

}
