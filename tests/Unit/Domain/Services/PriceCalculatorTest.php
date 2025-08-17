<?php

use Src\Domain\Entities\Layer;
use Src\Domain\Enums\LayerType;
use Src\Domain\Services\PriceCalculator;
use Src\Domain\ValueObjects\LayerId;

test('Deve cacular o preço de uma layer normal', function (int $initialValuePrice, int $expectedFinalValue) {
    $normalLayer = Layer::create(
        code: 'NORMAL',
        type: LayerType::NORMAL->value,
    );

    $finalPrice = PriceCalculator::calculate($initialValuePrice, $normalLayer);

    expect($finalPrice)->toBe($expectedFinalValue);
})->with([
    [2490, 2490]
]);

test('Deve cacular o preço de uma layer com desconto', function (
    int $initialValuePrice, 
    int $percentualValue,
    int $expectedFinalValue
) {
    $normalLayer = Layer::createPercentualDiscount(
        parentId: LayerId::create()->getValue(),
        code: 'PROMO',
        percentualDiscount: $percentualValue 
    );

    $finalPrice = PriceCalculator::calculate($initialValuePrice, $normalLayer);

    expect($finalPrice)->toBe($expectedFinalValue);
})->with([
    [1000, 23, 770],
    [2490, 27, 1817],
    [3333, 33, 2233]
])->only();
