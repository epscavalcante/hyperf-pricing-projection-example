<?php

declare(strict_types=1);

namespace Src\Application\UseCases\ListLayers;

use Src\Domain\Entities\Layer;

readonly class ListLayersOutput
{
    public int $total;

    public array $items;

    /**
     * @param int $total
     * @param ListLayersOutputItem[] $items
     */
    public function __construct(int $total, array $items) {
        $this->total= $total;
        $this->items = array_map(
            callback: fn (Layer $item) => new ListLayersOutputItem(
                id: $item->getId(),
                code: $item->code,
                type: $item->getType(),
                value: 0,
                description: $item->description,
            ),
            array: $items
        );
    }
}

readonly class ListLayersOutputItem
{
    public function __construct(
        public string $id,
        public string $code,
        public string $type,
        public int $value,
        public ?string $description,
        // final value
    ) {}
}
