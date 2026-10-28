<?php

namespace App\Support;

final class Ubigeo
{
    /** @return array<string, array{provinces: array<string, list<string>>}> */
    public static function data(): array
    {
        return json_decode(file_get_contents(resource_path('data/ubigeo.json')), true, flags: JSON_THROW_ON_ERROR);
    }

    /** @return list<string> */
    public static function departments(): array
    {
        return array_keys(self::data());
    }

    /** @return list<string> */
    public static function provinces(?string $department): array
    {
        return array_keys(self::data()[$department]['provinces'] ?? []);
    }

    /** @return list<string> */
    public static function districts(?string $department, ?string $province): array
    {
        return self::data()[$department]['provinces'][$province] ?? [];
    }

    public static function isValid(?string $department, ?string $province, ?string $district): bool
    {
        return in_array($province, self::provinces($department), true)
            && in_array($district, self::districts($department, $province), true);
    }
}
