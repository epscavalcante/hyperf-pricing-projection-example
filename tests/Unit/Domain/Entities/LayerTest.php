<?php

use Src\Domain\Entities\Layer;
use Src\Domain\Enums\LayerType;
use Src\Domain\ValueObjects\LayerId;

test('Deve criar uma layer normal', function () {
    $layer = Layer::create(
        code: 'LAYER',
        type: (string) LayerType::NORMAL->value,
        description: 'Descrição de exemplo',
    );

    expect($layer)->toBeInstanceOf(Layer::class);
    expect($layer->code)->toBe('LAYER');
    expect($layer->description)->toBe('Descrição de exemplo');
    expect($layer->isBase())->toBeTrue();
    expect($layer->getParentId())->toBeNull();
});

test('Deve criar uma layer com parent', function () {
    $baseLayer = Layer::create(
        code: 'LAYER',
        type: (string) LayerType::NORMAL->value,
        description: 'Tabela base',
    );

    $otherLayer = Layer::create(
        code: 'OTHER',
        parentId: $baseLayer->getId(),
        type: (string) LayerType::PERCENTAGE_DISCOUNT->value, // na pratica o usecase vai evitar esse tipo de relacionamento, a parent não pode ser NORMAL, inicialmente pode ser somente DISCOUNT
        description: 'Tabela de desconto da tabela base',
    );

    expect($otherLayer)->toBeInstanceOf(Layer::class);
    expect($otherLayer)->isBase()->toBeFalsy();
    expect($otherLayer->getParentId())->toBe($baseLayer->getId());
});

test('Deve falhar ao criar uma layer base com parent', function () {
    Layer::create(
        code: 'LAYER',
        parentId: LayerId::create()->getValue(),
        type: (string) LayerType::NORMAL->value,
        description: 'Tabela base',
    );
})->throws(Exception::class, 'Layers do tipo NORMAL não possui parent');

test('Deve falhar ao criar uma layer base sem o tipo normal', function () {
    Layer::create(
        code: 'LAYER',
        type: (string) LayerType::PERCENTAGE_DISCOUNT->value,
        description: 'Tabela base',
    );
})->throws(Exception::class, 'Layers do tipo !NORMAL devem ter um parent');