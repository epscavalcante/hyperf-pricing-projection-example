<?php

declare(strict_types=1);

namespace Src\Domain\Repositories;

use Src\Domain\Entities\Price;

interface PriceRepository
{
    /** @return Price[]*/
    public function findByLayerIdsAndProductIds(array $layerIds, array $productIds): array;

    public function save(Price $price): void;

    public function existsByLayerIdAndProductId(string $layerId, string $productId): bool;

    /** @return Price[]*/
    public function list(array $layerIds = [], array $productIds = []): array;
}
