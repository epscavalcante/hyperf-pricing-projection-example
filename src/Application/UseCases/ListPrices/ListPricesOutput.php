<?php

declare(strict_types=1);

namespace Src\Application\UseCases\ListPrices;

readonly class ListPricesOutput
{
    /**
     * @param int $total
     * @param ListPricesOutputItem[] $items
     */
    public function __construct(
        public int $total,
        public array $items
    ) {}
}

readonly class ListPricesOutputItem
{
    public function __construct(
        public string $id,
        public string $productId,
        public string $layerId,
        public int $value
        // final value
    ) {}
}

