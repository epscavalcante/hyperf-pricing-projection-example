<?php

use Src\Application\UseCases\ListPrices\ListPrices;
use Src\Application\UseCases\ListPrices\ListPricesInput;
use Src\Application\UseCases\ListPrices\ListPricesOutput;
use Src\Domain\Entities\Layer;
use Src\Domain\Entities\Price;
use Src\Domain\Entities\Product;
use Src\Domain\Repositories\PriceRepository;

test('Deve retornar uma lista vazia ao buscar os preços', function () {
    $priceRepository = Mockery::mock(PriceRepository::class);

    $priceRepository->shouldReceive('findByLayerIdsAndProductIds')
        ->with([], [])
        ->andReturn([]);
    $listPrices = new ListPrices(
        priceRepository: $priceRepository
    );

    $input = new ListPricesInput(
        layerIds: [],
        productIds: [],
    );
    $output = $listPrices->execute($input);
    expect($output)->toBeInstanceOf(ListPricesOutput::class);
    expect($output->total)->toBe(0);
    expect($output->items)->toHaveLength(0);
});

test('Deve retornar uma lista com items ao buscar os preços', function () {
    $priceRepository = Mockery::mock(PriceRepository::class);

    $product1 = Product::create(
        name: 'Produto 1',
    );

    $product2 = Product::create(
        name: 'Produto 2',
    );

    $product3 = Product::create(
        name: 'Produto 3',
    );

    $layer1 = Layer::createSimpleLayer(
        code: 'layer_id',
    );

    $layer2 = Layer::createSimpleLayer(
        code: 'layer_2',
    );

    // p1 l1 -> 300
    $price1 = Price::create(
        layerId: $layer1->getId(),
        productId: $product1->getId(),
        value: 300
    );
    // p2 l1 -> 250
    $price2 = Price::create(
        layerId: $layer1->getId(),
        productId: $product2->getId(),
        value: 250
    );
    // p3 l1 -> 500
    $price3 = Price::create(
        layerId: $layer1->getId(),
        productId: $product3->getId(),
        value: 500
    );
    // p1 l2 -> 600
    $price4 = Price::create(
        layerId: $layer2->getId(),
        productId: $product1->getId(),
        value: 600
    );
    // p2 l2 -> 650
    $price5 = Price::create(
        layerId: $layer2->getId(),
        productId: $product2->getId(),
        value: 650
    );
    // p3 l2 -> 920
    $price6 = Price::create(
        layerId: $layer2->getId(),
        productId: $product3->getId(),
        value: 920
    );

    $priceRepository->shouldReceive('findByLayerIdsAndProductIds')
        ->with(
            [$layer1->getId(), $layer2->getId()],
            [$product1->getId(), $product2->getId(), $product3->getId(),]
        )
        ->andReturn([
            $price1,
            $price2,
            $price3,
            $price4,
            $price5,
            $price6,
        ]);
    $listPrices = new ListPrices(
        priceRepository: $priceRepository
    );

    $input = new ListPricesInput(
        layerIds: [$layer1->getId(), $layer2->getId()],
        productIds: [$product1->getId(), $product2->getId(), $product3->getId()],
    );
    $output = $listPrices->execute($input);
    expect($output)->toBeInstanceOf(ListPricesOutput::class);
    expect($output->total)->toBe(6);
    expect($output->items)->toHaveLength(6);
});
