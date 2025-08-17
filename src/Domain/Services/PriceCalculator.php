<?php

declare(strict_types=1);

namespace Src\Domain\Services;

use Exception;
use Src\Domain\Entities\Layer;
use Src\Domain\Enums\LayerType;

class PriceCalculator
{
    public static function calculate(int $price, Layer $layer)
    {
        if ($layer->isBase())
            return $price;

        if ($layer->isPercentualDiscountType()) {
            $discount = $price * ($layer->value / 100);
            return intval($price - $discount);
        }

        throw new Exception('Unsupported price calculator');
    }
}
