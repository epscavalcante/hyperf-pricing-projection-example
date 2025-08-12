<?php

use Src\Domain\Entities\Layer;
use Src\Domain\Entities\Price;
use Src\Domain\Entities\Product;
use Src\Domain\Enums\LayerType;

test('Deve projetar o preço base de um produto', function () {
    $product = Product::create(
        name: 'Produto 1'
    );
    $layer = Layer::create(
        code: 'EXAMPLE',
        type: LayerType::NORMAL->value,
    );
    $price = Price::create(
        layerId: $layer->getId(),
        productId: $product->getId(),
        value: 100
    );
    expect($price)->toBeInstanceOf(Price::class);
    expect($price->getProductId())->toBeString();
    expect($price->getLayerId())->toBeString();
    expect($price->value)->toBe(100);
});
