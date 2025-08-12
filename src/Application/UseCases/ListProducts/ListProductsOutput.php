<?php

declare(strict_types=1);

namespace Src\Application\UseCases\ListProducts;

use Src\Domain\Entities\Product;

readonly class ListProductsOutput
{
    public int $total;

    public array $items;

    /**
     * @param int $total
     * @param ListProductsOutputItem[] $items
     */
    public function __construct(int $total, array $items) {
        $this->total= $total;
        $this->items = array_map(
            callback: fn (Product $item) => new ListProductsOutputItem(
                id: $item->getId(),
                name: $item->name,
            ),
            array: $items
        );
    }
}

readonly class ListProductsOutputItem
{
    public function __construct(
        public string $id,
        public string $name,
    ) {}
}
