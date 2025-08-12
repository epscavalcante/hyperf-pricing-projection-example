<?php

declare(strict_types=1);

namespace Src\Infrastructure\Repositories;

use Hyperf\DbConnection\Db as DB;
use Src\Domain\Entities\Price;
use Src\Domain\Repositories\PriceRepository;

class PriceQueryBuilderRepository implements PriceRepository
{
    /** @return Price[]*/
    public function findByLayerIdsAndProductIds(array $layerIds, array $productIds): array
    {
        return $this->list(
            layerIds: $layerIds,
            productIds: $productIds
        );
    }

    public function save(Price $price): void
    {
        DB::table('prices')
            ->insert([
                'id' => $price->getId(),
                'product_id' => $price->getProductId(),
                'layer_id' => $price->getLayerId(),
                'value_cents' => $price->value,
            ]);
    }

    public function existsByLayerIdAndProductId(string $layerId, string $productId): bool
    {
        $prices = $this->list(
            layerIds: [$layerId],
            productIds: [$productId]
        );

        return count($prices) > 0;
    }

    /** @return Price[]*/
    public function list(array $layerIds = [], array $productIds = []): array
    {
        $pricesQuery = DB::table('prices');

        if (count($layerIds) > 0) {
            $pricesQuery->whereIn('layer_id', $layerIds);
        }

        if (count($productIds) > 0) {
            $pricesQuery->whereIn('product_id', $productIds);
        }

        $prices = $pricesQuery->get();

        return array_map(
            callback: fn($priceDb) => Price::restore(
                id: $priceDb->id,
                layerId: $priceDb->layer_id,
                productId: $priceDb->product_id,
                value: $priceDb->value_cents
            ),
            array: $prices->all(),
        );
    }
}
