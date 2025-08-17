<?php

declare(strict_types=1);

namespace Src\Domain\Enums;

enum LayerType: string
{
    case NORMAL = 'NORMAL';
    case PERCENTAGE_DISCOUNT = 'PERCENTAGE_DISCOUNT';
    //case NOMINAL_DISCOUNT = 'NOMINAL_DISCOUNT';

    public static function isNormal(string $type): bool
    {
        return self::NORMAL->value === $type;
    }
}