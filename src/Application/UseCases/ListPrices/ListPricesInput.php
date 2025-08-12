<?php

declare(strict_types=1);

namespace Src\Application\UseCases\ListPrices;

readonly class ListPricesInput
{
    public function __construct(
        public array $layerIds,
        public array $productIds
    ) {}
}
