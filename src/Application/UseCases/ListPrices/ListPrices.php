<?php

declare(strict_types=1);

namespace Src\Application\UseCases\ListPrices;

use Src\Domain\Entities\Price;
use Src\Domain\Repositories\PriceRepository;

class ListPrices
{
    public function __construct(
        private readonly PriceRepository $priceRepository
    ) {}

    public function execute(ListPricesInput $input): ListPricesOutput
    {
        $prices = $this->priceRepository->findByLayerIdsAndProductIds(
            layerIds: $input->layerIds,
            productIds: $input->productIds,
        );

        return new ListPricesOutput(
            total: count($prices),
            items: array_map(
                callback: fn (Price $price) => new ListPricesOutputItem(
                    id: $price->getId(), 
                    productId: $price->getProductId(), 
                    layerId: $price->getLayerId(),
                    value: $price->value
                ),
                array: $prices
            )
        );
    }
}
